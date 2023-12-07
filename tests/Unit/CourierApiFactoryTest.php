<?php

namespace Sylapi\Courier\Dhl\Tests\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Courier;
use Sylapi\Courier\Dhl\Entities\Booking;
use Sylapi\Courier\Dhl\CourierApiFactory;
use Sylapi\Courier\Dhl\DhlParameters;
use Sylapi\Courier\Dhl\Entities\Parcel;
use Sylapi\Courier\Dhl\Entities\Receiver;
use Sylapi\Courier\Dhl\Entities\Sender;
use Sylapi\Courier\Dhl\Entities\Shipment;
use Sylapi\Courier\Dhl\Session;
use Sylapi\Courier\Dhl\SessionFactory;


class CourierApiFactoryTest extends PHPUnitTestCase
{
    private $parameters = [
        'login'           => 'login',
        'password'        => 'password',
        'sandbox'         => true,
        'labelType'       => 'one_label_on_a4_rt_pdf',
    ];

    public function testSessionFactory()
    {
        $sessionFactory = new SessionFactory();
        $session = $sessionFactory->session(
            DhlParameters::create($this->parameters)
        );
        $this->assertInstanceOf(DhlSession::class, $session);
    }

    public function testCourierFactoryCreate()
    {
        $DhlCourierApiFactory = new CourierApiFactory(new SessionFactory());
        $courier = $DhlCourierApiFactory->create($this->parameters);

        $this->assertInstanceOf(Courier::class, $courier);
        $this->assertInstanceOf(Booking::class, $courier->makeBooking());
        $this->assertInstanceOf(Parcel::class, $courier->makeParcel());
        $this->assertInstanceOf(Receiver::class, $courier->makeReceiver());
        $this->assertInstanceOf(Sender::class, $courier->makeSender());
        $this->assertInstanceOf(Shipment::class, $courier->makeShipment());
    }
}
