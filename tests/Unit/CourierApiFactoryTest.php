<?php

namespace Sylapi\Courier\Dhl\Tests\Unit;

use Sylapi\Courier\Courier;
use Sylapi\Courier\Dhl\Session;
use Sylapi\Courier\Dhl\SessionFactory;
use Sylapi\Courier\Dhl\Entities\Parcel;
use Sylapi\Courier\Dhl\Entities\Sender;
use Sylapi\Courier\Dhl\Entities\Booking;
use Sylapi\Courier\Dhl\CourierApiFactory;
use Sylapi\Courier\Dhl\Entities\Receiver;
use Sylapi\Courier\Dhl\Entities\Shipment;
use Sylapi\Courier\Dhl\Entities\Credentials;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;


class CourierApiFactoryTest extends PHPUnitTestCase
{

    public function testSessionFactory()
    {
        $credentials = new Credentials();
        $credentials->setLogin('login');
        $credentials->setPassword('password');
        $credentials->setSandbox(true);

        $sessionFactory = new SessionFactory();
        $session = $sessionFactory->session(
            $credentials
        );
        $this->assertInstanceOf(Session::class, $session);
    }

    public function testCourierFactoryCreate()
    {
        $credentials = [
            'login' => 'login',
            'password' => 'password',
            'sandbox' => true,
        ];

        $courierApiFactory = new CourierApiFactory(new SessionFactory());
        $courier = $courierApiFactory->create($credentials);

        $this->assertInstanceOf(Courier::class, $courier);
        $this->assertInstanceOf(Booking::class, $courier->makeBooking());
        $this->assertInstanceOf(Parcel::class, $courier->makeParcel());
        $this->assertInstanceOf(Receiver::class, $courier->makeReceiver());
        $this->assertInstanceOf(Sender::class, $courier->makeSender());
        $this->assertInstanceOf(Shipment::class, $courier->makeShipment());
    }
}
