<?php

declare(strict_types=1);

namespace NetglueRealIP\Helper;

use Psr\Http\Message\ServerRequestInterface;

class ClientIPFromPsrServerRequest extends ClientIP
{
    /** @var ServerRequestInterface */
    private $request;

    public function __invoke(ServerRequestInterface $request): ?string
    {
        $this->request = $request;

        return $this->getIpAddress();
    }

    public function getRemoteAddress(): ?string
    {
        $server = $this->request->getServerParams();

        return isset($server['REMOTE_ADDR'])
            ? $this->filterIp($server['REMOTE_ADDR'])
            : null;
    }

    protected function getHeaderValue(string $headerName): ?string
    {
        $headerValue = $this->request->getHeaderLine($headerName);

        return empty($headerValue) ? null : $headerValue;
    }
}
