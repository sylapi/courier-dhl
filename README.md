# Courier-dhl

![StyleCI](https://github.styleci.io/repos/261400599/shield?style=flat&style=flat) ![PHPStan](https://img.shields.io/badge/PHPStan-level%205-brightgreen.svg?style=flat) [![Build](https://github.com/sylapi/courier-dhl/actions/workflows/build.yaml/badge.svg?event=push)](https://github.com/sylapi/courier-dhl/actions/workflows/build.yaml) [![codecov.io](https://codecov.io/github/sylapi/courier-dhl/coverage.svg)](https://codecov.io/github/sylapi/courier-dhl/)

## Methody

### Init

```php
    /**
    * @return Sylapi\Courier\Courier
    */
    $courier = CourierFactory::create('Dhl',[
        'login'     => 'mylogin',
        'password'  => 'mypassword',
        'sandbox'   => true,
        'labelType' => 'LBLP',
        'pickupDate' => '2021-04-01',
        'pickupTimeFrom' => '10:00',
        'pickupTimeTo' => '16:00',
        'paymentMethod' => 'BANK_TRANSFER',
        'payerType' => 'SHIPPER',
        'accountNumber' => '6000000',
        'parcelType' => 'PACKAGE',
        'service' => [
            'deliveryEvening' => true
        ]
    ]);

```

### CreateShipment

```php
    $sender = $courier->makeSender();
    $sender->setFullName('Nazwa Firmy/Nadawca')
        ->setStreet('Ulica')
        ->setHouseNumber('2a')
        ->setApartmentNumber('1')
        ->setCity('Miasto')
        ->setZipCode('66100')
        ->setCountry('Poland')
        ->setCountryCode('pl')
        ->setContactPerson('Jan Kowalski')
        ->setEmail('my@email.com')
        ->setPhone('48500600700');

    $receiver = $courier->makeReceiver();

    $receiver->setFirstName('Jan')
        ->setSurname('Nowak')
        ->setStreet('Ulica')
        ->setHouseNumber('15')
        ->setApartmentNumber('1896')
        ->setCity('Miasto')
        ->setZipCode('70200')
        ->setCountry('Poland')
        ->setCountryCode('pl')
        ->setContactPerson('Jan Kowalski')
        ->setEmail('login@email.com')
        ->setPhone('48500600700');

    $parcel = $courier->makeParcel();
    $parcel->setWeight(1.5)
        ->setHeight(10)
        ->setWidth(10)
        ->setLength(10);

    $shipment = $courier->makeShipment();
    $shipment->setSender($sender)
            ->setReceiver($receiver)
            ->setParcel($parcel)
            ->setContent('Zawartość przesyłki');

    try {
        $response = $courier->createShipment($shipment);
        if($response->hasErrors()) {
            var_dump($response->getFirstError()->getMessage());
        } else {
            var_dump($response->shipmentId); // Zewnetrzny idetyfikator zamowienia
        }

    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }
```

### PostShipment

```php
    /**
     * Init Courier
     */
    $booking = $courier->makeBooking();
    $booking->setShipmentId('123456');
    try {
        $response = $courier->postShipment($booking);
        if($response->hasErrors()) {
            var_dump($response->getFirstError()->getMessage());
        } else {
            var_dump($response->shipmentId); // Zewnetrzny idetyfikator zamowienia
            var_dump($response->trackingId); // Zewnetrzny idetyfikator sledzenia przesylki
        }
    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }
```

### GetStatus

```php
    /**
     * Init Courier
     */
    try {
        $response = $courier->getStatus('123456');
        if($response->hasErrors()) {
            var_dump($response->getFirstError()->getMessage());
        } else {
            var_dump((string) $response);
        }
    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }
```

### GetLabel

```php
    try {
        $response = $courier->getLabel('123456');
        if($response->hasErrors()) {
            var_dump($response->getFirstError()->getMessage());
        } else {
            var_dump((string) $response);
        }
    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }
```

## ENUMS

### labelType

| WARTOŚĆ | OPIS |
| ------ | ------ |
| LP | list przewozowy |
| BLP | etykieta BLP |
| LBLP | etykieta BLP w formacie PDF A4 |
| ZBLP | etykieta BLP w formacie dla drukarek Zebra |

### PayerType

| WARTOŚĆ | OPIS |
| ------ | ------ |
| SHIPPER | Płaci nadawca |
| RECEIVER | Płaci odbiorca |

### PaymentMethod

| WARTOŚĆ | OPIS |
| ------ | ------ |
| CASH | Gotówka |
| BANK_TRANSFER | Przelew |

## Komendy

| KOMENDA | OPIS |
| ------ | ------ |
| composer tests | Testy |
| composer phpstan |  PHPStan |
| composer coverage | PHPUnit Coverage |
| composer coverage-html | PHPUnit Coverage HTML (DIR: ./coverage/) |
