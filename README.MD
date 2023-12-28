# Yate core products API wrapper for PHP

## Purpose

This package intended to simplify communication to [Yate core products](https://yatebts.com/products/) API. Build for composer with PSR-4 autoload, only depends from PSR standard implementations and a good to integrate with DI containers.

It is decoupled from most of depenencies, requiring standart PSR interfaces instead of exact implementations. It uses:
- [PSR-7](https://www.php-fig.org/psr/psr-7) standard interfaces used for Request, Response and Stream
- [PSR-17](https://www.php-fig.org/psr/psr-17) standard interfaces used for RequestFactory and StreamFactory to create PSR-7 objects
- [PSR-18](https://www.php-fig.org/psr/psr-18) standard interface for HTTP clients used

## Usage
Start with [simple usage example](https://github.com/yate-wireless/api/blob/master/example/example.php), check [ApiGen docs](https://yate-wireless.github.io/api/). This lib may make communications to the core components much simpler, but you still need deep knowledge of architecture, entities and logics. Use [Yate API docs](https://yatebts.com/documentation/core-network-documentation/).

Mostly, you only need to:
- Create `Config` object, which carry Yate core product location and credentials
- Configure `Api` class instance with Config and other dependencies (manually, autowire, container, whatever)
- Use `call()` method to send api requests to proper nodes and got `ApiResponse` object
- Access response fields as associated array or properties

More complex scenario: instead of use `call()` you may use `prepareRequest()` method to convert you functional request to API to PSR-7 standard request object, send it, got PSR-7 Response object and thet process it with static `Api::parseResult()` to `ApiResponse` object.
This could be useful for async and bulk operations which are not supported by PSR-18 client interface.

For example, you need to complete 1000 independent calls as soon as possible, so you use above methods combined with Guzzle [concurrent requests](https://docs.guzzlephp.org/en/stable/quickstart.html#concurrent-requests) feature, so Guzzle do it's job for concurent call while this lib provides API request to PSR-7 request and PSR-7 response to functional result.

Also, `prepareRequest()` and `Api::parseResult()` should work fine in Async calls/promises and may be used in extra promise layer to implement async call to Yate API.

## Installation
In the Composer storage. Just add proper require section:
```
    "require": {
        "yate-wireless/api": "^0.1"
    }
```
Please, review the [changelog](https://github.com/yate-wireless/api/blob/master/CHANGELOG.MD) before to change used version.

## Dependencies
Composer dependencies:
- php: ^7.4|^8.0|^8.1|^8.2
- psr/http-message-implementation: ^1.0
- psr/http-factory-implementation: ^1.0
- psr/http-client-implementation: ^1.0

## Testing
Tested with PHPUnit 9.6 on php 7.4 to 8.2, 100% coverage, which does not mean everything will work as intended. Guzzle PSR-7 used for unit tests.

Current testing and compatibility status check on [Github flows](https://github.com/yate-wireless/api/actions/workflows/Compatibility.yml)

To test, install in dev mode and use: 
- `composer test` command from library root for run all modular tests
