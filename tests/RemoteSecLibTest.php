<?php

use Mockery as m;

class RemoteSecLibGatewayTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testHostAndPortSetCorrectly()
    {
        $gateway = $this->getGateway();
        $this->assertEquals('127.0.0.1', $gateway->getHost());
        $this->assertEquals(22, $gateway->getPort());
    }

    public function testHostAndPortSetCorrectlyWithoutPort()
    {
        $gateway = $this->getGateway('127.0.0.1');
        $this->assertEquals('127.0.0.1', $gateway->getHost());
        $this->assertEquals(22, $gateway->getPort());
    }

    public function testHostAndPortSetCorrectlyNonstandardPort()
    {
        $gateway = $this->getGateway('127.0.0.1:2222');
        $this->assertEquals('127.0.0.1', $gateway->getHost());
        $this->assertEquals(2222, $gateway->getPort());
    }

    public function testHostAndPortSetCorrectlyWithIpv6()
    {
        $gateway = $this->getGateway('3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f:22');
        $this->assertEquals('[3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f]', $gateway->getHost());
        $this->assertEquals(22, $gateway->getPort());
    }

    public function testHostAndPortSetCorrectlyWithIpv6InBrackets()
    {
        $gateway = $this->getGateway('[3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f]:22');
        $this->assertEquals('[3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f]', $gateway->getHost());
        $this->assertEquals(22, $gateway->getPort());
    }

    public function testHostAndPortSetCorrectlyWithIpv6WithoutPort()
    {
        $gateway = $this->getGateway('3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f');
        $this->assertEquals('[3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f]', $gateway->getHost());
        $this->assertEquals(22, $gateway->getPort());
    }

    public function testHostAndPortSetCorrectlyWithIpv6InBracketsWithoutPort()
    {
        $gateway = $this->getGateway('[3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f]');
        $this->assertEquals('[3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f]', $gateway->getHost());
        $this->assertEquals(22, $gateway->getPort());
    }

    public function testHostAndPortSetCorrectlyWithIpv6NonstandardPort()
    {
        $gateway = $this->getGateway('3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f:2222');
        $this->assertEquals('[3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f]', $gateway->getHost());
        $this->assertEquals(2222, $gateway->getPort());
    }

    public function testHostAndPortSetCorrectlyWithIpv6InBracketsNonstandardPort()
    {
        $gateway = $this->getGateway('[3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f]:2222');
        $this->assertEquals('[3b71:2304:142e:cd5e:4ab3:439b:bc29:b59f]', $gateway->getHost());
        $this->assertEquals(2222, $gateway->getPort());
    }

    public function testConnectProperlyCallsLoginWithAuth()
    {
        $gateway = $this->getGateway();
        $gateway->shouldReceive('getNewKey')->andReturn($key = m::mock('StdClass'));
        $key->shouldReceive('setPassword')->once()->with('keyphrase');
        $key->shouldReceive('loadKey')->once()->with('keystuff');
        $gateway->getConnection()->shouldReceive('login')->with('taylor', $key);
        $gateway->connect('taylor');
    }

    public function testKeyTextCanBeSetManually()
    {
        $files = m::mock('Illuminate\Filesystem\Filesystem');
        $gateway = m::mock('Collective\Remote\SecLibGateway', [
                '127.0.0.1:22',
                ['username' => 'taylor', 'keytext' => 'keystuff'],
                $files,
                10,
            ])->makePartial();
        $gateway->shouldReceive('getConnection')->andReturn(m::mock('StdClass'));
        $gateway->shouldReceive('getNewKey')->andReturn($key = m::mock('StdClass'));
        $key->shouldReceive('setPassword')->once()->with(null);
        $key->shouldReceive('loadKey')->once()->with('keystuff');
        $gateway->getConnection()->shouldReceive('login')->with('taylor', $key);
        $gateway->connect('taylor');
    }

    public function getGateway($host = '127.0.0.1:22')
    {
        $files = m::mock('Illuminate\Filesystem\Filesystem');
        $files->shouldReceive('get')->with('keypath')->andReturn('keystuff');
        $gateway = m::mock('Collective\Remote\SecLibGateway', [
                $host,
                ['username' => 'taylor', 'key' => 'keypath', 'keyphrase' => 'keyphrase'],
                $files,
                10,
            ])->makePartial();
        $gateway->shouldReceive('getConnection')->andReturn(m::mock('StdClass'));

        return $gateway;
    }
}
