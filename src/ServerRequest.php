<?php
declare(strict_types=1);

namespace Fyre\Server;

use Closure;
use Fyre\Http\Negotiate;
use Fyre\Http\Request;
use Fyre\Http\Uri;
use Fyre\Http\UserAgent;
use Fyre\Server\Exceptions\ServerException;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

use function array_key_exists;
use function array_map;
use function array_merge;
use function array_splice;
use function count;
use function explode;
use function file_get_contents;
use function filter_var;
use function getenv;
use function in_array;
use function is_array;
use function json_decode;
use function locale_get_default;
use function parse_url;
use function str_replace;
use function str_starts_with;
use function strtolower;
use function substr;
use function ucwords;

use const FILTER_DEFAULT;
use const PHP_SAPI;
use const PHP_URL_PATH;

/**
 * ServerRequest
 */
class ServerRequest extends Request
{
    protected static Closure|ServerRequest|null $instance = null;

    protected string $defaultLocale;

    protected array $globals = [];

    protected string|null $locale = null;

    protected array $params = [];

    protected array $supportedLocales = [];

    protected UserAgent $userAgent;

    /**
     * Load a shared ServerRequest instance.
     *
     * @return ServerRequest The ServerRequest.
     */
    public static function instance(): static
    {
        if (static::$instance && static::$instance instanceof Closure) {
            return (static::$instance)();
        }

        return static::$instance ??= new static();
    }

    /**
     * Set a shared ServerRequest instance.
     *
     * @param Closure|ServerRequest $instance The ServerRequest, or a callback that returns the ServerRequest.
     */
    public static function setInstance(Closure|ServerRequest $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * New ServerRequest constructor.
     *
     * @param array $options The request options.
     */
    public function __construct(array $options = [])
    {
        $options['globals'] ??= [];
        $options['globals']['server'] ??= null;

        $this->defaultLocale = $options['defaultLocale'] ?? locale_get_default();
        $this->supportedLocales = $options['supportedLocales'] ?? [];

        foreach ($options['globals'] as $type => $data) {
            $this->loadGlobals($type, $data);
        }

        $options['method'] ??= $this->getServer('REQUEST_METHOD');
        $options['headers'] = array_merge(static::buildHeaders($this->getServer()), $options['headers'] ?? []);
        $options['body'] ??= file_get_contents('php://input');

        $uri = new Uri($options['baseUri'] ?? '');

        $requestUri = $this->getServer('REQUEST_URI');

        if ($requestUri) {
            $path = parse_url($requestUri, PHP_URL_PATH);
            $uri = $uri->setPath($path);
        }

        $query = $this->getServer('QUERY_STRING');

        if ($query) {
            $uri = $uri->setQueryString($query);
        }

        parent::__construct($uri, $options);

        $userAgent = $this->getHeaderValue('User-Agent') ?? '';

        $this->userAgent = new UserAgent($userAgent);

        if ($this->supportedLocales !== [] && $this->hasHeader('Accept-Language')) {
            $this->locale = $this->negotiate('language', $this->supportedLocales);
        }
    }

    /**
     * Get a value from the $_COOKIE array.
     *
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param array|int $options Options or flags to use when filtering.
     * @return mixed The $_COOKIE value.
     */
    public function getCookie(string|null $key = null, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
    {
        return $this->fetchGlobal('cookie', $key, $filter, $options);
    }

    /**
     * Get a value from the $_POST array or JSON body data.
     *
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param array|int $options Options or flags to use when filtering.
     * @return mixed The $_POST or JSON body value.
     */
    public function getData(string|null $key = null, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
    {
        return $this->getHeaderValue('Content-Type') === 'application/json' ?
            $this->getJson($key, $filter, $options) :
            $this->getPost($key, $filter, $options);
    }

    /**
     * Get the default locale.
     *
     * @return string The default locale.
     */
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * Get a value from the $_ENV array.
     *
     * @param string $key The key.
     * @param int $filter The filter to apply.
     * @param array|int $options Options or flags to use when filtering.
     * @return mixed The $_ENV value.
     */
    public function getEnv(string $key, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
    {
        $value = getenv($key, false);

        if ($value === false) {
            return null;
        }

        return static::filterVar($value, $filter, $options);
    }

    /**
     * Get an UploadedFile or array of files from the $_FILE array.
     *
     * @param string|null $key The key.
     * @return mixed The $_FILE value.
     */
    public function getFile(string|null $key = null): mixed
    {
        return $this->fetchGlobal('file', $key);
    }

    /**
     * Get a value from JSON body data.
     *
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param array|int $options Options or flags to use when filtering.
     * @return mixed The JSON body value.
     */
    public function getJson(string|null $key = null, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
    {
        return $this->fetchGlobal('json', $key, $filter, $options);
    }

    /**
     * Get the current locale.
     *
     * @return string The current locale.
     */
    public function getLocale(): string
    {
        return $this->locale ?? $this->defaultLocale;
    }

    /**
     * Get a parameter from the request.
     *
     * @param string $key The key.
     * @return mixed The parameter value.
     */
    public function getParam(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Get a value from the $_POST array.
     *
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param array|int $options Options or flags to use when filtering.
     * @return mixed The $_POST value.
     */
    public function getPost(string|null $key = null, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
    {
        return $this->fetchGlobal('post', $key, $filter, $options);
    }

    /**
     * Get a value from the $_GET array.
     *
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param array|int $options Options or flags to use when filtering.
     * @return mixed The $_GET value.
     */
    public function getQuery(string|null $key = null, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
    {
        return $this->fetchGlobal('get', $key, $filter, $options);
    }

    /**
     * Get a value from the $_SERVER array.
     *
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param array|int $options Options or flags to use when filtering.
     * @return mixed The $_SERVER value.
     */
    public function getServer(string|null $key = null, int $filter = FILTER_DEFAULT, array|int $options = 0): mixed
    {
        return $this->fetchGlobal('server', $key, $filter, $options);
    }

    /**
     * Get the user agent.
     *
     * @return UserAgent The user agent.
     */
    public function getUserAgent(): UserAgent
    {
        return $this->userAgent;
    }

    /**
     * Determine if the request was made using AJAX.
     *
     * @return bool TRUE if the request was made using AJAX, otherwise FALSE.
     */
    public function isAjax(): bool
    {
        $xRequestedWith = $this->getHeaderValue('X-Requested-With');

        return $xRequestedWith && strtolower($xRequestedWith) === 'xmlhttprequest';
    }

    /**
     * Determine if the request was made from the CLI.
     *
     * @return bool TRUE if the request was made from the CLI, otherwise FALSE.
     */
    public function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Determine if the request is using HTTPS.
     *
     * @return bool TRUE if the request is using HTTPS, otherwise FALSE.
     */
    public function isSecure(): bool
    {
        $https = $this->getServer('HTTPS');

        if ($https && strtolower($https) !== 'off') {
            return true;
        }

        $xForwardedProto = $this->getHeaderValue('X-Forwarded-Proto');

        if ($xForwardedProto && strtolower($xForwardedProto) === 'https') {
            return true;
        }

        $frontEndHttps = $this->getHeaderValue('Front-End-Https');

        return $frontEndHttps && strtolower($frontEndHttps) !== 'off';
    }

    /**
     * Negotiate a value from HTTP headers.
     *
     * @param string $type The negotiation type.
     * @param array $supported The supported values.
     * @param bool $strict Whether to not use a default fallback.
     * @return string The negotiated value.
     *
     * @throws ServerException if the negotiation type is not valid.
     */
    public function negotiate(string $type, array $supported, bool $strictMatch = false): string
    {
        switch ($type) {
            case 'content':
                $accepted = $this->getHeaderValue('Accept') ?? '';

                return Negotiate::content($accepted, $supported, $strictMatch);
            case 'encoding':
                $accepted = $this->getHeaderValue('Accept-Encoding') ?? '';

                return Negotiate::encoding($accepted, $supported);
            case 'language':
                $accepted = $this->getHeaderValue('Accept-Language') ?? '';

                return Negotiate::language($accepted, $supported);
            default:
                throw ServerException::forInvalidNegotiationType($type);
        }
    }

    /**
     * Set global data for the request.
     *
     * @param string $type The global type.
     * @param array $data The data.
     * @return ServerRequest The ServerRequest.
     */
    public function setGlobal(string $type, array $data): static
    {
        $temp = clone $this;

        $temp->globals[$type] = $data;

        return $temp;
    }

    /**
     * Set the current locale.
     *
     * @param string $locale The locale.
     * @return ServerRequest The ServerRequest.
     *
     * @throws ServerException if the locale is not supported.
     */
    public function setLocale(string $locale): static
    {
        if (!in_array($locale, $this->supportedLocales, true)) {
            throw ServerException::forUnsupportedLocale($locale);
        }

        $temp = clone $this;

        $temp->locale = $locale;

        return $temp;
    }

    /**
     * Set a parameter for the request.
     *
     * @param string $key The key.
     * @param mixed $value The parameter value.
     * @return ServerRequest The ServerRequest.
     */
    public function setParam(string $key, mixed $value): static
    {
        $temp = clone $this;

        $temp->params[$key] = $value;

        return $temp;
    }

    /**
     * Fetch a value from globals using dot notation.
     *
     * @param string $type The global type.
     * @param string|null $key The key.
     * @param int $filter The filter to apply.
     * @param array|int $options Options or flags to use when filtering.
     * @return array|string|UploadedFile|null The globals value.
     */
    protected function fetchGlobal(string $type, string|null $key = null, int $filter = FILTER_DEFAULT, array|int $options = 0): array|string|UploadedFile|null
    {
        $this->loadGlobals($type);

        $value = $this->globals[$type];

        if ($key) {
            switch ($type) {
                case 'file':
                case 'get':
                case 'json':
                case 'post':
                    foreach (explode('.', $key) as $key) {
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
     *
     * @param string $type The global type.
     * @param array|null $data The data.
     */
    protected function loadGlobals(string $type, array|null $data = null): void
    {
        if (array_key_exists($type, $this->globals)) {
            return;
        }

        switch ($type) {
            case 'cookie':
                $data ??= $_COOKIE;
                break;
            case 'file':
                $data ??= $_FILES;

                $data = static::normalizeFiles($data);
                $data = static::buildFiles($data);
                break;
            case 'get':
                $data ??= $_GET;
                break;
            case 'json':
                $data ??= json_decode($this->body, true) ?? [];
                break;
            case 'post':
                $data ??= $_POST;
                break;
            case 'request':
                $data ??= $_REQUEST;
                break;
            case 'server':
                $data ??= $_SERVER;
                break;
            default:
                return;
        }

        $this->globals[$type] = $data;
    }

    /**
     * Build array of UploadedFiles.
     *
     * @param array The normalized files.
     * @return array The UploadedFiles array.
     */
    protected static function buildFiles(array $files): array
    {
        return array_map(
            function(array $data): array|UploadedFile {
                if (!array_key_exists('tmp_name', $data)) {
                    return static::buildFiles($data);
                }

                return new UploadedFile($data);
            },
            $files
        );
    }

    /**
     * Populate headers from the $_SERVER data.
     *
     * @param array $data The $_SERVER data.
     * @return array The headers.
     */
    protected static function buildHeaders(array $data): array
    {
        $headers = [];

        $contentType = $data['CONTENT_TYPE'] ?? getenv('CONTENT_TYPE');

        if ($contentType) {
            $headers['Content-Type'] = $contentType;
        }

        foreach ($data as $key => $value) {
            if (!str_starts_with($key, 'HTTP_')) {
                continue;
            }

            $header = substr($key, 5);
            $header = strtolower($header);
            $header = str_replace('_', ' ', $header);
            $header = ucwords($header);
            $header = str_replace(' ', '-', $header);

            $headers[$header] = $value;
        }

        return $headers;
    }

    /**
     * Filter a value.
     *
     * @param array|float|int|string $value The value to filter.
     * @param int $filter The filter to apply.
     * @param array|int $options Options or flags to use when filtering.
     * @return array|string The globals value.
     */
    protected static function filterVar(array|float|int|string $value, int $filter = FILTER_DEFAULT, array|int $options = 0): array|string
    {
        if (is_array($value)) {
            return array_map(
                fn(mixed $val): array|string => static::filterVar($val, $filter, $options),
                $value
            );
        }

        return (string) filter_var($value, $filter, $options);
    }

    /**
     * Normalize $_FILES array.
     *
     * @param array $files The $_FILES array.
     * @return array The normalized files array.
     */
    protected static function normalizeFiles(array $files): array
    {
        $results = [];

        foreach ($files as $name => $data) {
            $results[$name] = [];

            foreach ($data as $field => $value) {
                $pointer = &$results[$name];

                if (!is_array($value)) {
                    $pointer[$field] = $value;

                    continue;
                }

                $stack = [&$pointer];

                $array = new RecursiveArrayIterator($value);
                $iterator = new RecursiveIteratorIterator($array, RecursiveIteratorIterator::SELF_FIRST);

                foreach ($iterator as $key => $value) {
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
