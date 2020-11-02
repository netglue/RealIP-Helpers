<?php

declare(strict_types=1);

namespace NetglueRealIP\Container\Helper;

use NetglueRealIP\Helper\ClientIPFromSuperGlobals;
use Psr\Container\ContainerInterface;

class ClientIPFromSuperGlobalsFactory
{
    public function __invoke(ContainerInterface $container): ClientIPFromSuperGlobals
    {
        $config = $container->get('config')['proxy_headers'];
        $checkProxyHeaders           = $config['checkProxyHeaders'];
        $trustedHeader               = $config['trustedHeader'];
        $remoteAddressIsTrustedProxy = $config['remoteAddressIsTrustedProxy'];
        $trustedProxies              = $config['trustedProxies'];
        $proxyHeadersToInspect       = $config['proxyHeadersToInspect'];

        return new ClientIPFromSuperGlobals(
            $checkProxyHeaders,
            $trustedHeader,
            $remoteAddressIsTrustedProxy,
            $trustedProxies,
            $proxyHeadersToInspect
        );
    }
}
