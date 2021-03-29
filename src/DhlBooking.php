<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Abstracts\Booking;

class DhlBooking extends Booking
{
    public function validate(): bool
    {
        return true;
    }
}
