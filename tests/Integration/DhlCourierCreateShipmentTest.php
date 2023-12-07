<?php

namespace Sylapi\Courier\Dhl\Tests;

use SoapFault;
use Sylapi\Courier\Dhl\Entities\Parcel;
use Sylapi\Courier\Dhl\Entities\Sender;
use Sylapi\Courier\Dhl\Entities\Receiver;
use Sylapi\Courier\Dhl\Entities\Shipment;
use Sylapi\Courier\Entities\Response;
use Sylapi\Courier\Dhl\CourierCreateShipment;
use Sylapi\Courier\Exceptions\TransportException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\Tests\Helpers\SessionTrait;

class DhlCourierCreateShipmentTest extends PHPUnitTestCase
{
    use SessionTrait;

    private $soapMock = null;
    private $sessionMock = null;

    public function setUp(): void
    {
        $this->soapMock = $this->getSoapMock();
        $this->sessionMock = $this->getSessionMock($this->soapMock);
    }

    private function getShipmentMock()
    {
        $senderMock = $this->createMock(Sender::class);
        $receiverMock = $this->createMock(Receiver::class);
        $parcelMock = $this->createMock(Parcel::class);
        $shipmentMock = $this->createMock(Shipment::class);

        $shipmentMock->method('getSender')
                ->willReturn($senderMock);

        $shipmentMock->method('getReceiver')
                ->willReturn($receiverMock);

        $shipmentMock->method('getParcel')
                ->willReturn($parcelMock);

        return $shipmentMock;
    }

    public function testCreateShipmentSuccess()
    {
        $localXml = simplexml_load_string(file_get_contents(__DIR__.'/Mock/createShipmentsSuccess.xml'));
        $this->soapMock->expects($this->any())->method('__call')->will($this->returnValue($localXml));
        
        $createShipment = new CourierCreateShipment($this->sessionMock);
        $response = $createShipment->createShipment($this->getShipmentMock());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertObjectHasAttribute('shipmentId', $response);
        $this->assertNotEmpty($response->shipmentId);
    }

    public function testCreateShipmentFailure()
    {
        $this->soapMock->expects($this->any())->method('__call')->will($this->throwException(new SoapFault('106', 'Błędy walidacji przesyłki: Rodzaj płatności spoza zakresu słownikowego (CASH, BANK_TRANSFER)')));
        $createShipment = new CourierCreateShipment($this->sessionMock);
        $response = $createShipment->createShipment($this->getShipmentMock());
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertObjectNotHasAttribute('shipmentId', $response);
        $this->assertTrue($response->hasErrors());
        $this->assertInstanceOf(TransportException::class, $response->getFirstError());
    }
}
