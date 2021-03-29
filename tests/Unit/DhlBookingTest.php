<?php

namespace Sylapi\Courier\Dhl\Tests\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\DhlBooking;
class DhlBookingTest extends PHPUnitTestCase
{
    public function testBookingTestValidate()
    {
        $booking = new DhlBooking();
        $this->assertIsBool($booking->validate());
    }
}
