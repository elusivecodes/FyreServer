# FyreServer

**FyreServer** is a free, open-source immutable HTTP server request/response library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Server Requests](#server-requests)
- [Client Responses](#client-responses)
- [Download Responses](#download-responses)
- [Redirect Responses](#redirect-responses)
- [Uploaded Files](#uploaded-files)



## Installation

**Using Composer**

```
composer require fyre/server
```

In PHP:

```php
use Fyre\Server\ClientResponse;
use Fyre\Server\DownloadResponse;
use Fyre\Server\RedirectResponse;
use Fyre\Server\ServerRequest;
```


## Server Requests

This class extends the [*Request*](https://github.com/elusivecodes/FyreRequest) class.

- `$options` is an array containing configuration options.
    - `baseUri` is a string representing the base URI to use.
    - `method` is a string representing the request method, and will default to the server request method.
    - `body` is a string representing the request body, and will default to the value of `php://input`.
    - `headers` is an array containing headers to set, and will default to the server headers.
    - `defaultLocale` is a string representing the default locale, and will default to the system default.
    - `supportedLocales` is an array containing the supported locales.
    - `protocolVersion` is a string representing the protocol version, and will default to "*1.1*".

```php
$request = new ServerRequest($options);
```

**Get Cookie**

Get a value from the `$_COOKIE` array.

- `$key` is a string representing the array key using "dot" notation.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number or array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getCookie($key, $filter, $options);
```

If the `$key` argument is omitted, this method will return an array containing all values.

```php
$values = $request->getCookie();
```

**Get Data**

Get a value from the `$_POST` array or JSON body data.

- `$key` is a string representing the array key using "dot" notation.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number or array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getData($key, $filter, $options);
```

If the `$key` argument is omitted, this method will return an array containing all values.

```php
$values = $request->getData();
```

**Get Default Locale**

Get the default locale.

```php
$defaultLocale = $request->getDefaultLocale();
```

**Get Environment**

Get a value from the `$_ENV` array.

- `$key` is a string representing the array key.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number or array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getEnv($key, $filter, $options);
```

**Get File**

Get an *UploadedFile* or array of files from the `$_FILE` array.

- `$key` is a string representing the array key using "dot" notation.

```php
$file = $request->getFile($key);
```

If the `$key` argument is omitted, this method will return an array containing all files.

```php
$files = $request->getFile();
```

**Get Json**

Get a value from JSON body data.

- `$key` is a string representing the array key using "dot" notation.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number or array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getJson($key, $filter, $options);
```

If the `$key` argument is omitted, this method will return an array containing all values.

```php
$values = $request->getJson();
```

**Get Locale**

Get the current locale.

```php
$locale = $request->getLocale();
```

**Get Post**

Get a value from the `$_POST` array.

- `$key` is a string representing the array key using "dot" notation.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number or array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getPost($key, $filter, $options);
```

If the `$key` argument is omitted, this method will return an array containing all values.

```php
$values = $request->getPost();
```

**Get Query**

Get a value from the `$_GET` array.

- `$key` is a string representing the array key using "dot" notation.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number or array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getQuery($key, $filter, $options);
```

If the `$key` argument is omitted, this method will return an array containing all values.

```php
$values = $request->getQuery();
```

**Get Server**

Get a value from the `$_SERVER` array.

- `$key` is a string representing the array key.
- `$filter` is a number representing the filter to apply, and will default to *FILTER_DEFAULT*.
- `$options` is a number or array containing flags to use when filtering, and will default to *0*.

```php
$value = $request->getServer($key, $filter, $options);
```

If the `$key` argument is omitted, this method will return an array containing all values.

```php
$values = $request->getServer();
```

**Get User Agent**

Get the user agent.

```php
$userAgent = $request->getUserAgent();
```

This method will return a [*UserAgent*](https://github.com/elusivecodes/FyreUserAgent).

**Is AJAX**

Determine whether the request was made using AJAX.

```php
$isAjax = $request->isAjax();
```

**Is CLI**

Determine whether the request was made from the CLI.

```php
$isCli = $request->isCli();
```

**Is Secure**

Determine whether the request is using HTTPS.

```php
$isSecure = $request->isSecure();
```

**Negotiate**

Negotiate a value from HTTP headers.

- `$type` is a string representing the type of negotiation to perform, and must be one of either "*content*", "*encoding*" or "*language*".
- `$supported` is an array containing the supported values.
- `$strict` is a boolean indicating whether to not use a default fallback, and will default to *false*.

```php
$value = $request->negotiate($type, $supported, $strict);
```

**Set Locale**

Set the current locale.

- `$locale` is a string representing the locale.

```php
$newRequest = $request->setLocale($locale);
```

The locale must be present in the `supportedLocales` property of the *ServerRequest* `$options` parameter.


## Client Responses

This class extends the [*Response*](https://github.com/elusivecodes/FyreResponse) class.

- `$options` is an array containing configuration options.
    - `body` is a string representing the message body, and will default to "".
    - `headers` is an array containing additional headers to set.
    - `protocolVersion` is a string representing the protocol version, and will default to "*1.1*".
    - `statusCode` is a number representing the status code, and will default to *200*.

```php
$response = new ClientResponse($options);
```

**Delete Cookie**

- `$name` is a string representing the cookie name.
- `$options` is an array containing cookie options.
    - `domain` is a string representing the cookie domain, and will default to "".
    - `path` is a string representing the cookie path, and will default to "*/*".
    - `secure` is a boolean indicating whether to set a secure cookie, and will default to *false*.
    - `httpOnly` is a boolean indicating whether to the cookie should be HTTP only, and will default to *false*.
    - `sameSite` is a string representing the cookie same site, and will default to "*Lax*".

```php
$newResponse = $response->deleteCookie($name, $options);
```

**Get Cookie**

- `$name` is a string representing the cookie name.

```php
$cookie = $response->getCookie($name);
```

This method will return a [*Cookie*](https://github.com/elusivecodes/FyreCookie).

**Has Cookie**

Determine whether a cookie has been set.

- `$name` is a string representing the cookie name.

```php
$hasCookie = $response->hasCookie($name);
```

**No Cache**

Set headers to prevent browser caching.

```php
$newResponse = $response->noCache();
```

**Send**

Send the response to the client.

```php
$response->send();
```

**Set Content Type**

Set the content type header

- `$mimeType` is a string representing the MIME type.
- `$charset` is a string representing the character set, and will default to "*UTF-8*".

```php
$newResponse = $response->setContentType($mimeType, $charset);
```

**Set Cookie**

Set a cookie value.

- `$name` is a string representing the cookie name.
- `$value` is a string representing the cookie value.
- `$options` is an array containing cookie options.
    - `expires` is a number representing the cookie lifetime, and will default to *0*.
    - `domain` is a string representing the cookie domain, and will default to "".
    - `path` is a string representing the cookie path, and will default to "*/*".
    - `secure` is a boolean indicating whether to set a secure cookie, and will default to *false*.
    - `httpOnly` is a boolean indicating whether to the cookie should be HTTP only, and will default to *false*.
    - `sameSite` is a string representing the cookie same site, and will default to "*Lax*".

```php
$newResponse = $response->setCookie($name, $value, $options);
```

**Set Date**

Set the date header.

- `$date` is a string, number or *DateTime* object representing the date.

```php
$newResponse = $response->setDate($date);
```

**Set JSON**

Set a JSON response.

- `$data` is the data to send.

```php
$newResponse = $response->setJson($data);
```

**Set Last Modified**

Set the last modified date header.

- `$date` is a string, number or *DateTime* object representing the date.

```php
$newResponse = $response->setLastModified($date);
```

**Set XML**

Set an XML response.

- `$data` is a *SimpleXMLElement* containing the data to send.

```php
$newResponse = $response->setXml($data);
```


## Download Responses

This class extends the [*ClientResponse*](#client-responses) class.

- `$path` is a string representing the file path.
- `$options` is an array containing configuration options.
    - `filename` is a string representing the download filename, and will default to the file name.
    - `mimeType` is a string representing the MIME type, and will default to the file MIME type.
    - `headers` is an array containing additional headers to set.
    - `protocolVersion` is a string representing the protocol version, and will default to "*1.1*".
    - `statusCode` is a number representing the status code, and will default to *200*.

```php
$response = new DownloadResponse($path, $options);
```

**From Binary**

- `$data` is a string representing the file data.
- `$options` is an array containing configuration options.
    - `filename` is a string representing the download filename, and will default to the file name.
    - `mimeType` is a string representing the MIME type, and will default to the file MIME type.

```php
$response = DownloadResponse::fromBinary($data, $options);
```

**Get File**

Get the download [*File*](https://github.com/elusivecodes/FyreFileSystem#files).

```php
$file = $response->getFile();
```


## Redirect Responses

This class extends the [*ClientResponse*](#client-responses) class.

- `$uri` is a [*Uri*](https://github.com/elusivecodes/FyreURI) or string representing the URI to redirect to.
- `$code` is a number representing the header status code, and will default to *302*.
- `$options` is an array containing configuration options.

```php
$response = new RedirectResponse($uri, $code $options);
```


## Uploaded Files

This class extends the [*File*](https://github.com/elusivecodes/FyreFileSystem#files) class.

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

Determine whether the uploaded file has been moved.

```php
$hasMoved = $uploadedFile->hasMoved();
```

**Is Valid**

Determine whether the uploaded file is valid.

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

This method will return a new [*File*](https://github.com/elusivecodes/FyreFileSystem#files) for the moved file.