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
    public function testConfigProviderHasReasonableDefaults(): void
    {
        $config = (new ConfigProvider())();
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new ClientIPFromSuperGlobalsFactory();
        $helper = $factory($container);
        self::assertInstanceOf(ClientIPFromSuperGlobals::class, $helper);
    }
}
