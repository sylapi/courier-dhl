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
        'AN' => StatusType::PROCESSING_FAILED, // przesyłka błędnie zaadresowana. Prosimy o kontakt z naszym Działem Obsługi Klienta
        'AN_BO' => StatusType::PROCESSING_FAILED, // odmowa przyjęcia – niepoprawny adres odbiorcy
        'AN_RA' => StatusType::PROCESSING_FAILED, // przesyłka błędnie zaadresowana lub niepełny adres
        'AN_SAS' => StatusType::PROCESSING_FAILED, // adres odbiorcy alternatywnego (sąsiada) został błędnie określony. Jest niepełny lub nie znajduje się w bezpośrednim sąsiedztwie odbiorcy przesyłki
        'AWI' => StatusType::PROCESSING_FAILED, // próba doręczenia zakończona niepowodzeniem. Odbiorcy nie było w domu w momencie doręczenia przesyłki
        'AWI_CZAS' => StatusType::RETURNING, // zwrot przesyłki do nadawcy - czas oczekiwania na doręczenie
        'AWI_DP2' => StatusType::PROCESSING, // kurier DHL nie został odbiorcy pod wskazanym adresem. Doręczenie przesyłki nastąpi w umówionym z odbiorcą terminie
        'AWI_ONU' => StatusType::PROCESSING_FAILED, // upoważnionego odbiorcy nie było w domu w momencie doręczenia przesyłki
        'AWI_OZ' => StatusType::PROCESSING_FAILED, // brak dostępu do posesji lub osiedle zamknięte
        'AWI_ROD' => StatusType::PROCESSING_FAILED, // brak wymaganego dodatkowego dokumentu do ROD
        'AWI_SAS' => StatusType::PROCESSING_FAILED, // kurier DHL nie zastał odbiorcy alternatywnego (sąsiada) pod wskazanym adresem doręczenia
        'BRG' => StatusType::SOLVING, // doręczenie wstrzymane do czasu uregulowania opłat przez odbiorcę 
        'BRG_SAS' => StatusType::PROCESSING_FAILED, // odbiorca alternatywny (sąsiad) nie uiścił opłaty za transport i/lub towar
        'CC' => StatusType::NEW, // powstały trudności w procesowaniu przesyłki. Aby uzyskać szczegółowe informacje prosimy o kontakt z Działem Obsługi Klienta DHL Parcel
        'DOR' => StatusType::DELIVERED, // przesyłka doręczona do odbiorcy
        'DOR_LOK' => StatusType::DELIVERED, // przesyłka doręczona do ustalonej lokalizacji
        'DOR_OTH' => StatusType::DELIVERED, // przesyłka doręczona do innej osoby
        'DOR_OWL' => StatusType::DELIVERED, // przesyłka odebrana osobiście przez Odbiorcę w Sortowni / Punkcie Obsługi Klienta
        'DOR_POC' => StatusType::DELIVERED, // przesyłka doręczona do urzędu pocztowego
        'DOR_RDZ' => StatusType::DELIVERED, // przesyłka doręczona do członka rodziny
        'DOR_SAS' => StatusType::DELIVERED, // przesyłka została doręczona do wskazanego odbiorcy alternatywnego (sąsiada)
        'DWP' => StatusType::SENT, // przesyłka odebrana od nadawcy
        'EDWP' => StatusType::PROCESSING, // DHL otrzymał dane elektroniczne przesyłki. Informacje zostaną zaktualizowane po przekazaniu przez Nadawcę przesyłki do transportu
        'WEJPL' => StatusType::PROCESSING, // przesyłka dotarła do Polski
        'WYJPL' => StatusType::PROCESSING, // przesyłka opuściła Polskę
        'LK' => StatusType::PROCESSING, // przesyłka przekazana kurierowi do doręczenia
        'LP' => StatusType::ENTRY_WAIT, // przesyłka dotarła do oddziału
        'OP' => StatusType::PROCESSING_FAILED, // odbiorca odmówił przyjęcia przesyłki
        'OP_BWER' => StatusType::PROCESSING_FAILED, // odmowa przyjęcia - brak możliwości weryfikacji zawartości przed podpisem lub pobraniem COD
        'OP_COD' => StatusType::PROCESSING_FAILED, // odmowa przyjęcia - błędna kwota COD
        'OP_DO' => StatusType::RETURNING, // zwrot przesyłki do nadawcy - nieaktualny dowód osobisty
        'OP_DOK' => StatusType::RETURNED, // zwrot przesyłki do nadawcy - brak dodatkowych dok. do odebrania
        'OP_DUB' => StatusType::PROCESSING_FAILED, // odmowa przyjęcia - zdublowane zamówienie
        'OP_OTH' => StatusType::PROCESSING_FAILED, // odbiorca odmówił przyjęcia przesyłki
        'OP_PLA' => StatusType::PROCESSING_FAILED, // odmowa przyjęcia - błędny płatnik
        'OP_PNK' => StatusType::PROCESSING_FAILED, // odbiorca odmówił przyjęcia przesyłki
        'OP_REZ' => StatusType::PROCESSING_FAILED, // odmowa przyjęcia – rezygnacja
        'OP_ROD' => StatusType::PROCESSING_FAILED, // odmowa przyjęcia - niekompletne ROD
        'OP_SAS' => StatusType::PROCESSING_FAILED, // odbiorca alternatywny (sąsiad) odmówił przyjęcia przesyłki
        'OP_TNZ' => StatusType::PROCESSING_FAILED, // odmowa przyjęcia - towar nie zamawiany lub błędny
        'OP_UMO' => StatusType::PROCESSING_FAILED, // odmowa przyjęcia - błędy w umowie
        'OP_USP' => StatusType::PROCESSING_FAILED, // odbiorca odmówił przyjęcia przesyłki
        'OWL' => StatusType::PICKUP_READY, // przesyłka oczekuje na odbiór przez klienta w terminalu DHL
        'OWL_KP' => StatusType::PICKUP_READY, // klient został powiadomiony o terminie i miejscu odbioru przesyłki         
        'OWL_ONO' => StatusType::PICKUP_READY, // przesyłka oczekuje na odbiór przez klienta
        'OWL_PS_KP' => StatusType::PICKUP_READY, // klient został powiadomiony o terminie i miejscu odbioru przesyłki
        'OWL_PS_ONO' => StatusType::PICKUP_READY, // przesyłka oczekuje na odbiór przez klienta - Packstation
        'OWL_SP_KP' => StatusType::PICKUP_READY, // klient został powiadomiony o terminie i miejscu odbioru przesyłki / paczkomat pełny / do odbioru z service pointu
        'OWL_SP_ONO' => StatusType::PICKUP_READY, // przesyłka oczekuje na odbiór przez klienta - Service point
        'PNK' => StatusType::PROCESSING_FAILED, // przesyłka niekompletna
        'PNPT' => StatusType::PROCESSING, // decyzja Odbiorcy: nowa data doręczenia przesyłki
        'PO18' => StatusType::PROCESSING, // decyzja Odbiorcy: zmiana godzin doręczenia przesyłki
        'PPDOR' => StatusType::DELIVERED, // przesyłka została odebrana w Punkcie Obsługi Klienta DHL
        'PSHOP' => StatusType::PROCESSING, // decyzja Odbiorcy: przesyłka będzie oczekiwała na odbiór osobisty w DHL Parcelshop
        'REZ' => StatusType::RETURNING, // decyzja Odbiorcy: przesyłka zostanie zwrócona do Nadawcy
        'SAS' => StatusType::PROCESSING, // decyzja Odbiorcy: alternatywny adres doręczenia przesyłki, w przypadku braku Adresata
        'SOB' => StatusType::PROCESSING, // decyzja Odbiorcy: nowa data doręczenia przesyłki
        'SORT' => StatusType::WAREHOUSE_ENTRY, // przesyłka jest obsługiwana w centrum sortowania
        'SP_CN' => StatusType::PROCESSING_FAILED, // nadanie przesyłki z DHL Parcelshop zostało anulowane. Prosimy o kontakt z Nadawcą
        'SP_DSP' => StatusType::PICKUP_READY, // przesyłka oczekuje na odbiór w DHL Parcelshop
        'SP_DW' => StatusType::PROCESSING_FAILED, // doręczenie przesyłki do DHL Parcelshop zostało wstrzymane. Prosimy o kontakt z Działem Obsługi Klienta DHL Parcel 
        'SP_DWP' => StatusType::SENT, // nadana w DHL Parcelshop przesyłka oczekuje na odbiór przez kuriera DHL
        'SP_KP' => StatusType::SENT, // klient został powiadomiony o terminie i miejscu odbioru przesyłki z DHL Parcelshop
        'SP_KZW' => StatusType::RETURNING, // zwrot przesyłki do nadawcy - minął termin odbioru z DHL Parcelshop
        'SP_OP' => StatusType::RETURNING, // zwrot przesyłki do nadawcy - odbiorca odmówił przyjęcia przesyłki z DHL Parcelshop
        'SP_PU' => StatusType::PROCESSING, // przesyłka odebrana przez Kuriera DHL z DHL Parcelshop
        'SP_TO' => StatusType::RETURNING, // zwrot przesyłki do nadawcy - minął termin odbioru z DHL Parcelshop
        'SP_ZAM' => StatusType::PROCESSING_FAILED, // doręczenie przesyłki do DHL Parcelshop zostało wstrzymane. Prosimy o kontakt z Działem Obsługi Klienta DHL Parcel
        'SP_ZWR' => StatusType::RETURNING, // zwrot przesyłki do nadawcy - odbiorca odmówił przyjęcia przesyłki
        'TRM' => StatusType::PROCESSING, // przesyłka oczekuje na kolejny cykl doręczenia
        'TRM2' => StatusType::PROCESSING, // przesyłka dotarła do Terminala DHL. Doręczenie jej do odbiorcy planowane jest dzisiaj lub w umówionym z odbiorcą terminie
        'ZA' => StatusType::PROCESSING, // decyzja Odbiorcy: nowy adres doręczenia przesyłki
        'ZAIT' => StatusType::PROCESSING, // decyzja Odbiorcy: nowy adres doręczenia przesyłki
        'ZWN' => StatusType::RETURNED, // przesyłka zwrócona nadawcy
        'ZWR' => StatusType::RETURNING, // przesyłka zostanie zwrócona do nadawcy
        'ZWR_AN' => StatusType::RETURNING, // przesyłka będzie zwrócona do nadawcy, Adresat nieznany
        'ZWR_OP' => StatusType::RETURNING, // przesyłka będzie zwrócona do nadawcy. Doręczenie nie było możliwe
        'ZWR_ZB' => StatusType::RETURNED, // kurier DHL podjął dwie próby doręczenia przesyłki. Minął termin oczekiwania przesyłki na odbiór przez klienta w punkcie DHL. Przesyłka została zwrócona do nadawcy
        'ZWR_ZT' => StatusType::RETURNED, // kurier DHL podjął dwie próby doręczenia przesyłki. Minął termin oczekiwania przesyłki na odbiór przez klienta w punkcie DHL. Przesyłka została zwrócona do nadawcy po uprzednim potwierdzeniu telefonicznym
        'ZWW' => StatusType::PROCESSING_FAILED, // próba doręczenia zakończona niepowodzeniem. Przesyłka oczekuje na kolejny cykl doręczenia
    ];
}
