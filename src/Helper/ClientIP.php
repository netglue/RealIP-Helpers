<?php
declare(strict_types=1);

namespace NetglueRealIP\Helper;

use function array_diff;
use function array_filter;
use function array_map;
use function count;
use function end;
use function explode;
use function filter_var;
use function in_array;
use function reset;
use function strpos;
use function strtolower;
use function substr;
use function trim;

abstract class ClientIP
{
    /** @var bool Whether to evaluate proxy headers or not */
    private $proxyMode = false;

    /** @var string|null A header that we’re trusting to contain the real client IP */
    private $trustedHeader;

    /** @var array An array of trusted proxy IP addresses */
    private $trustedProxies = [];

    /** @var bool Whether the Remote Address is guaranteed to be a trusted proxy */
    private $remoteAddressIsTrustedProxy = false;

    /** @var array */
    private $proxyHeadersToInspect = [
        'Forwarded',
        'X-Forwarded-For',
        'X-Forwarded',
        'X-Cluster-Client-Ip',
        'Client-Ip',
    ];

    public function __construct(
        bool $proxyMode = false,
        ?string $trustedHeader = null,
        bool $remoteAddressIsTrustedProxy = false,
        ?array $trustedProxies = null,
        ?array $proxyHeadersToInspect = null
    ) {
        $this->proxyMode     = $proxyMode;
        $this->trustedHeader = $trustedHeader;
        $this->remoteAddressIsTrustedProxy = $remoteAddressIsTrustedProxy;
        if (! empty($trustedProxies)) {
            $this->trustedProxies = $trustedProxies;
        }
        if (! empty($proxyHeadersToInspect)) {
            $this->proxyHeadersToInspect = $proxyHeadersToInspect;
        }
    }

    public function getIpAddress() :? string
    {
        // The trusted header trumps everything:
        $ip = $this->getTrustedHeader();
        if ($ip) {
            return $ip;
        }
        // Just return REMOTE_ADDR if proxies are not to be checked
        $remote = $this->getRemoteAddress();
        if (! $this->proxyMode) {
            return $remote;
        }

        $remoteAddr = $this->getRemoteAddress();
        if ($this->remoteAddressIsTrustedProxy && ! empty($remoteAddr)) {
            $this->trustedProxies[] = $remoteAddr;
        }

        // If REMOTE_ADDR is not a trusted proxy, it's the client
        if (count($this->trustedProxies) && ! in_array($remote, $this->trustedProxies)) {
            return $remote;
        }

        return $this->searchProxyHeaders();
    }

    abstract public function getRemoteAddress() :? string;

    abstract protected function getHeaderValue(string $headerName) :? string;

    private function searchProxyHeaders() :? string
    {
        foreach ($this->proxyHeadersToInspect as $headerName) {
            $ips = $this->proxyHeaderToArray($headerName);
            if (! empty($ips) && count($this->trustedProxies)) {
                // The client IP is the left-most address, but when the all trusted proxies are removed,
                // the most trusted source of information would be the right most.
                $ips = array_diff($ips, $this->trustedProxies);
                return end($ips);
            }
            // There are no trusted proxies, so assume the left-most is the client
            if (! empty($ips)) {
                return reset($ips);
            }
        }
        return $this->getRemoteAddress();
    }

    private function getTrustedHeader() :? string
    {
        if ($this->trustedHeader) {
            $value = $this->getHeaderValue($this->trustedHeader);
            if ($value) {
                return $this->filterIp($value);
            }
        }
        return null;
    }

    public function filterIp(string $ip) :? string
    {
        $ip = $this->removePort($ip);
        if (! $this->validateIp($ip)) {
            return null;
        }
        return $ip;
    }

    private function proxyHeaderToArray(string $headerName) : array
    {
        $headerValue = $this->getHeaderValue($headerName);
        if (! $headerValue) {
            return [];
        }

        $items = array_map('trim', explode(',', $headerValue));

        if (strtolower($headerName) === 'forwarded') {
            foreach (explode(';', $headerValue) as $headerPart) {
                if (strtolower(substr($headerPart, 0, 4)) === 'for=') {
                    $items = explode(',', $headerPart);
                    $items = array_map(function ($value) {
                        // IPv6 is quoted: For="[2001:db8:cafe::17]:4711"
                        return trim(trim(substr($value, 4)), '"');
                    }, $items);
                    break;
                }
            }
        }

        $items = array_map([$this, 'filterIp'], $items);

        return array_filter($items);
    }

    private function removePort(string $ipAddress) : string
    {
        if (strpos($ipAddress, ']:') !== false) {
            $parts = explode(']', $ipAddress);
            $parts[0] = trim($parts[0], '[');
            if (filter_var($parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
                return $parts[0];
            }
        }
        $parts = explode(':', $ipAddress);
        if (count($parts) == 2) {
            if (filter_var($parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
                return $parts[0];
            }
        }

        return $ipAddress;
    }

    private function validateIp(string $ip) : bool
    {
        $flags = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6;
        return ! (filter_var($ip, FILTER_VALIDATE_IP, $flags) === false);
    }
}
