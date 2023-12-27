<?php

namespace Sylapi\Courier\Dhl\Tests\Integration;

use SoapFault;
use Sylapi\Courier\Dhl\Entities\Booking;
use Sylapi\Courier\Dhl\CourierPostShipment;
use Sylapi\Courier\Exceptions\TransportException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\Tests\Helpers\SessionTrait;
use Sylapi\Courier\Dhl\Responses\Shipment as ResponsesShipment;

class CourierPostShipmentTest extends PHPUnitTestCase
{
    use SessionTrait;

    private $soapMock = null;
    private $sessionMock = null;

    public function setUp(): void
    {
        $this->soapMock = $this->getSoapMock();
        $this->sessionMock = $this->getSessionMock($this->soapMock);
    }

    private function getBookingMock(int $shipmentId)
    {
        $shipmentMock = $this->createMock(Booking::class);

        $shipmentMock->method('getShipmentId')
                ->willReturn($shipmentId);

        return $shipmentMock;
    }    

    public function testPostShipmentSuccess()
    {
        $localXml = simplexml_load_string(file_get_contents(__DIR__.'/Mock/bookCourierSuccess.xml'));
        $this->soapMock->expects($this->any())->method('__call')->will($this->returnValue($localXml));
        
        $shipmentId = 1234567890;

        $postShipment = new CourierPostShipment($this->sessionMock);
        $response = $postShipment->postShipment($this->getBookingMock($shipmentId));


        $this->assertInstanceOf(ResponsesShipment::class, $response);
        $this->assertEquals($shipmentId, $response->getShipmentId());
    }

    public function testPostShipmentFailure()
    {
        $this->soapMock->expects($this->any())->method('__call')->will($this->throwException(new SoapFault('100', 'Error message')));

        $shipmentId = 1234567890;
        $this->expectException(TransportException::class);

        $createShipment = new CourierPostShipment($this->sessionMock);
        $createShipment->postShipment($this->getBookingMock($shipmentId));
    }
}
