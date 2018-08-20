<?php
declare(strict_types=1);

namespace NetglueRealIP\Helper;

use function str_replace;
use function strpos;
use function strtoupper;

class ClientIPFromSuperGlobals extends ClientIP
{

    /**
     * @var array Effectively $_SERVER
     */
    public $serverArray;

    public function __invoke(?array $serverArray = null) :? string
    {
        $this->serverArray = empty($serverArray) ? $_SERVER : $serverArray;
        return $this->getIpAddress();
    }

    public function getRemoteAddress() :? string
    {
        return isset($this->serverArray['REMOTE_ADDR'])
            ? $this->filterIp($this->serverArray['REMOTE_ADDR'])
            : null;
    }

    protected function getHeaderValue(string $headerName) :? string
    {
        $headerName = $this->normaliseHeaderFromSuperGlobal($headerName);
        return isset($this->serverArray[$headerName])
            ? $this->serverArray[$headerName]
            : null;
    }

    private function normaliseHeaderFromSuperGlobal(string $header) : string
    {
        $header = strtoupper($header);
        $header = str_replace('-', '_', $header);
        if (0 !== strpos($header, 'HTTP_')) {
            $header = 'HTTP_' . $header;
        }
        return $header;
    }
}
