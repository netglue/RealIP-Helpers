<?php

declare(strict_types=1);

namespace NetglueRealIP\Container\Middleware;

use NetglueRealIP\Helper\ClientIPFromPsrServerRequest;
use NetglueRealIP\Middleware\IpAddress;
use Psr\Container\ContainerInterface;

class IpAddressFactory
{
    public function __invoke(ContainerInterface $container): IpAddress
    {
        return new IpAddress($container->get(ClientIPFromPsrServerRequest::class));
    }
}
