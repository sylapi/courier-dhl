<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Abstracts\Parcel;

class DhlParcel extends Parcel
{
    public function validate(): bool
    {
        return true;
    }
}
