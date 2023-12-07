<?php

namespace Sylapi\Courier\Dhl\Tests\Helpers;

use Sylapi\Courier\Dhl\DhlParameters;
use Sylapi\Courier\Dhl\Session;

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
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->method('client')
            ->willReturn($soapMock);
        $sessionMock->method('parameters')
            ->willReturn(DhlParameters::create([]));

        return $sessionMock;
    }
}
