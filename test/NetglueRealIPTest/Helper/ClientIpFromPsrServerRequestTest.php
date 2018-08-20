<?php
declare(strict_types=1);

namespace NetglueGeoIPTest\Helper;

use NetglueRealIP\Helper\ClientIPFromPsrServerRequest;
use NetglueRealIPTest\TestCase;
use Zend\Diactoros\ServerRequestFactory;

class ClientIpFromPsrServerRequestTest extends TestCase
{
    public function testGetRemoteAddress()
    {
        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '1.1.1.1',
        ]);
        $helper = new ClientIPFromPsrServerRequest;
        $this->assertSame('1.1.1.1', $helper($request));
    }

    public function testPortIsStrippedFromRemoteAddress()
    {
        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '1.1.1.1:1234',
        ]);
        $helper = new ClientIPFromPsrServerRequest;
        $this->assertSame('1.1.1.1', $helper($request));
    }

    public function testHeaderRetrieval()
    {
        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '1.1.1.1:1234',
            'HTTP_X_FORWARDED_FOR' => '3.3.3.3, 2.2.2.2',
        ]);
        $helper = new ClientIPFromPsrServerRequest(true);
        $this->assertSame('3.3.3.3', $helper($request));
    }
}
