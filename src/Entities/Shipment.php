<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl\Entities;

use Sylapi\Courier\Abstracts\Shipment as ShipmentAbstract;

class Shipment extends ShipmentAbstract
{
    public function getQuantity(): int
    {
        return 1;
    }

    public function validate(): bool
    {
        return true;
    }
}
