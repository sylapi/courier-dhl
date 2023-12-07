<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl\Entities;

use Sylapi\Courier\Abstracts\Sender as SenderAbstract;

class Sender extends SenderAbstract
{
    public function validate(): bool
    {
        return true;
    }
}
