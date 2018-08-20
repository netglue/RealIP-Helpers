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

    private $serverArray;

    public function setUp()
    {
        parent::setUp();
        $this->serverArray = $_SERVER;
        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';
    }

    public function tearDown()
    {
        parent::tearDown();
        $_SERVER = $this->serverArray;
    }

    public function testBasicOperation()
    {
        $helper = new ClientIPFromSuperGlobals();
        $plugin = new ClientIP($helper);
        $this->assertSame('1.1.1.1', $plugin());
    }

    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(ClientIPFromSuperGlobals::class)->willReturn(new ClientIPFromSuperGlobals());
        $factory = new ClientIpPluginFactory();
        $plugin = $factory($container->reveal());
        $this->assertInstanceOf(ClientIP::class, $plugin);
    }
}
