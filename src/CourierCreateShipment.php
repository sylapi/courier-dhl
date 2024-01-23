<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use ship;
use address;
use billing;
use contact;
use shipper;
use receiver;
use shipment;
use Exception;

use SoapFault;
use addressData;
use paymentData;
use shipmentInfo;
use shipmentTime;
use createShipments;
use pieceDefinition;
use specialServices;
use ShipmentFullData;
use serviceDefinition;
use createShipmentReturn;

use Sylapi\Courier\Dhl\Enums\PaymentType;
use Sylapi\Courier\Exceptions\TransportException;
use Sylapi\Courier\Contracts\Shipment as ShipmentContract;
use Sylapi\Courier\Contracts\CourierCreateShipment as CourierCreateShipmentContract;
use Sylapi\Courier\Dhl\Responses\Shipment as ShipmentResponse;
use Sylapi\Courier\Dhl\Entities\Options;
use Sylapi\Courier\Dhl\Services\Product;
use Sylapi\Courier\Responses\Shipment as ResponseShipment;



class CourierCreateShipment implements CourierCreateShipmentContract
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function createShipment(ShipmentContract $shipment): ResponseShipment
    {
        
        $response = new ShipmentResponse();

        try {
            if($shipment->getOptions()->get('isShipmentReturn') ?? false) {
                $response = $this->shipmentReturnRequest($shipment);
            } else {
                $response = $this->shipmentRequest($shipment);
            }

        } catch (SoapFault $fault) {
            throw new TransportException($fault->faultstring, (int) $fault->faultcode);
        } catch (Exception $e) {
            throw new TransportException($e->getMessage(), $e->getCode());
        }
        
        return $response;
    }

    private function shipmentReturnRequest(ShipmentContract $shipment): ResponseShipment
    {
        $response = new ShipmentResponse();

        $client = $this->session->client();
        
        $request = $this->getShipmentReturn($shipment);

        $response->setRequest($request);

        $result = $client->createShipmentReturn($request);

        $response->setResponse($result);

        $shipmentId = $result->createShipmentReturnResult->item->shipmentNotificationNumber	?? null;
        if(!$shipmentId) {
            throw new TransportException('Shipment ID or tracking ID does not exist in response.');
        }
        
        $labelData = $result->createShipmentReturnResult->item->label->labelContent ?? null;
        if(!$labelData) {
            throw new TransportException('Label content does not exist in response.');
        }

        $storage = $this->session->storage();
        $storage->setLabel($labelData);

        $response->setTrackingId((string) $shipmentId);
        $response->setShipmentId((string) $shipmentId);

        return $response;
    }

    private function shipmentRequest(ShipmentContract $shipment): ResponseShipment
    {
        $response = new ShipmentResponse();

        $client = $this->session->client();
        
        $request = $this->getShipment($shipment);
        $response->setRequest($request);

        $result = $client->createShipments($request);
        $response->setResponse($result);

        $shipmentId = $result->createShipmentsResult->item->shipmentId ?? null;

        if(!$shipmentId) {
            throw new TransportException('Shipment ID does not exist in response.');
        }

        $response->setTrackingId((string) $shipmentId);
        $response->setShipmentId((string) $shipmentId);

        return $response;
    }    


    public function getShipmentReturn(ShipmentContract $shipment)
    {
        /**
         * @var Options $options
         */
        $options = $shipment->getOptions();

        $billing = new billing();
        $billing->shippingPaymentType = PaymentType::PAYER_RECEIVER->value;
        $billing->billingAccountNumber = $options->get('AccountNumber');
        $billing->paymentType = $options->get('paymentMethod');
        // $billing->costsCenter = '';

        $shipmentReturnParameters =  $options->get('shipmentReturn');

        $specialServices = null;
        if($shipmentReturnParameters['serviceType'] && $shipmentReturnParameters['serviceValue']) {
            $specialServices = new specialServices();
            $specialServices->serviceType = $shipmentReturnParameters['serviceType'];
            $specialServices->serviceValue = $shipmentReturnParameters['serviceValue'];
        }

        if(!$shipmentReturnParameters['labelExpDate']) {
            throw new \InvalidArgumentException('Options shipmentReturn[labelExpDate] is required.');
        }
        
        $shipmentTime = new shipmentTime();
        $shipmentTime->labelExpDate = $shipmentReturnParameters['labelExpDate'];

        $shipmentInfo = new shipmentInfo();
        // $shipmentInfo->waybill = '';
        $shipmentInfo->serviceType = $options->getShipmentReturnService();
        $shipmentInfo->bookCourier = ($shipmentReturnParameters['bookCourier'] && $shipmentReturnParameters['bookCourier'] === true);
        $shipmentInfo->billing = $billing;
        if($specialServices) {
            $shipmentInfo->specialServices = [ 
                'item' => $specialServices
            ];
        }
        $shipmentInfo->shipmentTime = $shipmentTime;
        $shipmentInfo->labelType =  $options->get('labelType', $options::DEFAULT_LABEL_TYPE);

        //Sender
        $preaviso = new contact();
        $preaviso->personName = $shipment->getSender()->getContactPerson();
        $preaviso->phoneNumber = $shipment->getSender()->getPhone();
        $preaviso->emailAddress = $shipment->getSender()->getEmail();

        $contact = new contact();
        $contact->personName = $shipment->getSender()->getContactPerson();
        $contact->phoneNumber = $shipment->getSender()->getPhone();
        $contact->emailAddress =$shipment->getSender()->getEmail();

        $address = new address();
        $address->country = $shipment->getSender()->getCountryCode();
        $address->name = $shipment->getSender()->getFullName();
        $address->postalCode = $shipment->getSender()->getZipCode();
        $address->city = $shipment->getSender()->getCity();
        $address->street = $shipment->getSender()->getStreet();
        $address->houseNumber = $shipment->getSender()->getHouseNumber();
        $address->apartmentNumber = $shipment->getSender()->getApartmentNumber();

        $shipper = new shipper();
        $shipper->preaviso = $preaviso;
        $shipper->contact = $contact;
        $shipper->address = $address;

        // Receiver
        $preaviso = new contact();
        $preaviso->personName = $shipment->getReceiver()->getContactPerson();
        $preaviso->phoneNumber = $shipment->getReceiver()->getPhone();
        $preaviso->emailAddress = $shipment->getReceiver()->getEmail();

        $contact = new contact();
        $contact->personName = $shipment->getReceiver()->getContactPerson();
        $contact->phoneNumber = $shipment->getReceiver()->getPhone();
        $contact->emailAddress =$shipment->getReceiver()->getEmail();

        $address = new address();
        $address->country = $shipment->getReceiver()->getCountryCode();
        $address->name = $shipment->getReceiver()->getFullName();
        $address->postalCode = $shipment->getReceiver()->getZipCode();
        $address->city = $shipment->getReceiver()->getCity();
        $address->street = $shipment->getReceiver()->getStreet();
        $address->houseNumber = $shipment->getReceiver()->getHouseNumber();
        $address->apartmentNumber = $shipment->getReceiver()->getApartmentNumber();
    
        $receiver = new receiver();
        $receiver->preaviso = $preaviso;
        $receiver->contact = $contact;
        $receiver->address = $address;

        $ship = new ship();
        $ship->shipper = $shipper;
        $ship->receiver = $receiver;

        $pieceDefinition = new pieceDefinition();
        $pieceDefinition->type = $options->get('parcelType', $options::DEFAULT_PARCEL_TYPE);
        $pieceDefinition->width = $shipment->getParcel()->getWidth();
        $pieceDefinition->height = $shipment->getParcel()->getHeight();
        $pieceDefinition->length = $shipment->getParcel()->getLength();
        $pieceDefinition->weight = $shipment->getParcel()->getWeight();
        $pieceDefinition->quantity = $shipment->getQuantity();
        $pieceDefinition->nonStandard = false;

        $shipmentReturn = new shipment();
        $shipmentReturn->shipmentInfo = $shipmentInfo;
        $shipmentReturn->content = $shipment->getContent();
        // $shipment->comment = '';
        // $shipment->reference = '';
        // $shipment->primaryWaybillNumber = '';
        $shipmentReturn->ship = $ship;
        $shipmentReturn->pieceList = [
            'item' => $pieceDefinition
        ];

        $createShipmentReturn = new createShipmentReturn();
        $createShipmentReturn->authData = $this->session->getAuthData();
        $createShipmentReturn->shipment =  $shipmentReturn;

        return $createShipmentReturn;
    }

    private function getShipment(ShipmentContract $shipment): createShipments
    {
        /**
         * @var Options $options
         */
        $options = $shipment->getOptions();

        $sender = new addressData();
        $sender->name = $shipment->getSender()->getFullName();
        $sender->postalCode = $shipment->getSender()->getZipCode();
        $sender->city = $shipment->getSender()->getCity();
        $sender->street = $shipment->getSender()->getStreet();
        $sender->houseNumber = $shipment->getSender()->getHouseNumber();
        $sender->apartmentNumber = $shipment->getSender()->getApartmentNumber();
        $sender->contactPerson = $shipment->getSender()->getContactPerson();
        $sender->contactPhone = $shipment->getSender()->getPhone();
        $sender->contactEmail = $shipment->getSender()->getEmail();

        $receiver = new addressData();
        $receiver->addressType = $options->get('addressType', $options::DEFAULT_ADDRESS_TYPE);
        $receiver->country = $shipment->getReceiver()->getCountryCode();
        $receiver->name = $shipment->getReceiver()->getFullName();
        $receiver->postalCode = $shipment->getReceiver()->getZipCode();
        $receiver->city =  $shipment->getReceiver()->getCity();
        $receiver->street =  $shipment->getReceiver()->getStreet();
        $receiver->houseNumber =  $shipment->getReceiver()->getHouseNumber();
        $receiver->apartmentNumber = $shipment->getReceiver()->getApartmentNumber();
        $receiver->contactPerson =  $shipment->getReceiver()->getContactPerson();
        $receiver->contactPhone =  $shipment->getReceiver()->getPhone();
        $receiver->contactEmail =  $shipment->getReceiver()->getEmail();

        $pieceDefinition = new pieceDefinition();
        $pieceDefinition->type = $options->get('parcelType', $options::DEFAULT_PARCEL_TYPE);
        $pieceDefinition->width = $shipment->getParcel()->getWidth();
        $pieceDefinition->height = $shipment->getParcel()->getHeight();
        $pieceDefinition->length = $shipment->getParcel()->getLength();
        $pieceDefinition->weight = $shipment->getParcel()->getWeight();
        $pieceDefinition->quantity = $shipment->getQuantity();
        $pieceDefinition->nonStandard = false;

        $paymentData = new paymentData();
        $paymentData->paymentMethod = $options->get('paymentMethod');
        $paymentData->payerType = $options->get('payerType');
        $paymentData->accountNumber = $options->get('accountNumber');

        $serviceItems = $shipment->getServices();
        $serviceData = [];
        foreach ($serviceItems as $serviceItem) {
            $serviceData = array_merge($serviceData, $serviceItem->handle());   
        }

        if(!isset($serviceData['product'])) {
            $serviceData['product'] = Product::DEFAULT_SERVICE_PRODUCT;
        }
        
        $service = new serviceDefinition();
        foreach ($serviceData as $k => $v) {
            $service->{$k} = $v;
        }

        $shipmentFullData = new ShipmentFullData();
        $shipmentFullData->receiver = $receiver;
        $shipmentFullData->shipper = $sender;
        $shipmentFullData->pieceList = [
            'item' => $pieceDefinition
        ];
        $shipmentFullData->payment = $paymentData;
        $shipmentFullData->service = $service;
        $shipmentFullData->shipmentDate = $options->get('shipmentDate');
        $shipmentFullData->content = $shipment->getContent();
        $shipmentFullData->skipRestrictionCheck = false;

        $createShipment = new createShipments();
        $createShipment->authData = $this->session->getAuthData();
        $createShipment->shipments = ['item' => $shipmentFullData];

        return $createShipment;
    }
}
