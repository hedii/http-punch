[![Build Status](https://travis-ci.org/hedii/http-punch.svg?branch=master)](https://travis-ci.org/hedii/http-punch)

# http-punch

Http punch is a php library for punching (visiting) an http url.

It can be used to check whether an host is online or offline, to check a server response time, to hit a cron job url, to hit a webhook url, etc...

## Table of contents

- [Table of contents](#table-of-contents)
- [Installation](#installation)
- [Usage](#usage)
  - [Instantiation](#instantiation)
  - [Perform an http punch](#perform-an-http-punch)
  - [The result array](#the-result-array)
- [Testing](#testing)
- [License](#license)

## Installation

Install via [composer](https://getcomposer.org/doc/00-intro.md)
```sh
composer require hedii/http-punch
```

## Usage

### Instantiation

Create a http punch instance:

```php
<?php

// require composer autoloader
require '/path/to/vendor/autoload.php';

// instantiate
$puncher = new Hedii\HttpPunch\HttpPunch();
```

Alternatively, you can pass a request timeout in second (default 30), and a connection timeout in second (default 10):

```php
// instantiate with request and connection timeout as parameters
$puncher = new Hedii\HttpPunch\HttpPunch(20, 5);
```

### Perform an http punch

Call the `punch(string $url, string $method = 'get', array $body = []): array` method to perform an http punch.

```php
// instantiate
$puncher = new Hedii\HttpPunch\HttpPunch();

// perform a get request
$result = $puncher->punch('http://example.com');

// perform a post request 
$result = $puncher->punch('http://example.com', 'post');

// perform a post request with a given body as an array
$result = $puncher->punch('http://example.com', 'post', ['foo' => 'bar']);

// set the outgoing ip address (it uses CURLOPT_INTERFACE behind the scenes) and perform a get request
$result = $puncher->setIp('192.160.0.101')->punch('http://example.com');

// set an array of request headers and perform a get request
$result = $puncher->setHeaders(['foo' => 'bar'])->punch('http://example.com');
```

The result of this method is an array with with the http punch report information. The value of `success` indicates if the website is has successfully responded to the request:

```
array(5) {
    'url' => "http://example.com"
    'success' => true
    'status_code' => 200
    'message' => "OK"
    'transfer_time' => 0.765217
}
```

Notice that the url field contains the effective url (in case of redirect response, this is the final url, the status code will be 200 and the message "OK").

### The result array

| Field           | Type          | Description                                                     |
| --------------- | ------------- | --------------------------------------------------------------- |
| `url`           | string        | The effective url                                               |
| `success`       | boolean       | Whether the http punch is successful or not                     |
| `status_code`   | null\|integer | The http response status code or null in case of a client error |
| `message`       | string        | The http response message or the client error message           |
| `transfer_time` | float         | The transfer time in seconds                                    |

## Testing

```
composer test
```

## License

hedii/http-punch is released under the MIT Licence. See the bundled [LICENSE](https://github.com/hedii/http-punch/blob/master/LICENSE.md) file for details.
