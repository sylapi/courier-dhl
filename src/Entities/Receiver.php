<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl\Entities;

use Sylapi\Courier\Abstracts\Receiver as ReceiverAbstract;

class Receiver extends ReceiverAbstract
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
