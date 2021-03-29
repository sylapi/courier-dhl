<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Contracts\CourierMakeShipment;
use Sylapi\Courier\Contracts\Shipment;

class DhlCourierMakeShipment implements CourierMakeShipment
{
    public function makeShipment(): Shipment
    {
        return new DhlShipment();
    }
}
