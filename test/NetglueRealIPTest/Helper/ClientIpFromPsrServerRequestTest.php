<?php
declare(strict_types=1);

namespace NetglueRealIPTest\Helper;

use Laminas\Diactoros\ServerRequestFactory;
use NetglueRealIP\Helper\ClientIPFromPsrServerRequest;
use NetglueRealIPTest\TestCase;

class ClientIpFromPsrServerRequestTest extends TestCase
{
    public function testGetRemoteAddress() : void
    {
        $request = ServerRequestFactory::fromGlobals(['REMOTE_ADDR' => '1.1.1.1']);
        $helper = new ClientIPFromPsrServerRequest();
        $this->assertSame('1.1.1.1', $helper($request));
    }

    public function testPortIsStrippedFromRemoteAddress() : void
    {
        $request = ServerRequestFactory::fromGlobals(['REMOTE_ADDR' => '1.1.1.1:1234']);
        $helper = new ClientIPFromPsrServerRequest();
        $this->assertSame('1.1.1.1', $helper($request));
    }

    public function testHeaderRetrieval() : void
    {
        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '1.1.1.1:1234',
            'HTTP_X_FORWARDED_FOR' => '3.3.3.3, 2.2.2.2',
        ]);
        $helper = new ClientIPFromPsrServerRequest(true);
        $this->assertSame('3.3.3.3', $helper($request));
    }
}
