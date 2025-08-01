<?php
declare(strict_types=1);

namespace Fyre\Server;

use DateTimeInterface;
use Fyre\DateTime\DateTime;
use Fyre\Http\Cookie;
use Fyre\Http\Response;
use SimpleXMLElement;

use function array_key_exists;
use function gmdate;
use function header;
use function http_response_code;
use function is_numeric;
use function is_string;
use function json_encode;
use function strtotime;

use const JSON_PRETTY_PRINT;

/**
 * ClientResponse
 */
class ClientResponse extends Response
{
    protected const HEADER_FORMAT = 'D, d-M-Y H:i:s e';

    protected array $cookies = [];

    /**
     * New ClientResponse constructor.
     *
     * @param array $options The response options.
     */
    public function __construct(array $options = [])
    {
        $options['headers'] ??= [];
        $options['headers']['Content-Type'] ??= 'text/html; charset=UTF-8';

        parent::__construct($options);
    }

    /**
     * Delete a cookie.
     *
     * @param string $name The cookie name.
     * @param array $options The cookie options.
     * @return ClientResponse A new ClientResponse.
     */
    public function deleteCookie(string $name, array $options = []): static
    {
        $options['expires'] = 1;

        $cookie = new Cookie($name, '', $options);

        $temp = clone $this;

        $temp->cookies[static::getCookieKey($cookie)] = $cookie;

        return $temp;
    }

    /**
     * Get a cookie.
     *
     * @param string $name The cookie name.
     * @return Cookie|null The Cookie.
     */
    public function getCookie(string $name): Cookie|null
    {
        foreach ($this->cookies as $cookie) {
            if ($cookie->getName() !== $name) {
                continue;
            }

            return $cookie;
        }

        return null;
    }

    /**
     * Determine whether a cookie has been set.
     *
     * @param string $name The cookie name.
     * @return bool TRUE if the cookie exists, otherwise FALSE.
     */
    public function hasCookie(string $name): bool
    {
        return $this->getCookie($name) !== null;
    }

    /**
     * Set headers to prevent browser caching.
     *
     * @return ClientResponse A new ClientResponse.
     */
    public function noCache(): static
    {
        return $this->setHeader('Cache-Control', ['no-store', 'max-age=0', 'no-cache']);
    }

    /**
     * Send the response to the client.
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        header('HTTP/'.$this->protocolVersion.' '.$this->statusCode.' '.$this->getReason());

        foreach ($this->headers as $header) {
            header((string) $header);
        }

        foreach ($this->cookies as $cookie) {
            header($cookie->getHeaderString(), false);
        }

        if ($this->body) {
            echo $this->body;
        }
    }

    /**
     * Set the content type header
     *
     * @param string $mimeType The MIME type.
     * @param string $charset The character set.
     * @return ClientResponse A new ClientResponse.
     */
    public function setContentType(string $mimeType, string $charset = 'UTF-8'): static
    {
        return $this->setHeader('Content-Type', $mimeType.'; charset='.$charset);
    }

    /**
     * Set a cookie value.
     *
     * @param string $name The cookie name.
     * @param string $value The cookie value.
     * @param array $options The cookie options.
     * @return ClientResponse A new ClientResponse.
     */
    public function setCookie(string $name, string $value, array $options = []): static
    {
        if (array_key_exists('expires', $options) && $options['expires'] instanceof DateTime) {
            $options['expires'] = $options['expires']->getTimestamp();
        }

        $cookie = new Cookie($name, $value, $options);

        $temp = clone $this;

        $temp->cookies[static::getCookieKey($cookie)] = $cookie;

        return $temp;
    }

    /**
     * Set the date header.
     *
     * @param DateTime|DateTimeInterface|int|string $date The date.
     * @return ClientResponse A new ClientResponse.
     */
    public function setDate(DateTime|DateTimeInterface|int|string $date): static
    {
        $utcString = static::formatDateUTC($date);

        return $this->setHeader('Date', $utcString);
    }

    /**
     * Set a JSON response.
     *
     * @param mixed $data The data to send.
     * @return ClientResponse A new ClientResponse.
     */
    public function setJson(mixed $data): static
    {
        $data = json_encode($data, JSON_PRETTY_PRINT);

        return $this
            ->setContentType('application/json')
            ->setBody($data);
    }

    /**
     * Set the last modified date header.
     *
     * @param DateTime|DateTimeInterface|int|string $date The date.
     * @return ClientResponse A new ClientResponse.
     */
    public function setLastModified(DateTime|DateTimeInterface|int|string $date): static
    {
        $utcString = static::formatDateUTC($date);

        return $this->setHeader('Last-Modified', $utcString);
    }

    /**
     * Set an XML response.
     *
     * @param SimpleXMLElement $data The data to send.
     * @return ClientResponse A new ClientResponse.
     */
    public function setXml(SimpleXMLElement $data): static
    {
        $data = $data->asXML();

        return $this
            ->setContentType('application/xml')
            ->setBody($data);
    }

    /**
     * Format a UTC date.
     *
     * @param DateTime|DateTimeInterface|int|string $date The date to format.
     * @return string The formatted UTC date.
     */
    protected static function formatDateUTC(DateTime|DateTimeInterface|int|string $date): string
    {
        if (is_numeric($date)) {
            $timestamp = (int) $date;
        } else if (is_string($date)) {
            $timestamp = strtotime($date);
        } else {
            $timestamp = $date->getTimestamp();
        }

        return gmdate(static::HEADER_FORMAT, $timestamp);
    }

    /**
     * Get the key for a cookie.
     *
     * @param Cookie $cookie The Cookie.
     * @return string The Cookie key.
     */
    protected static function getCookieKey(Cookie $cookie): string
    {
        return implode(',', [$cookie->getName(), $cookie->getDomain(), $cookie->getPath()]);
    }
}
