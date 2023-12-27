<?php

namespace Sylapi\Courier\Dhl\Tests;

use SoapFault;
use Sylapi\Courier\Entities\Status;
use Sylapi\Courier\Dhl\CourierGetStatuses;
use Sylapi\Courier\Dhl\DhlCourierGetStatuses;
use Sylapi\Courier\Exceptions\TransportException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\Tests\Helpers\SessionTrait;

class DhlCourierGetStatusTest extends PHPUnitTestCase
{
    use SessionTrait;

    private $soapMock = null;
    private $sessionMock = null;

    public function setUp(): void
    {
        $this->soapMock = $this->getSoapMock();
        $this->sessionMock = $this->getSessionMock($this->soapMock);
    }

    public function testGetStatusSuccess()
    {
        $localXml =  json_decode(json_encode(simplexml_load_string(file_get_contents(__DIR__.'/Mock/getStatusSuccess.xml'))));
        $this->soapMock->expects($this->any())->method('__call')->will($this->returnValue($localXml));
        
        $shipmentId = 1234567890;

        $getStatus = new CourierGetStatuses($this->sessionMock);
        $response = $getStatus->getStatus((string) $shipmentId);
        $this->assertEquals('returned', $response);
    }

    public function testGetStatusFailure()
    {
        $this->soapMock->expects($this->any())->method('__call')->will($this->throwException(new SoapFault('100', 'Error message')));

        $this->expectException(TransportException::class);

        $shipmentId = 1234567890;

        $getStatus = new CourierGetStatuses($this->sessionMock);
        $response = $getStatus->getStatus((string) $shipmentId);
    }
}
