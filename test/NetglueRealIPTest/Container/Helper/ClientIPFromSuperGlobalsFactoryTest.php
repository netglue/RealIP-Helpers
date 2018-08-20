<?php
declare(strict_types=1);

namespace NetglueRealIPTest\Container\Helper;

use NetglueRealIP\ConfigProvider;
use NetglueRealIP\Container\Helper\ClientIPFromSuperGlobalsFactory;
use NetglueRealIP\Helper\ClientIPFromSuperGlobals;
use NetglueRealIPTest\TestCase;
use Psr\Container\ContainerInterface;

class ClientIPFromSuperGlobalsFactoryTest extends TestCase
{
    public function testConfigProviderHasReasonableDefaults()
    {
        $config = (new ConfigProvider())();
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);

        $factory = new ClientIPFromSuperGlobalsFactory();
        $helper = $factory($container->reveal());
        $this->assertInstanceOf(ClientIPFromSuperGlobals::class, $helper);
    }
}
