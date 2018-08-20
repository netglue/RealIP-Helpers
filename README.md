# Helpers for retrieving the real client IP in Zend Projects

[![Latest Stable Version](https://poser.pugx.org/netglue/realip-helpers/version)](https://packagist.org/packages/netglue/realip-helpers)
[![Coverage Status](https://coveralls.io/repos/github/netglue/RealIP-Helpers/badge.svg)](https://coveralls.io/github/netglue/RealIP-Helpers)
[![Build Status](https://travis-ci.org/netglue/RealIP-Helpers.svg?branch=master)](https://travis-ci.org/netglue/RealIP-Helpers)
[![Maintainability](https://api.codeclimate.com/v1/badges/1b1d79eb6c32e717413c/maintainability)](https://codeclimate.com/github/netglue/RealIP-Helpers/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/1b1d79eb6c32e717413c/test_coverage)](https://codeclimate.com/github/netglue/RealIP-Helpers/test_coverage)

## Why?

I put this together to scratch an itch - I typically write server-side apps using zend expressive or zend framework and
there's an existing middleware implementation [akrabat/ip-address-middleware](https://github.com/akrabat/ip-address-middleware),
that already solves the problem of getting the remote address, optionally from common proxy headers with PSR-7/PSR-15 middleware.
The problem I had with that package is that it requires an array of trusted proxies in order to perform searches of those
headers, so if your app is behind a loadbalancer and it's IP might change, this means that updating your config is a pain,
particularly if you're using the component in multiple projects on the same environment. Also, this package introduces the
concept of a trusted header, i.e. if you've got an upstream proxy that you can guarantee will send you a header with the
remote address and you're confident that your proxy cannot be circumvented, then you can ignore the idea of trusted proxies
and just use that header. Furthermore, I wanted the convenience of default configuration and DI factories for the middleware
and helpers. You may also want to checkout this too: [middlewares/client-ip](https://github.com/middlewares/client-ip).

## Install

Install with composer using `"netglue/realip-helpers"`

## Configure

Without any additional configuration, the helper(s) will simply return whatever `$_SERVER['REMOTE_ADDR']` reports (But 
sanitised and validated as an actual IP address). The reason you're looking at this is probably because `REMOTE_ADDR` 
doesn't cut it… The options are detailed in [`./src/ConfigProvider.php`](https://github.com/netglue/RealIP-Helpers/blob/master/src/ConfigProvider.php) and repeated below. The values are the defaults.

```php
return [
    'proxy_headers' => [
        // When figuring out the client IP, should common proxy headers be checked?
        'checkProxyHeaders' => false,
        // If your app is firewalled, and you're sure you can trust that, say,
        // Cloud Flare is sending you the client IP in the header 'CF-Connecting-IP', you can add that here
        // and it will always be used
        'trustedHeader' => null,
        // If your app is on a private network and REMOTE_ADDR is always the load balancer ip, you could say
        // that REMOTE_ADDR is always a trusted proxy
        'remoteAddressIsTrustedProxy' => false,
        // You can provide an array of IP addresses (v4 or v6) of proxies that you trust. These will be eliminated as
        // potential client IP addresses
        'trustedProxies' => [],
        // If you provide a non-empty array of proxy headers to inspect, only these headers will be checked,
        // overriding the defaults. If you know that your proxy/loadbalancer only sends X-Forwarded-For, you could
        // put just that one in here. By default, a number of headers are inspected:
        'proxyHeadersToInspect' => [],
    ],
];
```

## ZF3 Usage

Config should be injected automatically if you are using Zend's component installer, so it's just a case of altering 
config values for you environment if appropriate and then issuing a `$ip = $this->clientIp()` from a controller method to
retrieve the remote IP address.

## Expressive Usage

Simply add `NetglueRealIP\Middleware\IpAddress::class` to your pipeline and your request will contain the attribute
 `'ip_address'`, i.e. `$request->getAttribute('ip_address')`. You can change the name of the attribute at construction 
 time if you are not using a DI container, or if you are, by aliasing the class to a different factory.

## Test

`cd` to wherever the module is installed, issue a `composer install` followed by a `composer test`.

## Contributions

PR's are welcomed. Please write tests for new features.

## Support

You're welcome to file issues, but please understand that finding the time to answer support requests is very limited
so there might be a long wait for an answer.


## About

[Netglue makes websites and apps in Devon, England](https://netglue.uk).
We hope this is useful to you and we’d appreciate feedback either way :)

