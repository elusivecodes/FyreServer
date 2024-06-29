<?php
declare(strict_types=1);

namespace Fyre\Server;

use Fyre\Http\Uri;
use Fyre\Server\Exceptions\ServerException;

use function array_key_exists;

/**
 * RedirectResponse
 */
class RedirectResponse extends ClientResponse
{
    /**
     * New RedirectResponse constructor.
     *
     * @param string|Uri $uri The URI to redirect to.
     * @param int $code The header status code.
     * @param array $options The response options.
     */
    public function __construct(string|Uri $uri, int $code = 302, array $options = [])
    {
        if (array_key_exists('REQUEST_METHOD', $_SERVER) && $this->protocolVersion >= 1.1) {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                $code = 303;
            } else if ($code === 302) {
                $code = 307;
            }
        }

        $options['headers'] ??= [];
        $options['headers']['Location'] = (string) $uri;
        $options['statusCode'] = $code;

        parent::__construct($options);
    }

    /**
     * Set the message body.
     *
     * @param string $data The message body.
     *
     * @throws ServerException as body cannot be set for a RedirectResponse.
     */
    public function setBody(string $data): static
    {
        throw ServerException::forUnsupportedSetBody();
    }
}
