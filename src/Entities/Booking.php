<?php

declare(strict_types=1);


namespace Sylapi\Courier\Dhl\Entities;

use Sylapi\Courier\Abstracts\Booking as BookingAbstract;

class Booking extends BookingAbstract
{
    public function validate(): bool
    {
        return true;
    }
}
