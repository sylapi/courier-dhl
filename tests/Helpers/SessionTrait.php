<?php

namespace Sylapi\Courier\Dhl\Tests\Helpers;

use Sylapi\Courier\Dhl\Session;
use Sylapi\Courier\Dhl\DhlParameters;
use Sylapi\Courier\Dhl\Entities\Options;
use Sylapi\Courier\Dhl\Entities\Credentials;

trait SessionTrait
{
    private function getSoapMock()
    {
        return $this->getMockBuilder('SoapClient')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    private function getSessionMock($soapMock)
    {
        $credentials = new Credentials();
        $credentials
            ->setLogin('login')
            ->setPassword('password')
            ->setSandbox(true)
            ;

        $sessionMock = $this->createMock(Session::class);
        $sessionMock->method('client')
            ->willReturn($soapMock);

        return $sessionMock;
    }
}
