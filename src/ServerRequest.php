<?php
declare(strict_types=1);

namespace Fyre\Server;

use
    Fyre\Http\Negotiate,
    Fyre\Http\Request,
    Fyre\Http\UserAgent,
    Fyre\Server\Exceptions\ServerException,
    Fyre\Server\UploadedFile,
    Fyre\Uri\Uri,
    Locale,
    RecursiveArrayIterator,
    RecursiveIteratorIterator;

use const
    FILTER_DEFAULT,
    PHP_SAPI,
    PHP_URL_PATH;

use function
    array_is_list,
    array_key_exists,
    array_map,
    array_splice,
    count,
    explode,
    file_get_contents,
    filter_var,
    getenv,
    in_array,
    is_array,
    is_string,
    parse_url,
    str_replace,
    strpos,
    strtolower,
    substr,
    ucwords;

/**
 * ServerRequest
 */
class ServerRequest extends Request
{

    protected UserAgent $userAgent;

    protected string $defaultLocale;
    protected string|null $locale = null;
    protected array $supportedLocales = [];

    protected array $globals = [];

    /**
     * New ServerRequest constructor.
     * @param array $config Options for the request.
     */
    public function __construct(array $config = [])
    {
        parent::__construct();

        $this->userAgent = new UserAgent();

        $this->uri->parseUri($config['baseUri'] ?? '');

        $this->body = $config['body'] ?? file_get_contents('php://input');

        $this->defaultLocale = $config['defaultLocale'] ??  Locale::getDefault();
        $this->supportedLocales = $config['supportedLocales'] ?? [];

        $this->loadGlobals('server');
    }

    /**
     * Get a value from the $_COOKIE array.
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param int|array $options Options or flags to use when filtering.
     */
    public function getCookie(string|null $key = null, int $filter = FILTER_DEFAULT, int|array $options = 0)
    {
        return $this->fetchGlobal('cookie', $key, $filter, $options);
    }

    /**
     * Get the default locale.
     * @return string The default locale.
     */
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * Get a value from the $_ENV array.
     * @param string $key The key.
     * @param int $filter The filter to apply.
     * @param int|array $options Options or flags to use when filtering.
     */
    public function getEnv(string $key, int $filter = FILTER_DEFAULT, int|array $options = 0)
    {
        $value = getenv($key, false);

        if ($value === false) {
            return null;
        }

        return static::filterVar($value, $filter, $options);
    }

    /**
     * Get an UploadedFile or array of files from the $_FILE array.
     * @param string|null $key The key.
     */
    public function getFile(string|null $key = null)
    {
        return $this->fetchGlobal('file', $key);
    }

    /**
     * Get a value from the $_GET array.
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param int|array $options Options or flags to use when filtering.
     */
    public function getGet(string|null $key = null, int $filter = FILTER_DEFAULT, int|array $options = 0)
    {
        return $this->fetchGlobal('get', $key, $filter, $options);
    }

    /**
     * Get the current locale.
     * @return string The current locale.
     */
    public function getLocale(): string
    {
        return $this->locale ?? $this->defaultLocale;
    }

    /**
     * Get a value from the $_POST array.
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param int|array $options Options or flags to use when filtering.
     */
    public function getPost(string|null $key = null, int $filter = FILTER_DEFAULT, int|array $options = 0)
    {
        return $this->fetchGlobal('post', $key, $filter, $options);
    }

    /**
     * Get a value from the $_SERVER array.
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param int|array $options Options or flags to use when filtering.
     */
    public function getServer(string|null $key = null, int $filter = FILTER_DEFAULT, int|array $options = 0)
    {
        return $this->fetchGlobal('server', $key, $filter, $options);
    }

    /**
     * Get the user agent.
     * @return UserAgent The user agent.
     */
    public function getUserAgent(): UserAgent
    {
        return $this->userAgent;
    }

    /**
     * Determine if the request was made using AJAX.
     * @return bool TRUE if the request was made using AJAX, otherwise FALSE.
     */
    public function isAjax(): bool
    {
        $xRequestedWith = $this->getHeaderValue('X-Requested-With');

        return $xRequestedWith && strtolower($xRequestedWith) === 'xmlhttprequest';
    }

    /**
     * Determine if the request was made from the CLI.
     * @return bool TRUE if the request was made from the CLI, otherwise FALSE.
     */
    public function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Determine if the request is using HTTPS.
     * @return bool TRUE if the request is using HTTPS, otherwise FALSE.
     */
    public function isSecure(): bool
    {
        $https = $this->getServer('HTTPS');

        if ($https && strtolower($https) !== 'off') {
            return true;
        }

        $xForwardedProto = $this->getHeaderValue('X-Forwarded-Proto');

        if (strtolower($xForwardedProto) === 'https') {
            return true;
        }

        $frontEndHttps = $this->getHeaderValue('Front-End-Https');

        return $frontEndHttps && strtolower($frontEndHttps) !== 'off';
    }

    /**
     * Negotiate a value from HTTP headers.
     * @param string $type The negotiation type.
     * @param array $supported The supported values.
     * @param bool $strict Whether to not use a default fallback.
     * @return string The negotiated value.
     * @throws ServerException if the negotiation type is not valid.
     */
    public function negotiate(string $type, array $supported, bool $strictMatch = false): string
    {
        switch ($type) {
            case 'content':
                $accepted = $this->getHeaderValue('Accept');

                return Negotiate::content($accepted, $supported, $strictMatch);
            case 'encoding':
                $accepted = $this->getHeaderValue('Accept-Encoding');

                return Negotiate::encoding($accepted, $supported);
            case 'language':
                $accepted = $this->getHeaderValue('Accept-Language');

                return Negotiate::language($accepted, $supported);
            default:
                throw ServerException::forInvalidNegotiationType($type);
        }
    }

    /**
     * Set global data.
     * @param string $type The global type.
     * @param array $data The data.
     * @return ServerRequest The ServerRequest.
     */
    public function setGlobals(string $type, array $data): self
    {
        if ($type === 'file') {
            $data = static::normalizeFiles($data);
            $data = static::buildFiles($data);
        }

        $this->globals[$type] ??= [];
        $this->globals[$type] += $data;

        if ($type === 'server') {
            $this->populateHeaders($data);
            $this->parseServer($data);
        }

        return $this;
    }

    /**
     * Set the current locale.
     * @param string $locale The locale.
     * @return ServerRequest The ServerRequest.
     */
    public function setLocale(string $locale): self
    {
        if (!in_array($locale, $this->supportedLocales, true)) {
            $locale = $this->defaultLocale;
        }

        $this->locale = $locale;

        Locale::setDefault($this->locale);

        return $this;
    }

    /**
     * Fetch a value from globals using dot notation.
     * @param string $type The global type.
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param int|array $options Options or flags to use when filtering.
     * @return UploadedFile|string|array|null The globals value.
     */
    protected function fetchGlobal(string $type, string|null $key = null, int $filter = FILTER_DEFAULT, int|array $options = 0): UploadedFile|string|array|null
    {
        $this->loadGlobals($type);

        $value = $this->globals[$type];

        if ($key) {
            switch ($type) {
                case 'file':
                case 'get':
                case 'post':
                    foreach (explode('.', $key) AS $key) {
                        if (!is_array($value) || !array_key_exists($key, $value)) {
                            return null;
                        }

                        $value = $value[$key];
                    }
                    break;
                default:
                    if (!array_key_exists($key, $value)) {
                        return null;
                    }

                    $value = $value[$key];
                    break;
            }
        }

        if ($type === 'file') {
            return $value;
        }

        return static::filterVar($value, $filter, $options);
    }

    /**
     * Load global data.
     * @param string $type The global type.
     */
    protected function loadGlobals(string $type): void
    {
        if (array_key_exists($type, $this->globals)) {
            return;
        }

        switch ($type) {
            case 'cookie':
                $data = $_COOKIE;
                break;
            case 'get':
                $data = $_GET;
                break;
            case 'file':
                $data = $_FILES;
                break;
            case 'post':
                $data = $_POST;
                break;
            case 'request':
                $data = $_REQUEST;
                break;
            case 'server':
                $data = $_SERVER;
                break;
            default:
                return;
        }

        $this->setGlobals($type, $data);
    }

    /**
     * Parse server data from the $_SERVER data.
     * @param array $data The $_SERVER data.
     */
    protected function parseServer(array $data): void
    {

        $method = $this->getServer('REQUEST_METHOD');
        $requestUri =  $this->getServer('REQUEST_URI');
        $query = $this->getServer('QUERY_STRING');
        $userAgent = $this->getHeaderValue('User-Agent');

        if ($method) {
            $this->setMethod($method);
        }

        if ($requestUri) {
            $path = parse_url($requestUri, PHP_URL_PATH);
            $this->uri->setPath($path);
        }

        if ($query) {
            $this->uri->setQueryString($query);
        }

        if ($userAgent) {
            $this->userAgent->setAgentString($userAgent);
        }

        if ($this->supportedLocales !== []) {
            $locale = $this->negotiate('language', $this->supportedLocales);

            $this->setLocale($locale);
        }
    }

    /**
     * Populate headers from the $_SERVER data.
     * @param array $data The $_SERVER data.
     */
    protected function populateHeaders(array $data): void
    {
        $contentType = $data['CONTENT_TYPE'] ?? getenv('CONTENT_TYPE');

        if ($contentType) {
            $this->setHeader('Content-Type', $contentType);
        }

        foreach ($data AS $key => $value) {
            if (strpos($key, 'HTTP_') !== 0) {
                continue;
            }

            $header = substr($key, 5);
            $header = strtolower($header);
            $header = str_replace('_', ' ', $header);
            $header = ucwords($header);
            $header = str_replace(' ', '-', $header);

            $this->setHeader($header, $value);
        }
    }

    /**
     * Build array of UploadedFiles.
     * @param array The normalized files.
     * @return array The UploadedFiles array.
     */
    protected static function buildFiles(array $files): array
    {
        return array_map(
            function($data) {
                if (!array_key_exists('tmp_name', $data)) {
                    return static::buildFiles($data);
                }

                return new UploadedFile($data);
            },
            $files
        );
    }

    /**
     * Filter a value.
     * @param string|int|float|array $value The value to filter.
     * @param int $filter The filter to apply.
     * @param int|array $options Options or flags to use when filtering.
     * @return string|array The globals value.
     */
    protected static function filterVar(string|int|float|array $value, int $filter = FILTER_DEFAULT, int|array $options = 0): string|array
    {
        if (is_array($value)) {
            return array_map(
                fn($val) => static::filterVar($val, $filter, $options),
                $value
            );
        }

        return (string) filter_var($value, $filter, $options);
    }

    /**
     * Normalize $_FILES array.
     * @param array $files The $_FILES array.
     * @return array The normalized files array.
     */
    protected static function normalizeFiles(array $files): array
    {
        $results = [];

        foreach ($files AS $name => $data) {
            $results[$name] = [];

            foreach ($data AS $field => $value) {
                $pointer = &$results[$name];

                if (!is_array($value)) {
                    $pointer[$field] = $value;
                    continue;
                }

                $stack = [&$pointer];

                $array = new RecursiveArrayIterator($value);
                $iterator = new RecursiveIteratorIterator($array, RecursiveIteratorIterator::SELF_FIRST);

                foreach ($iterator AS $key => $value) {
                    array_splice($stack, $iterator->getDepth() + 1);

                    $pointer = &$stack[count($stack) - 1];
                    $pointer = &$pointer[$key];
                    $stack[] = &$pointer[$key];

                    if ($iterator->hasChildren()) {
                        continue;
                    }

                    $pointer[$field] = $value;
                }
            }
        }

        return $results;
    }

}
