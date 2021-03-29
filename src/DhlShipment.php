<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Abstracts\Shipment;

class DhlShipment extends Shipment
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
