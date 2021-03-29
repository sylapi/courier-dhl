<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Abstracts\Enum;

class DhlParcelType extends Enum
{
    const ENVELOPE = 'ENVELOPE';
    const PACKAGE = 'PACKAGE';
    const PALLET = 'PALLET';
}