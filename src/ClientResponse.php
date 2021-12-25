<?php
declare(strict_types=1);

namespace Fyre\Server;

use 
    Fyre\Cookie\Cookie,
    Fyre\Cookie\CookieStore,
    Fyre\DateTime\DateTimeInterface,
    Fyre\Http\Response,
    SimpleXMLElement;

use const
    JSON_PRETTY_PRINT;

use function
    array_key_exists,
    gmdate,
    header,
    http_response_code,
    is_numeric,
    is_string,
    json_encode,
    strtotime,
    time;

/**
 * ClientResponse
 */
class ClientResponse extends Response
{

    protected const HEADER_FORMAT = 'D, d-M-Y H:i:s e';

    /**
     * New ClientResponse constructor.
     */
    public function __construct()
    {
        $this->noCache();
        $this->setContentType('text/html');
    }

    /**
     * Delete a cookie.
     * @param string $name The cookie name.
     * @param array $options The cookie options.
     * @return ClientResponse The ClientResponse.
     */
    public function deleteCookie(string $name, array $options = []): self
    {
        CookieStore::delete($name, $options);

        return $this;
    }

    /**
     * Get a cookie.
     * @param string $name The cookie name.
     * @return Cookie|null The Cookie.
     */
    public function getCookie(string $name): Cookie|null
    {
        return CookieStore::get($name);
    }

    /**
     * Determine if a cookie has been set.
     * @param string $name The cookie name.
     * @return bool TRUE if the cookie exists, otherwise FALSE.
     */
    public function hasCookie(string $name): bool
    {
        return CookieStore::has($name);
    }

    /**
     * Set headers to prevent browser caching.
     * @return ClientResponse The ClientResponse.
     */
    public function noCache(): self
    {
        $this->setHeader('Cache-Control', ['no-store', 'max-age=0', 'no-cache']);

        return $this;
    }

    /**
     * Set a redirect response.
     * @param string $uri The URI to redirect to.
     * @param int $code The header status code.
     * @return ClientResponse The ClientResponse.
     */
    public function redirect(string $uri, int $code = 302): self
    {
        if (array_key_exists('REQUEST_METHOD', $_SERVER) && $this->getProtocolVersion() >= 1.1) {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                $code = 303;
            } else if ($code === 302) {
                $code = 307;
            }
        }

        $this->setStatusCode($code);
        $this->setHeader('Location', $uri);

        return $this;
    }

    /**
     * Send the response to the client.
     * @return ClientResponse The ClientResponse.
     */
    public function send(): void
    {
        $protocol = $this->getProtocolVersion();
        $code = $this->getStatusCode();
        $reason = $this->getReason();

        http_response_code($code);
        header('HTTP/'.$protocol.' '.$code.' '.$reason);

        foreach ($this->headers AS $header) {
            header($header->__toString(), false, $code);
        }

        CookieStore::dispatch();

        echo $this->body;
    }

    /**
     * Set the content type header
     * @param string $mimeType The MIME type.
     * @param string $charset The character set.
     * @return ClientResponse The ClientResponse.
     */
    public function setContentType(string $mimeType, string $charset = 'UTF-8'): self
    {
        $this->setHeader('Content-Type', $mimeType.'; charset='.$charset);

        return $this;
    }

    /**
     * Set a cookie value.
     * @param string $name The cookie name.
     * @param string $value The cookie value.
     * @param array $options The cookie options.
     * @return ClientResponse The ClientResponse.
     */
    public function setCookie(string $name, string $value, array $options = []): self
    {
        if (array_key_exists('expires', $options)) {
            $options['expires'] += time();
        }

        CookieStore::set($name, $value, $options);

        return $this;
    }

    /**
     * Set the date header.
     * @param DateTimeInterface|\DateTimeInterface|string|int $date The date.
     * @return ClientResponse The ClientResponse.
     */
    public function setDate(DateTimeInterface|\DateTimeInterface|string|int $date): self
    {
        $utcString = static::formatDateUTC($date);

        $this->setHeader('Date', $utcString);

        return $this;
    }

    /**
     * Set a JSON response.
     * @param mixed $data The data to send.
     * @return ClientResponse The ClientResponse.
     */
    public function setJson($data): self
    {
        $this->setContentType('application/json');

        $this->body = json_encode($data, JSON_PRETTY_PRINT);

        return $this;
    }

    /**
     * Set the last modified date header.
     * @param DateTimeInterface|\DateTimeInterface|string|int $date The date.
     * @return ClientResponse The ClientResponse.
     */
    public function setLastModified(DateTimeInterface|\DateTimeInterface|string|int $date): self
    {
        $utcString = static::formatDateUTC($date);

        $this->setHeader('Last-Modified', $utcString);

        return $this;
    }

    /**
     * Set an XML response.
     * @param SimpleXMLElement $data The data to send.
     * @return ClientResponse The ClientResponse.
     */
    public function setXml(SimpleXMLElement $data): self
    {
        $this->setContentType('application/xml');

        $this->body = $data->asXML();

        return $this;
    }

    /**
     * Format a UTC date.
     * @param DateTimeInterface|\DateTimeInterface|string|int $date The date to format.
     * @return string The formatted UTC date.
     */
    protected static function formatDateUTC(DateTimeInterface|\DateTimeInterface|string|int $date): string
    {
        if (is_numeric($date)) {
            $timestamp = $date;
        } else if (is_string($date)) {
            $timestamp = strtotime($date);
        } else {
            $timestamp = $date->getTimestamp();
        }

        return gmdate(static::HEADER_FORMAT, $timestamp);
    }

}
