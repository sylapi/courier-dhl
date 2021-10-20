<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Exception;
use SoapFault;
use addressData;
use paymentData;
use createShipments;
use pieceDefinition;
use ShipmentFullData;
use serviceDefinition;

use createShipmentReturn;
use shipment;
use shipmentTime;
use shipmentInfo;
use billing;
use contact;
use ship;
use shipper;
use address;
use receiver;
use specialServices;

use Sylapi\Courier\Entities\Response;
use Sylapi\Courier\Contracts\Shipment as ShipmentContract;
use Sylapi\Courier\Helpers\ResponseHelper;
use Sylapi\Courier\Exceptions\TransportException;
use Sylapi\Courier\Contracts\CourierCreateShipment;
use Sylapi\Courier\Contracts\Response as ResponseContract;
use Sylapi\Courier\Dhl\DhlPaymentType;
class DhlCourierCreateShipment implements CourierCreateShipment
{
    private $session;

    public function __construct(DhlSession $session)
    {
        $this->session = $session;
    }

    public function createShipment(ShipmentContract $shipment): ResponseContract
    {
        
        $response = new Response();

        try {
            if($this->session->parameters()->isShipmentReturn()) 
            {
                $response = $this->shipmentReturnRequest($shipment);
            } else {
                $response = $this->shipmentRequest($shipment);
            }

        } catch (SoapFault $fault) {
            $e = new TransportException($fault->faultstring, (int) $fault->faultcode);
            ResponseHelper::pushErrorsToResponse($response, [$e]);
            file_put_contents('req.txt', $this->session->client()->__getLastRequest());
            
        } catch (Exception $e) {
            ResponseHelper::pushErrorsToResponse($response, [$e]);
        }
        
        return $response;
    }

    private function shipmentReturnRequest(ShipmentContract $shipment): ResponseContract
    {
        $response = new Response();

        $client = $this->session->client();
        
        $request = $this->getShipmentReturn($shipment);

        $result = $client->createShipmentReturn($request);

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

        $response->shipmentId = $shipmentId;
        $response->trackingId = $shipmentId;

        return $response;
    }

    private function shipmentRequest(ShipmentContract $shipment): ResponseContract
    {
        $response = new Response();

        $client = $this->session->client();
        
        $request = $this->getShipment($shipment);
        $result = $client->createShipments($request);
        $shipmentId = $result->createShipmentsResult->item->shipmentId ?? null;

        if(!$shipmentId) {
            throw new TransportException('Shipment ID does not exist in response.');
        }

        $response->shipmentId = $shipmentId;
        $response->trackingId = $shipmentId;

        return $response;
    }    


    public function getShipmentReturn(ShipmentContract $shipment)
    {

        $billing = new billing();
        $billing->shippingPaymentType = DhlPaymentType::PAYER_RECEIVER;
        $billing->billingAccountNumber = $this->session->parameters()->getAccountNumber();
        $billing->paymentType = $this->session->parameters()->getPaymentMethod();
        // $billing->costsCenter = '';

        $shipmentReturnParameters =  $this->session->parameters()->getShipmentReturn();

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
        $shipmentInfo->serviceType = $this->session->parameters()->getShipmentReturnService();
        $shipmentInfo->bookCourier = ($shipmentReturnParameters['bookCourier'] && $shipmentReturnParameters['bookCourier'] === true);
        $shipmentInfo->billing = $billing;
        if($specialServices) {
            $shipmentInfo->specialServices = [ 
                'item' => $specialServices
            ];
        }
        $shipmentInfo->shipmentTime = $shipmentTime;
        $shipmentInfo->labelType = $this->session->parameters()->getLabelType();


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
        $pieceDefinition->type = $this->session->parameters()->getParcelType();
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
        $receiver->addressType = $this->session->parameters()->getAddressType();
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
        $pieceDefinition->type = $this->session->parameters()->getParcelType();
        $pieceDefinition->width = $shipment->getParcel()->getWidth();
        $pieceDefinition->height = $shipment->getParcel()->getHeight();
        $pieceDefinition->length = $shipment->getParcel()->getLength();
        $pieceDefinition->weight = $shipment->getParcel()->getWeight();
        $pieceDefinition->quantity = $shipment->getQuantity();
        $pieceDefinition->nonStandard = false;

        $paymentData = new paymentData();
        $paymentData->paymentMethod = $this->session->parameters()->getPaymentMethod();
        $paymentData->payerType = $this->session->parameters()->getPayerType();
        $paymentData->accountNumber = $this->session->parameters()->getAccountNumber();

        $serviceData = $this->session->parameters()->getService();
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
        $shipmentFullData->shipmentDate = $this->session->parameters()->getShipmentDate();
        $shipmentFullData->content = $shipment->getContent();
        $shipmentFullData->skipRestrictionCheck = false;


        $createShipment = new createShipments();
        $createShipment->authData = $this->session->getAuthData();
        $createShipment->shipments = ['item' => $shipmentFullData];

        return $createShipment;
    }
}
