<?php
declare(strict_types=1);

namespace NetglueRealIPTest\Middleware;

use NetglueRealIP\Container\Middleware\IpAddressFactory;
use NetglueRealIP\Helper\ClientIPFromPsrServerRequest;
use NetglueRealIP\Middleware\IpAddress;
use NetglueRealIPTest\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class IpAddressTest extends TestCase
{
    private $serverArray;

    private $request;

    public function setUp()
    {
        parent::setUp();
        $this->serverArray = $_SERVER;
        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';

        $this->request = ServerRequestFactory::fromGlobals($_SERVER);
    }

    public function tearDown()
    {
        parent::tearDown();
        $_SERVER = $this->serverArray;
    }

    public function testBasic()
    {
        $helper = new ClientIPFromPsrServerRequest();
        $middleware = new IpAddress($helper);
        $handler = (new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $response->getBody()->write($request->getAttribute('ip_address'));
                return $response;
            }
        });
        $response = $middleware->process($this->request, $handler);
        $this->assertSame('1.1.1.1', (string) $response->getBody());
    }

    public function testAttributeCanBeRenamed()
    {
        $helper = new ClientIPFromPsrServerRequest();
        $middleware = new IpAddress($helper, 'Whatever');
        $handler = (new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $response->getBody()->write($request->getAttribute('Whatever'));
                return $response;
            }
        });
        $response = $middleware->process($this->request, $handler);
        $this->assertSame('1.1.1.1', (string) $response->getBody());
    }

    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(ClientIPFromPsrServerRequest::class)->willReturn(new ClientIPFromPsrServerRequest);

        $factory = new IpAddressFactory();
        $middleware = $factory($container->reveal());
        $this->assertInstanceOf(IpAddress::class, $middleware);
    }
}
