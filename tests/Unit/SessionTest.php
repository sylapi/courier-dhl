<?php

namespace Sylapi\Courier\Dhl\Tests\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\DhlParameters;
use Sylapi\Courier\Dhl\Session;
use SoapClient;
use authData;

class SessionTest extends PHPUnitTestCase
{
    public function testDhlSessionParameters()
    {
        $dhlSession = new Session(DhlParameters::create([
            'apiUrl' => __DIR__.'/../Helpers/test.wsdl',
        ]));
        $this->assertInstanceOf(DhlParameters::class, $dhlSession->parameters());
        $this->assertInstanceOf(SoapClient::class, $dhlSession->client());
        $this->assertInstanceOf(authData::class, $dhlSession->getAuthData());
    }
}
