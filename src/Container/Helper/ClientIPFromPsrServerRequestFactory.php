<?php
declare(strict_types=1);

namespace NetglueRealIP\Container\Helper;

use NetglueRealIP\Helper\ClientIPFromPsrServerRequest;
use Psr\Container\ContainerInterface;

class ClientIPFromPsrServerRequestFactory
{
    public function __invoke(ContainerInterface $container) : ClientIPFromPsrServerRequest
    {
        $config = $container->get('config')['proxy_headers'];
        $checkProxyHeaders           = $config['checkProxyHeaders'];
        $trustedHeader               = $config['trustedHeader'];
        $remoteAddressIsTrustedProxy = $config['remoteAddressIsTrustedProxy'];
        $trustedProxies              = $config['trustedProxies'];
        $proxyHeadersToInspect       = $config['proxyHeadersToInspect'];
        return new ClientIPFromPsrServerRequest(
            $checkProxyHeaders,
            $trustedHeader,
            $remoteAddressIsTrustedProxy,
            $trustedProxies,
            $proxyHeadersToInspect
        );
    }
}
