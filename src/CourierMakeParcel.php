<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Contracts\CourierMakeParcel as CourierMakeParcelContract;
use Sylapi\Courier\Contracts\Parcel as ParcelContract;
use Sylapi\Courier\Dhl\Entities\Parcel;

class CourierMakeParcel implements CourierMakeParcelContract
{
    public function makeParcel(): ParcelContract
    {
        return new Parcel();
    }
}
