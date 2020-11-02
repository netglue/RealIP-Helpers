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
    public function testConfigProviderHasReasonableDefaults(): void
    {
        $config = (new ConfigProvider())();
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new ClientIPFromPsrServerRequestFactory();
        $helper = $factory($container);
        self::assertInstanceOf(ClientIPFromPsrServerRequest::class, $helper);
    }
}
