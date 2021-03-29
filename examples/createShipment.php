<?php

use Sylapi\Courier\CourierFactory;

$courier = CourierFactory::create('Dhl', [
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

/**
 * CreateShipment.
 */
$sender = $courier->makeSender();
$sender->setFullName('Nazwa Firmy/Nadawca')
    ->setStreet('Ulica')
    ->setHouseNumber('2a')
    ->setApartmentNumber('1')
    ->setCity('Miasto')
    ->setZipCode('66100')
    ->setCountry('Poland')
    ->setCountryCode('cz')
    ->setContactPerson('Jan Kowalski')
    ->setEmail('login@email.com')
    ->setPhone('48500600700');

$receiver = $courier->makeReceiver();
$receiver->setFirstName('Jan')
    ->setSurname('Nowak')
    ->setStreet('Vysoká')
    ->setHouseNumber('15')
    ->setApartmentNumber('1896')
    ->setCity('Ostrava')
    ->setZipCode('70200')
    ->setCountry('Czechy')
    ->setCountryCode('cz')
    ->setContactPerson('Jan Kowalski')
    ->setEmail('login@email.com')
    ->setPhone('48500600700');

$parcel = $courier->makeParcel();
$parcel->setWeight(2.5)
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
    if ($response->hasErrors()) {
        var_dump($response->getFirstError()->getMessage());
    } else {
        var_dump($response->shipmentId);
    }
} catch (\Exception $e) {
    var_dump($e->getMessage());
}
