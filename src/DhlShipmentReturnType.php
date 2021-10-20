<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Abstracts\Enum;

class DhlShipmentReturnType extends Enum
{
    const ZC = 'ZC';
    const ZK = 'ZK';
}