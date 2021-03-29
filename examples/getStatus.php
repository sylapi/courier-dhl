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
 * GetStatus.
 */
try {
    $response = $courier->getStatus('123456');
    if ($response->hasErrors()) {
        var_dump($response->getFirstError()->getMessage());
    } else {
        var_dump((string) $response);
    }
} catch (\Exception $e) {
    var_dump($e->getMessage());
}
