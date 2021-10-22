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
    'accountNumber' => '1000000',
    'parcelType' => 'PACKAGE',
    'service' => [
        'deliveryEvening' => true
    ]       
]);

/**
 * PostShipment.
 */
$booking = $courier->makeBooking();
$booking->setShipmentId('123456');

try {
    $response = $courier->postShipment($booking);
    if ($response->hasErrors()) {
        var_dump($response->getFirstError()->getMessage());
    } else {
        var_dump($response->shipmentId);
        var_dump($response->trackingId);
    }
} catch (\Exception $e) {
    var_dump($e->getMessage());
}
