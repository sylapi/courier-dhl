<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl\Entities;

use Sylapi\Courier\Abstracts\Receiver as ReceiverAbstract;

class Receiver extends ReceiverAbstract
{
    public function getZipCode(): ?string
    {
        return (is_null(parent::getZipCode()))
            ? null
            : preg_replace('/[^A-Za-z0-9]/', '', parent::getZipCode());
    }

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
