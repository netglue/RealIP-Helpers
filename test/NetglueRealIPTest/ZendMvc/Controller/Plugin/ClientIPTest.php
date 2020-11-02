<?php

declare(strict_types=1);

namespace NetglueRealIPTest\ZendMvc\Controller\Plugin;

use NetglueRealIP\Container\ZendMvc\Controller\Plugin\ClientIpPluginFactory;
use NetglueRealIP\Helper\ClientIPFromSuperGlobals;
use NetglueRealIP\ZendMvc\Controller\Plugin\ClientIP;
use NetglueRealIPTest\TestCase;
use Psr\Container\ContainerInterface;

class ClientIPTest extends TestCase
{
    /** @var mixed[] */
    private $serverArray;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverArray = $_SERVER;
        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_SERVER = $this->serverArray;
    }

    public function testBasicOperation(): void
    {
        $helper = new ClientIPFromSuperGlobals();
        $plugin = new ClientIP($helper);
        self::assertSame('1.1.1.1', $plugin());
    }

    public function testFactory(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(ClientIPFromSuperGlobals::class)
            ->willReturn(new ClientIPFromSuperGlobals());

        $factory = new ClientIpPluginFactory();
        $plugin = $factory($container);
        self::assertInstanceOf(ClientIP::class, $plugin);
    }
}
