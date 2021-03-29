<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Abstracts\Enum;

class DhlPaymentType extends Enum
{
    const PAYMENT_CASH = 'CASH';
    const PAYMENT_BANK_TRANSFER = 'BANK_TRANSFER';
    const PAYER_USER = 'USER';
    const PAYER_SHIPPER = 'SHIPPER';
    const PAYER_RECEIVER = 'RECEIVER';
}