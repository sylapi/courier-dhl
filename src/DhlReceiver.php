<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Abstracts\Receiver;

class DhlReceiver extends Receiver
{
    public function getCountryCode(): ?string
    {    
        return (is_null(parent::getCountryCode()))
            ? null
            : strtoupper(parent::getCountryCode());
    }

    public function validate(): bool
    {
        return true;
    }
}
