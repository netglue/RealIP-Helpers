<?php
declare(strict_types=1);

namespace NetglueGeoIPTest\Helper;

use NetglueRealIP\Helper\ClientIPFromSuperGlobals;
use NetglueRealIPTest\TestCase;

class ClientIPFromSuperGlobalsTest extends TestCase
{
    private static $preservedServer;


    public function tearDown()
    {
        parent::tearDown();
        $_SERVER = self::$preservedServer;
    }

    public function setUp()
    {
        parent::setUp();
        self::$preservedServer = $_SERVER;
        $_SERVER = [
            'REMOTE_ADDR' => '1.1.1.1'
        ];
    }

    public function testNullReturnedWhenThereIsNoRemoteAddr()
    {
        unset($_SERVER['REMOTE_ADDR']);
        $helper = new ClientIPFromSuperGlobals;
        $this->assertNull($helper());
    }

    public function testRemoteAddressIsReturnedByDefault()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '3.3.3.3, 2.2.2.2';
        $helper = new ClientIPFromSuperGlobals;
        $this->assertSame('1.1.1.1', $helper());
    }

    public function testRemoteAddressPortIsStripped()
    {
        $_SERVER['REMOTE_ADDR'] = '1.1.1.1:80';
        $helper = new ClientIPFromSuperGlobals;
        $this->assertSame('1.1.1.1', $helper());
    }

    public function testInvalidRemoteAddressIsNull()
    {
        $_SERVER['REMOTE_ADDR'] = 'whatever';
        $helper = new ClientIPFromSuperGlobals;
        $this->assertNull($helper());
    }

    public function testTrustedHeaderTrumpsRemoteAddr()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '3.3.3.3, 2.2.2.2';
        $_SERVER['HTTP_CF_CONNECTING_IP'] = '4.4.4.4';
        $helper = new ClientIPFromSuperGlobals(false, 'CF-Connecting-IP');
        $this->assertSame('4.4.4.4', $helper());
    }

    public function testProxyModeReturnsLeftMostIpWithNoTrustedProxies()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '3.3.3.3, 2.2.2.2';
        $helper = new ClientIPFromSuperGlobals(true);
        $this->assertSame('3.3.3.3', $helper());
    }

    public function testProxyModeReturnsRightMostIpWithTrustedProxies()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '3.3.3.3, 2.2.2.2';
        $helper = new ClientIPFromSuperGlobals(true, null, true);
        $this->assertSame('2.2.2.2', $helper());
    }

    public function testRemoteAddrIsReturnedWhenThereAreNoProxyHeaders()
    {
        $helper = new ClientIPFromSuperGlobals(true);
        $this->assertSame('1.1.1.1', $helper());
    }

    public function testProxyHeadersOverrideDefaults()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '3.3.3.3, 2.2.2.2';
        $_SERVER['HTTP_X_FOO'] = '9.9.9.9';
        $helper = new ClientIPFromSuperGlobals(true, null, false, null, ['x-foo']);
        $this->assertSame('9.9.9.9', $helper());
    }

    public function testInvalidIpsAreExcludedFromHeaders()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = 'foo, 3.3.3.3, bunnies, 2.2.2.2, 5.5.5';
        $helper = new ClientIPFromSuperGlobals(true);
        $this->assertSame('3.3.3.3', $helper());
    }

    public function testIPV4PortNumberIsRemoved()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '3.3.3.3:1234, 2.2.2.2:1234';
        $helper = new ClientIPFromSuperGlobals(true);
        $this->assertSame('3.3.3.3', $helper());
    }

    public function testIPV6PortNumberIsRemoved()
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '[2606:4700:4700::1111]:1234, [2606:4700:4700::1001]:1234';
        $helper = new ClientIPFromSuperGlobals(true);
        $this->assertSame('2606:4700:4700::1111', $helper());
    }

    public function fwdForDataProvider() : array
    {
        return [
            ['for="_gazonk"', '1.1.1.1'],
            ['For="[2001:db8:cafe::17]:4711"', '2001:db8:cafe::17'],
            ['for=192.0.2.60;proto=http;by=203.0.113.43', '192.0.2.60'],
            ['for=192.0.2.43, for=198.51.100.17', '192.0.2.43'],
            ['for=192.0.2.43,for=198.51.100.17;by=203.0.113.60;proto=http;host=example.com', '192.0.2.43'],
        ];
    }

    /**
     * @dataProvider fwdForDataProvider
     * @param string $header
     * @param string $expect
     */
    public function testForwardedHeaderIP(string $header, string $expect)
    {
        $_SERVER['HTTP_FORWARDED'] = $header;
        $helper = new ClientIPFromSuperGlobals(true);
        $this->assertSame($expect, $helper());
    }

    public function trustedDataProvider() : array
    {
        return [
            ['9.9.9.9, 5.5.5.5, 3.3.3.3, 4.4.4.4', '9.9.9.9'],
            ['9.9.9.9, 5.5.5.5, 7.7.7.7, 4.4.4.4', '7.7.7.7'],
        ];
    }

    /**
     * @dataProvider trustedDataProvider
     * @param string $header
     * @param string $expect
     */
    public function testTrustedProxies(string $header, string $expect)
    {
        $trusted = [
            '3.3.3.3',
            '4.4.4.4',
            '5.5.5.5',
        ];
        $_SERVER['HTTP_X_FORWARDED_FOR'] = $header;
        $helper = new ClientIPFromSuperGlobals(true, null, true, $trusted);
        $this->assertSame($expect, $helper());
    }

    /**
     * @dataProvider trustedDataProvider
     * @param string $header
     */
    public function testRemoteAddrIsClientWhenNotATrustedProxy(string $header)
    {
        $expect = '1.1.1.1';
        $trusted = [
            '3.3.3.3',
            '4.4.4.4',
            '5.5.5.5',
        ];
        $_SERVER['HTTP_X_FORWARDED_FOR'] = $header;
        $helper = new ClientIPFromSuperGlobals(true, null, false, $trusted);
        $this->assertSame($expect, $helper());
    }
}
