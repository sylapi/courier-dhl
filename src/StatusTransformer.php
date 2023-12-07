<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Abstracts\StatusTransformer as StatusTransformerAbstract;
use Sylapi\Courier\Enums\StatusType;

class StatusTransformer extends StatusTransformerAbstract
{
    /**
     * @var array<string, string>
     */
    public $statuses = [
        'AN' => StatusType::PROCESSING_FAILED->value, // przesyłka błędnie zaadresowana. Prosimy o kontakt z naszym Działem Obsługi Klienta
        'AN_BO' => StatusType::PROCESSING_FAILED->value, // odmowa przyjęcia – niepoprawny adres odbiorcy
        'AN_RA' => StatusType::PROCESSING_FAILED->value, // przesyłka błędnie zaadresowana lub niepełny adres
        'AN_SAS' => StatusType::PROCESSING_FAILED->value, // adres odbiorcy alternatywnego (sąsiada) został błędnie określony. Jest niepełny lub nie znajduje się w bezpośrednim sąsiedztwie odbiorcy przesyłki
        'AWI' => StatusType::PROCESSING_FAILED->value, // próba doręczenia zakończona niepowodzeniem. Odbiorcy nie było w domu w momencie doręczenia przesyłki
        'AWI_CZAS' => StatusType::RETURNING->value, // zwrot przesyłki do nadawcy - czas oczekiwania na doręczenie
        'AWI_DP2' => StatusType::PROCESSING->value, // kurier DHL nie został odbiorcy pod wskazanym adresem. Doręczenie przesyłki nastąpi w umówionym z odbiorcą terminie
        'AWI_ONU' => StatusType::PROCESSING_FAILED->value, // upoważnionego odbiorcy nie było w domu w momencie doręczenia przesyłki
        'AWI_OZ' => StatusType::PROCESSING_FAILED->value, // brak dostępu do posesji lub osiedle zamknięte
        'AWI_ROD' => StatusType::PROCESSING_FAILED->value, // brak wymaganego dodatkowego dokumentu do ROD
        'AWI_SAS' => StatusType::PROCESSING_FAILED->value, // kurier DHL nie zastał odbiorcy alternatywnego (sąsiada) pod wskazanym adresem doręczenia
        'BRG' => StatusType::SOLVING->value, // doręczenie wstrzymane do czasu uregulowania opłat przez odbiorcę 
        'BRG_SAS' => StatusType::PROCESSING_FAILED->value, // odbiorca alternatywny (sąsiad) nie uiścił opłaty za transport i/lub towar
        'CC' => StatusType::NEW->value, // powstały trudności w procesowaniu przesyłki. Aby uzyskać szczegółowe informacje prosimy o kontakt z Działem Obsługi Klienta DHL Parcel
        'DOR' => StatusType::DELIVERED->value, // przesyłka doręczona do odbiorcy
        'DOR_LOK' => StatusType::DELIVERED->value, // przesyłka doręczona do ustalonej lokalizacji
        'DOR_OTH' => StatusType::DELIVERED->value, // przesyłka doręczona do innej osoby
        'DOR_OWL' => StatusType::DELIVERED->value, // przesyłka odebrana osobiście przez Odbiorcę w Sortowni / Punkcie Obsługi Klienta
        'DOR_POC' => StatusType::DELIVERED->value, // przesyłka doręczona do urzędu pocztowego
        'DOR_RDZ' => StatusType::DELIVERED->value, // przesyłka doręczona do członka rodziny
        'DOR_SAS' => StatusType::DELIVERED->value, // przesyłka została doręczona do wskazanego odbiorcy alternatywnego (sąsiada)
        'DWP' => StatusType::SENT->value, // przesyłka odebrana od nadawcy
        'EDWP' => StatusType::PROCESSING->value, // DHL otrzymał dane elektroniczne przesyłki. Informacje zostaną zaktualizowane po przekazaniu przez Nadawcę przesyłki do transportu
        'WEJPL' => StatusType::PROCESSING->value, // przesyłka dotarła do Polski
        'WYJPL' => StatusType::PROCESSING->value, // przesyłka opuściła Polskę
        'LK' => StatusType::PROCESSING->value, // przesyłka przekazana kurierowi do doręczenia
        'LP' => StatusType::ENTRY_WAIT->value, // przesyłka dotarła do oddziału
        'OP' => StatusType::PROCESSING_FAILED->value, // odbiorca odmówił przyjęcia przesyłki
        'OP_BWER' => StatusType::PROCESSING_FAILED->value, // odmowa przyjęcia - brak możliwości weryfikacji zawartości przed podpisem lub pobraniem COD
        'OP_COD' => StatusType::PROCESSING_FAILED->value, // odmowa przyjęcia - błędna kwota COD
        'OP_DO' => StatusType::RETURNING->value, // zwrot przesyłki do nadawcy - nieaktualny dowód osobisty
        'OP_DOK' => StatusType::RETURNED->value, // zwrot przesyłki do nadawcy - brak dodatkowych dok. do odebrania
        'OP_DUB' => StatusType::PROCESSING_FAILED->value, // odmowa przyjęcia - zdublowane zamówienie
        'OP_OTH' => StatusType::PROCESSING_FAILED->value, // odbiorca odmówił przyjęcia przesyłki
        'OP_PLA' => StatusType::PROCESSING_FAILED->value, // odmowa przyjęcia - błędny płatnik
        'OP_PNK' => StatusType::PROCESSING_FAILED->value, // odbiorca odmówił przyjęcia przesyłki
        'OP_REZ' => StatusType::PROCESSING_FAILED->value, // odmowa przyjęcia – rezygnacja
        'OP_ROD' => StatusType::PROCESSING_FAILED->value, // odmowa przyjęcia - niekompletne ROD
        'OP_SAS' => StatusType::PROCESSING_FAILED->value, // odbiorca alternatywny (sąsiad) odmówił przyjęcia przesyłki
        'OP_TNZ' => StatusType::PROCESSING_FAILED->value, // odmowa przyjęcia - towar nie zamawiany lub błędny
        'OP_UMO' => StatusType::PROCESSING_FAILED->value, // odmowa przyjęcia - błędy w umowie
        'OP_USP' => StatusType::PROCESSING_FAILED->value, // odbiorca odmówił przyjęcia przesyłki
        'OWL' => StatusType::PICKUP_READY->value, // przesyłka oczekuje na odbiór przez klienta w terminalu DHL
        'OWL_KP' => StatusType::PICKUP_READY->value, // klient został powiadomiony o terminie i miejscu odbioru przesyłki         
        'OWL_ONO' => StatusType::PICKUP_READY->value, // przesyłka oczekuje na odbiór przez klienta
        'OWL_PS_KP' => StatusType::PICKUP_READY->value, // klient został powiadomiony o terminie i miejscu odbioru przesyłki
        'OWL_PS_ONO' => StatusType::PICKUP_READY->value, // przesyłka oczekuje na odbiór przez klienta - Packstation
        'OWL_SP_KP' => StatusType::PICKUP_READY->value, // klient został powiadomiony o terminie i miejscu odbioru przesyłki / paczkomat pełny / do odbioru z service pointu
        'OWL_SP_ONO' => StatusType::PICKUP_READY->value, // przesyłka oczekuje na odbiór przez klienta - Service point
        'PNK' => StatusType::PROCESSING_FAILED->value, // przesyłka niekompletna
        'PNPT' => StatusType::PROCESSING->value, // decyzja Odbiorcy: nowa data doręczenia przesyłki
        'PO18' => StatusType::PROCESSING->value, // decyzja Odbiorcy: zmiana godzin doręczenia przesyłki
        'PPDOR' => StatusType::DELIVERED->value, // przesyłka została odebrana w Punkcie Obsługi Klienta DHL
        'PSHOP' => StatusType::PROCESSING->value, // decyzja Odbiorcy: przesyłka będzie oczekiwała na odbiór osobisty w DHL Parcelshop
        'REZ' => StatusType::RETURNING->value, // decyzja Odbiorcy: przesyłka zostanie zwrócona do Nadawcy
        'SAS' => StatusType::PROCESSING->value, // decyzja Odbiorcy: alternatywny adres doręczenia przesyłki, w przypadku braku Adresata
        'SOB' => StatusType::PROCESSING->value, // decyzja Odbiorcy: nowa data doręczenia przesyłki
        'SORT' => StatusType::WAREHOUSE_ENTRY->value, // przesyłka jest obsługiwana w centrum sortowania
        'SP_CN' => StatusType::PROCESSING_FAILED->value, // nadanie przesyłki z DHL Parcelshop zostało anulowane. Prosimy o kontakt z Nadawcą
        'SP_DSP' => StatusType::PICKUP_READY->value, // przesyłka oczekuje na odbiór w DHL Parcelshop
        'SP_DW' => StatusType::PROCESSING_FAILED->value, // doręczenie przesyłki do DHL Parcelshop zostało wstrzymane. Prosimy o kontakt z Działem Obsługi Klienta DHL Parcel 
        'SP_DWP' => StatusType::SENT->value, // nadana w DHL Parcelshop przesyłka oczekuje na odbiór przez kuriera DHL
        'SP_KP' => StatusType::SENT->value, // klient został powiadomiony o terminie i miejscu odbioru przesyłki z DHL Parcelshop
        'SP_KZW' => StatusType::RETURNING->value, // zwrot przesyłki do nadawcy - minął termin odbioru z DHL Parcelshop
        'SP_OP' => StatusType::RETURNING->value, // zwrot przesyłki do nadawcy - odbiorca odmówił przyjęcia przesyłki z DHL Parcelshop
        'SP_PU' => StatusType::PROCESSING->value, // przesyłka odebrana przez Kuriera DHL z DHL Parcelshop
        'SP_TO' => StatusType::RETURNING->value, // zwrot przesyłki do nadawcy - minął termin odbioru z DHL Parcelshop
        'SP_ZAM' => StatusType::PROCESSING_FAILED->value, // doręczenie przesyłki do DHL Parcelshop zostało wstrzymane. Prosimy o kontakt z Działem Obsługi Klienta DHL Parcel
        'SP_ZWR' => StatusType::RETURNING->value, // zwrot przesyłki do nadawcy - odbiorca odmówił przyjęcia przesyłki
        'TRM' => StatusType::PROCESSING->value, // przesyłka oczekuje na kolejny cykl doręczenia
        'TRM2' => StatusType::PROCESSING->value, // przesyłka dotarła do Terminala DHL. Doręczenie jej do odbiorcy planowane jest dzisiaj lub w umówionym z odbiorcą terminie
        'ZA' => StatusType::PROCESSING->value, // decyzja Odbiorcy: nowy adres doręczenia przesyłki
        'ZAIT' => StatusType::PROCESSING->value, // decyzja Odbiorcy: nowy adres doręczenia przesyłki
        'ZWN' => StatusType::RETURNED->value, // przesyłka zwrócona nadawcy
        'ZWR' => StatusType::RETURNING->value, // przesyłka zostanie zwrócona do nadawcy
        'ZWR_AN' => StatusType::RETURNING->value, // przesyłka będzie zwrócona do nadawcy, Adresat nieznany
        'ZWR_OP' => StatusType::RETURNING->value, // przesyłka będzie zwrócona do nadawcy. Doręczenie nie było możliwe
        'ZWR_ZB' => StatusType::RETURNED->value, // kurier DHL podjął dwie próby doręczenia przesyłki. Minął termin oczekiwania przesyłki na odbiór przez klienta w punkcie DHL. Przesyłka została zwrócona do nadawcy
        'ZWR_ZT' => StatusType::RETURNED->value, // kurier DHL podjął dwie próby doręczenia przesyłki. Minął termin oczekiwania przesyłki na odbiór przez klienta w punkcie DHL. Przesyłka została zwrócona do nadawcy po uprzednim potwierdzeniu telefonicznym
        'ZWW' => StatusType::PROCESSING_FAILED->value, // próba doręczenia zakończona niepowodzeniem. Przesyłka oczekuje na kolejny cykl doręczenia
    ];
}
