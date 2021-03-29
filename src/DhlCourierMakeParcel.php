<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Contracts\CourierMakeParcel;
use Sylapi\Courier\Contracts\Parcel;

class DhlCourierMakeParcel implements CourierMakeParcel
{
    public function makeParcel(): Parcel
    {
        return new DhlParcel();
    }
}
