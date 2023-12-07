<?php

namespace Sylapi\Courier\Dhl\Tests\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\Entities\Booking;
class BookingTest extends PHPUnitTestCase
{
    public function testBookingTestValidate()
    {
        $booking = new Booking();
        $this->assertIsBool($booking->validate());
    }
}
