<?php

namespace Sylapi\Courier\Dhl\Tests\Helpers;

use Sylapi\Courier\Dhl\DhlParameters;
use Sylapi\Courier\Dhl\DhlSession;

trait DhlSessionTrait
{
    private function getSoapMock()
    {
        return $this->getMockBuilder('SoapClient')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    private function getSessionMock($soapMock)
    {
        $sessionMock = $this->createMock(DhlSession::class);
        $sessionMock->method('client')
            ->willReturn($soapMock);
        $sessionMock->method('parameters')
            ->willReturn(DhlParameters::create([]));

        return $sessionMock;
    }
}
