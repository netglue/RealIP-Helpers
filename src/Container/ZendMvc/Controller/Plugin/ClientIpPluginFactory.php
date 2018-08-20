<?php
declare(strict_types=1);

namespace NetglueRealIP\Container\ZendMvc\Controller\Plugin;

use NetglueRealIP\Helper\ClientIPFromSuperGlobals;
use NetglueRealIP\ZendMvc\Controller\Plugin\ClientIP;
use Psr\Container\ContainerInterface;

class ClientIpPluginFactory
{
    public function __invoke(ContainerInterface $container) : ClientIP
    {
        return new ClientIP(
            $container->get(ClientIPFromSuperGlobals::class)
        );
    }
}
