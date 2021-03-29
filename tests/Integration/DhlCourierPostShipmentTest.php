<?php

namespace Sylapi\Courier\Dhl\Tests\Integration;

use SoapFault;
use Sylapi\Courier\Dhl\DhlBooking;
use Sylapi\Courier\Entities\Response;
use Sylapi\Courier\Dhl\DhlCourierPostShipment;
use Sylapi\Courier\Exceptions\TransportException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\Tests\Helpers\DhlSessionTrait;

class DhlCourierPostShipmentTest extends PHPUnitTestCase
{
    use DhlSessionTrait;

    private $soapMock = null;
    private $sessionMock = null;

    public function setUp(): void
    {
        $this->soapMock = $this->getSoapMock();
        $this->sessionMock = $this->getSessionMock($this->soapMock);
    }

    private function getBookingMock(int $shipmentId)
    {
        $shipmentMock = $this->createMock(DhlBooking::class);

        $shipmentMock->method('getShipmentId')
                ->willReturn($shipmentId);

        return $shipmentMock;
    }    

    public function testPostShipmentSuccess()
    {
        $localXml = simplexml_load_string(file_get_contents(__DIR__.'/Mock/bookCourierSuccess.xml'));
        $this->soapMock->expects($this->any())->method('__call')->will($this->returnValue($localXml));
        
        $shipmentId = 1234567890;

        $postShipment = new DhlCourierPostShipment($this->sessionMock);
        $response = $postShipment->postShipment($this->getBookingMock($shipmentId));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertObjectHasAttribute('shipmentId', $response);
        $this->assertNotEmpty($response->shipmentId);
        $this->assertEquals($shipmentId, $response->shipmentId);
    }

    public function testPostShipmentFailure()
    {
        $this->soapMock->expects($this->any())->method('__call')->will($this->throwException(new SoapFault('100', 'Error message')));

        $shipmentId = 1234567890;

        $createShipment = new DhlCourierPostShipment($this->sessionMock);
        $response = $createShipment->postShipment($this->getBookingMock($shipmentId));
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertObjectNotHasAttribute('shipmentId', $response);
        $this->assertTrue($response->hasErrors());
        $this->assertInstanceOf(TransportException::class, $response->getFirstError());
    }
}
