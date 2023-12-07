<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl\Entities;

use Sylapi\Courier\Abstracts\Parcel as ParcelAbstract;

class Parcel extends ParcelAbstract
{
    public function validate(): bool
    {
        return true;
    }
}
