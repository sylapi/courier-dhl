<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Contracts\CourierMakeReceiver;
use Sylapi\Courier\Contracts\Receiver;

class DhlCourierMakeReceiver implements CourierMakeReceiver
{
    public function makeReceiver(): Receiver
    {
        return new DhlReceiver();
    }
}
