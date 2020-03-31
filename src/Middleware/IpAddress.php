<?php
declare(strict_types=1);

namespace NetglueRealIP\Middleware;

use NetglueRealIP\Helper\ClientIPFromPsrServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class IpAddress implements MiddlewareInterface
{
    public const ATTRIBUTE = 'ip_address';

    /** @var ClientIPFromPsrServerRequest */
    private $helper;

    /** @var string */
    private $attribute;

    public function __construct(
        ClientIPFromPsrServerRequest $helper,
        string $requestAttribute = self::ATTRIBUTE
    ) {
        $this->helper = $helper;
        $this->attribute = $requestAttribute;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $ip = ($this->helper)($request);
        $request = $request->withAttribute($this->attribute, $ip);

        return $handler->handle($request);
    }
}
