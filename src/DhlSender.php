<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Abstracts\Sender;

class DhlSender extends Sender
{
    public function validate(): bool
    {
        return true;
    }
}
