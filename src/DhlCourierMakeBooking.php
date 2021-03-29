<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Contracts\Booking;
use Sylapi\Courier\Contracts\CourierMakeBooking;

class DhlCourierMakeBooking implements CourierMakeBooking
{
    public function makeBooking(): Booking
    {
        return new DhlBooking();
    }
}
