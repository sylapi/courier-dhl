<?php

namespace Sylapi\Courier\Dhl\Tests\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Courier;
use Sylapi\Courier\Dhl\DhlBooking;
use Sylapi\Courier\Dhl\DhlCourierApiFactory;
use Sylapi\Courier\Dhl\DhlParameters;
use Sylapi\Courier\Dhl\DhlParcel;
use Sylapi\Courier\Dhl\DhlReceiver;
use Sylapi\Courier\Dhl\DhlSender;
use Sylapi\Courier\Dhl\DhlSession;
use Sylapi\Courier\Dhl\DhlSessionFactory;
use Sylapi\Courier\Dhl\DhlShipment;

class DhlCourierApiFactoryTest extends PHPUnitTestCase
{
    private $parameters = [
        'login'           => 'login',
        'password'        => 'password',
        'sandbox'         => true,
        'labelType'       => 'one_label_on_a4_rt_pdf',
    ];

    public function testDhlSessionFactory()
    {
        $DhlSessionFactory = new DhlSessionFactory();
        $DhlSession = $DhlSessionFactory->session(
            DhlParameters::create($this->parameters)
        );
        $this->assertInstanceOf(DhlSession::class, $DhlSession);
    }

    public function testCourierFactoryCreate()
    {
        $DhlCourierApiFactory = new DhlCourierApiFactory(new DhlSessionFactory());
        $courier = $DhlCourierApiFactory->create($this->parameters);

        $this->assertInstanceOf(Courier::class, $courier);
        $this->assertInstanceOf(DhlBooking::class, $courier->makeBooking());
        $this->assertInstanceOf(DhlParcel::class, $courier->makeParcel());
        $this->assertInstanceOf(DhlReceiver::class, $courier->makeReceiver());
        $this->assertInstanceOf(DhlSender::class, $courier->makeSender());
        $this->assertInstanceOf(DhlShipment::class, $courier->makeShipment());
    }
}
