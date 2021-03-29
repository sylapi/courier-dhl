<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Contracts\CourierMakeSender;
use Sylapi\Courier\Contracts\Sender;

class DhlCourierMakeSender implements CourierMakeSender
{
    public function makeSender(): Sender
    {
        return new DhlSender();
    }
}
