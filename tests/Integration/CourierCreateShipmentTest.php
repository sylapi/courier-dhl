<?php

namespace Sylapi\Courier\Dhl\Tests;

use SoapFault;
use Sylapi\Courier\Dhl\Entities\Parcel;
use Sylapi\Courier\Dhl\Entities\Sender;
use Sylapi\Courier\Dhl\Entities\Receiver;
use Sylapi\Courier\Dhl\Entities\Shipment;
use Sylapi\Courier\Dhl\Responses\Shipment as ResponsesShipment;
use Sylapi\Courier\Dhl\CourierCreateShipment;
use Sylapi\Courier\Exceptions\TransportException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\Entities\Options;
use Sylapi\Courier\Dhl\Tests\Helpers\SessionTrait;

class CourierCreateShipmentTest extends PHPUnitTestCase
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
        $optionsMock = $this->createMock(Options::class);

        $shipmentMock->method('getSender')
                ->willReturn($senderMock);

        $shipmentMock->method('getReceiver')
                ->willReturn($receiverMock);

        $shipmentMock->method('getParcel')
                ->willReturn($parcelMock);

        $shipmentMock->method('getOptions')
                ->willReturn($optionsMock);                

        return $shipmentMock;
    }

    public function testCreateShipmentSuccess()
    {
        $localXml = simplexml_load_string(file_get_contents(__DIR__.'/Mock/createShipmentsSuccess.xml'));
        $this->soapMock->expects($this->any())->method('__call')->will($this->returnValue($localXml));
        
        $createShipment = new CourierCreateShipment($this->sessionMock);
        $response = $createShipment->createShipment($this->getShipmentMock());

        $this->assertInstanceOf(ResponsesShipment::class, $response);
        $this->assertNotEmpty($response->getShipmentId());
    }

    public function testCreateShipmentFailure()
    {
        $this->expectException(TransportException::class);

        $this->soapMock->expects($this->any())->method('__call')->will($this->throwException(new SoapFault('106', 'Błędy walidacji przesyłki: Rodzaj płatności spoza zakresu słownikowego (CASH, BANK_TRANSFER)')));
        $createShipment = new CourierCreateShipment($this->sessionMock);
        $response = $createShipment->createShipment($this->getShipmentMock());
    }
}
