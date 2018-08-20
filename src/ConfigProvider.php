<?php
declare(strict_types=1);

namespace NetglueRealIP;

class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'controller_plugins' => $this->getZendMvcControllerPluginConfig(),
            'proxy_headers' => $this->getProxyHeaderSetup(),
        ];
    }

    public function getDependencies() : array
    {
        return [
            Helper\ClientIPFromPsrServerRequest::class => Container\Helper\ClientIPFromPsrServerRequestFactory::class,
            Helper\ClientIPFromSuperGlobals::class => Container\Helper\ClientIPFromSuperGlobalsFactory::class,
            Middleware\IpAddress::class => Container\Middleware\IpAddressFactory::class,
        ];
    }

    public function getZendMvcControllerPluginConfig() : array
    {
        return [
            'factories' => [
                ZendMvc\Controller\Plugin\ClientIP::class => Container\ZendMvc\Controller\Plugin\ClientIPFactory::class,
            ]
        ];
    }

    public function getProxyHeaderSetup() : array
    {
        return [
            // When figuring out the client IP, should common proxy headers be checked?
            'checkProxyHeaders' => false,
            // If your app is firewalled, and you're sure you can trust that, say,
            // Cloud Flare is sending you the client IP in the header 'CF-Connecting-IP', you can add that here
            // and it will always be used
            'trustedHeader' => null,
            // If your app is on a private network and REMOTE_ADDR is always the load balancer ip, you could say
            // that REMOTE_ADDR is always a trusted proxy
            'remoteAddressIsTrustedProxy' => false,
            // You can provide an array of IP addresses (v4 or v6) of proxies that trust. These will be eliminated as
            // potential client IP addresses
            'trustedProxies' => [],
            // If you provide a non-empty array of proxy headers to inspect, only these headers will be checked,
            // overriding the defaults. If you know that your proxy/loadbalancer only sends X-Forwarded-For, you could
            // put just that one in here:
            'proxyHeadersToInspect' => [],
        ];
    }
}
