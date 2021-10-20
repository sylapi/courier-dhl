<?php

namespace Sylapi\Courier\Dhl\Tests;

use SoapFault;
use Sylapi\Courier\Dhl\DhlParcel;
use Sylapi\Courier\Dhl\DhlSender;
use Sylapi\Courier\Dhl\DhlReceiver;
use Sylapi\Courier\Dhl\DhlShipment;
use Sylapi\Courier\Entities\Response;
use Sylapi\Courier\Dhl\DhlCourierCreateShipment;
use Sylapi\Courier\Exceptions\TransportException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\Tests\Helpers\DhlSessionTrait;

class DhlCourierCreateShipmentTest extends PHPUnitTestCase
{
    use DhlSessionTrait;

    private $soapMock = null;
    private $sessionMock = null;

    public function setUp(): void
    {
        $this->soapMock = $this->getSoapMock();
        $this->sessionMock = $this->getSessionMock($this->soapMock);
    }

    private function getShipmentMock()
    {
        $senderMock = $this->createMock(DhlSender::class);
        $receiverMock = $this->createMock(DhlReceiver::class);
        $parcelMock = $this->createMock(DhlParcel::class);
        $shipmentMock = $this->createMock(DhlShipment::class);

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
        
        $createShipment = new DhlCourierCreateShipment($this->sessionMock);
        $response = $createShipment->createShipment($this->getShipmentMock());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertObjectHasAttribute('shipmentId', $response);
        $this->assertNotEmpty($response->shipmentId);
    }

    public function testCreateShipmentFailure()
    {
        $this->soapMock->expects($this->any())->method('__call')->will($this->throwException(new SoapFault('106', 'Błędy walidacji przesyłki: Rodzaj płatności spoza zakresu słownikowego (CASH, BANK_TRANSFER)')));
        $createShipment = new DhlCourierCreateShipment($this->sessionMock);
        $response = $createShipment->createShipment($this->getShipmentMock());
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertObjectNotHasAttribute('shipmentId', $response);
        $this->assertTrue($response->hasErrors());
        $this->assertInstanceOf(TransportException::class, $response->getFirstError());
    }
}
