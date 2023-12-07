<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl\Enums;

enum PaymentType :string {
    case PAYMENT_CASH = 'CASH';
    case PAYMENT_BANK_TRANSFER = 'BANK_TRANSFER';
    case PAYER_USER = 'USER';
    case PAYER_SHIPPER = 'SHIPPER';
    case PAYER_RECEIVER = 'RECEIVER';
}