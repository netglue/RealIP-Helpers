<?php
declare(strict_types=1);

namespace NetglueRealIPTest\Container\Helper;

use NetglueRealIP\ConfigProvider;
use NetglueRealIP\Container\Helper\ClientIPFromPsrServerRequestFactory;
use NetglueRealIP\Helper\ClientIPFromPsrServerRequest;
use NetglueRealIPTest\TestCase;
use Psr\Container\ContainerInterface;

class ClientIPFromPsrServerRequestFactoryTest extends TestCase
{
    public function testConfigProviderHasReasonableDefaults() : void
    {
        $config = (new ConfigProvider())();
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);

        $factory = new ClientIPFromPsrServerRequestFactory();
        $helper = $factory($container->reveal());
        $this->assertInstanceOf(ClientIPFromPsrServerRequest::class, $helper);
    }
}
