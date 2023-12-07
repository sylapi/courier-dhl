<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl\Enums;

enum ParcelType :string {
    case ENVELOPE = 'ENVELOPE';
    case PACKAGE = 'PACKAGE';
    case PALLET = 'PALLET';
}