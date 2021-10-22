<?php

use Sylapi\Courier\CourierFactory;

$courier = CourierFactory::create('Dhl', [
    'login'     => 'mylogin',
    'password'  => 'mypassword',
    'sandbox'   => true,
    'labelType' => 'LBLP',
    'paymentMethod' => 'BANK_TRANSFER',
    'accountNumber' => '1000000',
    'parcelType' => 'PACKAGE',
    'isShipmentReturn' => true,
    'shipmentReturn' => [
        'serviceType' => 'UBEZP', // optional: insurance
        'serviceValue' => 2000, // optional: insurance value (float)
        'labelExpDate' => '2021-10-31',
        'shipmentReturnService' => 'ZK', // ZK or ZC
        'bookCourier' => false
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
    ->setCountryCode('PL')
    ->setContactPerson('Jan Kowalski')
    ->setEmail('login@email.com')
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
    ->setCountryCode('PL')
    ->setContactPerson('Jan Nowak')
    ->setEmail('my@email.com')
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
