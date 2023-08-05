<?php
declare(strict_types=1);

namespace Fyre\Server;

use DateTimeInterface;
use Fyre\DateTime\DateTime;
use Fyre\Http\Cookie;
use Fyre\Http\Response;
use SimpleXMLElement;

use const JSON_PRETTY_PRINT;

use function array_key_exists;
use function gmdate;
use function header;
use function http_response_code;
use function is_numeric;
use function is_string;
use function json_encode;
use function strtotime;
use function time;

/**
 * ClientResponse
 */
class ClientResponse extends Response
{

    protected const HEADER_FORMAT = 'D, d-M-Y H:i:s e';

    protected array $cookies = [];

    /**
     * New ClientResponse constructor.
     * @param array $options The response options.
     */
    public function __construct(array $options = [])
    {
        $options['headers'] ??= [];
        $options['headers']['Content-Type'] ??= 'text/html; charset=UTF-8';
        $options['headers']['Cache-Control'] ??= ['no-store', 'max-age=0', 'no-cache'];

        parent::__construct($options);
    }

    /**
     * Delete a cookie.
     * @param string $name The cookie name.
     * @param array $options The cookie options.
     * @return ClientResponse A new ClientResponse.
     */
    public function deleteCookie(string $name, array $options = []): static
    {
        $options['expires'] ??= 1;

        $cookie = new Cookie($name, '', $options);

        $temp = clone $this;

        $temp->cookies[$cookie->getId()] = $cookie;

        return $temp;
    }

    /**
     * Get a cookie.
     * @param string $name The cookie name.
     * @return Cookie|null The Cookie.
     */
    public function getCookie(string $name): Cookie|null
    {
        foreach ($this->cookies AS $cookie) {
            if ($cookie->getName() !== $name) {
                continue;
            }

            return $cookie;
        }

        return null;
    }

    /**
     * Determine if a cookie has been set.
     * @param string $name The cookie name.
     * @return bool TRUE if the cookie exists, otherwise FALSE.
     */
    public function hasCookie(string $name): bool
    {
        return $this->getCookie($name) !== null;
    }

    /**
     * Set headers to prevent browser caching.
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

        foreach ($this->headers AS $header) {
            header((string) $header, false, $this->statusCode);
        }

        foreach ($this->cookies AS $cookie) {
            $cookie->dispatch();
        }

        if ($this->body) {
            echo $this->body;
        }
    }

    /**
     * Set the content type header
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
     * @param string $name The cookie name.
     * @param string $value The cookie value.
     * @param array $options The cookie options.
     * @return ClientResponse A new ClientResponse.
     */
    public function setCookie(string $name, string $value, array $options = []): static
    {
        if (array_key_exists('expires', $options)) {
            $options['expires'] += time();
        }

        $cookie = new Cookie($name, $value, $options);

        $temp = clone $this;

        $temp->cookies[$cookie->getId()] = $cookie;

        return $temp;
    }

    /**
     * Set the date header.
     * @param DateTime|DateTimeInterface|string|int $date The date.
     * @return ClientResponse A new ClientResponse.
     */
    public function setDate(DateTime|DateTimeInterface|string|int $date): static
    {
        $utcString = static::formatDateUTC($date);

        return $this->setHeader('Date', $utcString);
    }

    /**
     * Set a JSON response.
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
     * @param DateTime|DateTimeInterface|string|int $date The date.
     * @return ClientResponse A new ClientResponse.
     */
    public function setLastModified(DateTime|DateTimeInterface|string|int $date): static
    {
        $utcString = static::formatDateUTC($date);

        return $this->setHeader('Last-Modified', $utcString);
    }

    /**
     * Set an XML response.
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
     * @param DateTime|DateTimeInterface|string|int $date The date to format.
     * @return string The formatted UTC date.
     */
    protected static function formatDateUTC(DateTime|DateTimeInterface|string|int $date): string
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

}
