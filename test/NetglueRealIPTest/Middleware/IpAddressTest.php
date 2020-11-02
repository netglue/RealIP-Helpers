<?php

declare(strict_types=1);

namespace NetglueRealIPTest\Middleware;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use NetglueRealIP\Container\Middleware\IpAddressFactory;
use NetglueRealIP\Helper\ClientIPFromPsrServerRequest;
use NetglueRealIP\Middleware\IpAddress;
use NetglueRealIPTest\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class IpAddressTest extends TestCase
{
    /** @var mixed[] */
    private $serverArray;

    /** @var ServerRequest */
    private $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverArray = $_SERVER;
        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';

        $this->request = ServerRequestFactory::fromGlobals($_SERVER);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_SERVER = $this->serverArray;
    }

    public function testBasic(): void
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
        self::assertSame('1.1.1.1', (string) $response->getBody());
    }

    public function testAttributeCanBeRenamed(): void
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
        self::assertSame('1.1.1.1', (string) $response->getBody());
    }

    public function testFactory(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects(self::once())
            ->method('get')
            ->with(ClientIPFromPsrServerRequest::class)
            ->willReturn(new ClientIPFromPsrServerRequest());

        $factory = new IpAddressFactory();
        $middleware = $factory($container);
        self::assertInstanceOf(IpAddress::class, $middleware);
    }
}
