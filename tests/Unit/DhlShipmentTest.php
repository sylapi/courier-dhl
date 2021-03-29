<?php

namespace Sylapi\Courier\Dhl\Tests\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\DhlParcel;
use Sylapi\Courier\Dhl\DhlReceiver;
use Sylapi\Courier\Dhl\DhlSender;
use Sylapi\Courier\Dhl\DhlShipment;

class DhlShipmentTest extends PHPUnitTestCase
{
    public function testNumberOfPackagesIsAlwaysEqualTo1()
    {
        $parcel = new DhlParcel();
        $shipment = new DhlShipment();
        $shipment->setParcel($parcel);
        $shipment->setParcel($parcel);

        $this->assertEquals(1, $shipment->getQuantity());
    }

    public function testShipmentValidate()
    {
        $receiver = new DhlReceiver();
        $sender = new DhlSender();
        $parcel = new DhlParcel();

        $shipment = new DhlShipment();
        $shipment->setSender($sender)
            ->setReceiver($receiver)
            ->setParcel($parcel);

        $this->assertIsBool($shipment->validate());
        $this->assertIsBool($shipment->getReceiver()->validate());
        $this->assertIsBool($shipment->getSender()->validate());
        $this->assertIsBool($shipment->getParcel()->validate());
    }
}
