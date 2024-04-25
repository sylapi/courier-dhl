<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl\Entities;

use Sylapi\Courier\Abstracts\Sender as SenderAbstract;

class Sender extends SenderAbstract
{
    public function getZipCode(): ?string
    {
        return (is_null(parent::getZipCode()))
            ? null
            : preg_replace('/[^A-Za-z0-9]/', '', parent::getZipCode());
    }

    public function validate(): bool
    {
        return true;
    }
}
