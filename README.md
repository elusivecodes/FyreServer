# FyreServer

**FyreServer** is a free, HTTP server request/response library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Server Requests](#server-requests)
- [Client Responses](#client-responses)
- [Uploaded Files](#uploaded-files)



## Installation

**Using Composer**

```
composer install fyre/server
```

In PHP:

```php
use Fyre\Server\ServerRequest;
use Fyre\Server\ClientResponse;
```


## Server Requests

This class extends the [*Request*](https://github.com/elusivecodes/FyreRequest) class.

- `$config` is an array containing configuration options.
    - `baseUri` is a string representing the base URI to use.
    - `body` is a string representing the request body, and will default to the value of `php://input`.
    - `defaultLocale` is a string representing the default locale, and will default to the system default.
    - `supportedLocales` is an array containing the supported locales.

```php
$request = new ServerRequest($config);
```

**Get Cookie**

Get a value from $_COOKIE array.

- `$key` is a string representing the array key using "dot" notation.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number o array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getCookie($key, $filter, $options);
```

**Get Default Locale**

Get the default locale.

```php
$defaultLocale = $request->getDefaultLocale();
```

**Get Environment**

Get a value from $_ENV array.

- `$key` is a string representing the array key.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number o array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getEnv($key, $filter, $options);
```

**Get File**

Get an *UploadedFile* or array of files from $_FILE array.

- `$key` is a string representing the array key using "dot" notation.

```php
$file = $request->getFile($key);
```

**Get Get**

Get a value from $_GET array.

- `$key` is a string representing the array key using "dot" notation.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number o array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getGet($key, $filter, $options);
```

**Get Locale**

Get the current locale.

```php
$locale = $request->getLocale();
```

**Get Post**

Get a value from $_POST array.

- `$key` is a string representing the array key using "dot" notation.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number o array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getPost($key, $filter, $options);
```

**Get Server**

Get a value from $_SERVER array.

- `$key` is a string representing the array key.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number o array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getServer($key, $filter, $options);
```

**Get User Agent**

Get the user agent.

```php
$userAgent = $request->getUserAgent();
```

This method will return a [*UserAgent*](https://github.com/elusivecodes/FyreUserAgent).

**Is AJAX**

Determine if the request was made using AJAX.

```php
$isAjax = $request->isAjax();
```

**Is CLI**

Determine if the request was made from the CLI.

```php
$isCli = $request->isCli();
```

**Is Secure**

Determine if the request is using HTTPS.

```php
$isSecure = $request->isSecure();
```

**Negotiate**

Negotiate a value from HTTP headers.

- `$type` is a string representing the type of negotiation to perform, and must be one of either "*content*", "*encoding*" or "*language*".
- `$supported` is an array containing the supported values.
- `$strict` is a boolean indicating whether to not use a default fallback, and will default to *false*.

```php
$request->negotiate($type, $supported, $strict);
```

**Set Locale**

Set the current locale.

- `$locale` is a string representing the locale.

```php
$request->setLocale($locale);
```

The locale must be present in the `supportedLocales` property of the *Request* `$options` parameter, or the default locale will be used.


## Client Responses

This class extends the [*Response*](https://github.com/elusivecodes/FyreResponse) class.

```php
$response = new ClientResponse();
```

**Delete Cookie**

- `$name` is a string representing the cookie name.
- `$name` is an array containing cookie options.

```php
$response->deleteCookie($name, $options);
```

**Get Cookie**

- `$name` is a string representing the cookie name.

```php
$cookie = $response->getCookie($name);
```

This method will return a [*Cookie*](https://github.com/elusivecodes/FyreCookie).

**Has Cookie**

Determine if a cookie has been set.

- `$name` is a string representing the cookie name.

```php
$hasCookie = $response->hasCookie($name);
```

**No Cache**

Set headers to prevent browser caching.

```php
$response->noCache();
```

**Redirect**

Set a redirect response.

- `$uri` is a string representing the URI to redirect to.
- `$code` is a number representing the header status code, and will default to *302*.

```php
$response->redirect($uri, $code);
```

**Send**

Send the response to the client.

```php
$response->send();
```

**Set Content Type**

Set the content type header

- `$mimeType` is a string representing the MIME type.
- `$charset` is a string representing the character set, and will default to "*UTF-*".

```php
$response->setContentType($mimeType, $charset);
```

**Set Cookie**

Set a cookie value.

- `$name` is a string representing the cookie name.
- `$value` is a string representing the cookie value.
- `$name` is an array containing cookie options.

```php
$response->setCookie($name, $value, $options);
```

**Set Date**

Set the date header.

- `$date` is a string, number or *DateTime* object representing the date.

```php
$response->setDate($date);
```

**Set JSON**

Set a JSON response.

- `$data` is the data to send.

```php
$response->setJson($data);
```

**Set Last Modified**

Set the last modified date header.

- `$date` is a string, number or *DateTime* object representing the date.

```php
$response->setLastModified($date);
```

**Set XML**

Set an XML response.

- `$data` is a *SimpleXMLElement* containing the data to send.

```php
$response->setXml($data);
```


## Uploaded Files

This class extends the [*File*](https://github.com/elusivecodes/FyreFileSystem) class.

**Client Extension**

Get the client extension.

```php
$extension = $uploadedFile->clientExtension();
```

**Client MIME Type**

Get the client MIME type.

```php
$mimeType = $uploadedFile->clientMimeType();
```

**Client Name**

Get the client filename.

```php
$name = $uploadedFile->clientName();
```

**Error**

Get the uploaded error code.

```php
$error = $uploadedFile->error();
```

**Has Moved**

Determine if the uploaded file has been moved.

```php
$hasMoved = $uploadedFile->hasMoved();
```

**Is Valid**

Determine if the uploaded file is valid.

```php
$isValid = $uploadedFile->isValid();
```

**Move To**

Move the uploaded file.

- `$destination` is string representing the destination folder.
- `$name` is a string representing the new filename, and will default to the client name.

```php
$file = $uploadedFile->moveTo($destination, $name);
```

This method will return a new [*File*](https://github.com/elusivecodes/FyreFileSystem) for the moved file.