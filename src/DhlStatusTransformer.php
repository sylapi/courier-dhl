<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Abstracts\StatusTransformer;
use Sylapi\Courier\Enums\StatusType;

class DhlStatusTransformer extends StatusTransformer
{
    /**
     * @var array<string, string>
     */
    public $statuses = [
        'DWP' => StatusType::SENT, // przesyłka odebrana od nadawcy
        'SORT' => StatusType::WAREHOUSE_ENTRY, // przesyłka jest obsługiwana w centrum sortowania
        'LP' => StatusType::ENTRY_WAIT, // przesyłka dotarła do oddziału              
        'LK' => StatusType::PROCESSING, // przesyłka przekazana kurierowi do doręczenia            
        'BRG' => StatusType::SOLVING, // doręczenie wstrzymane do czasu uregulowania opłat przez odbiorcę            
        'AWI' => StatusType::PROCESSING_FAILED, // próba doręczenia zakończona niepowodzeniem. Odbiorcy nie było w domu w momencie doręczenia przesyłki
        'AN' => StatusType::PROCESSING_FAILED, // przesyłka błędnie zaadresowana. Prosimy o kontakt z naszym Działem Obsługi Klienta
        'DOR' => StatusType::DELIVERED, // przesyłka doręczona do odbiorcy        
        'ZWN' => StatusType::RETURNED, // przesyłka zwrócona nadawcy
        'OP' => StatusType::PROCESSING_FAILED, // odbiorca odmówił przyjęcia przesyłki       
    ];
}
