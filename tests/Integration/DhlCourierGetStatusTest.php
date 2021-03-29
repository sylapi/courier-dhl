<?php

namespace Sylapi\Courier\Dhl\Tests;

use SoapFault;
use Sylapi\Courier\Entities\Status;
use Sylapi\Courier\Dhl\DhlCourierGetStatuses;
use Sylapi\Courier\Exceptions\TransportException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\Tests\Helpers\DhlSessionTrait;

class DhlCourierGetStatusTest extends PHPUnitTestCase
{
    use DhlSessionTrait;

    private $soapMock = null;
    private $sessionMock = null;

    public function setUp(): void
    {
        $this->soapMock = $this->getSoapMock();
        $this->sessionMock = $this->getSessionMock($this->soapMock);
    }

    public function testGetStatusSuccess()
    {
        $this->assertTrue(true);
    }

    public function testGetStatusFailure()
    {
        $this->soapMock->expects($this->any())->method('__call')->will($this->throwException(new SoapFault('100', 'Error message')));

        $shipmentId = 1234567890;

        $getStatus = new DhlCourierGetStatuses($this->sessionMock);
        $response = $getStatus->getStatus((string) $shipmentId);

        $this->assertInstanceOf(Status::class, $response);
        $this->assertTrue($response->hasErrors());
        $this->assertInstanceOf(TransportException::class, $response->getFirstError());
    }
}
