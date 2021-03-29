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
use Sylapi\Courier\Entities\Response;
use Sylapi\Courier\Contracts\Shipment;
use Sylapi\Courier\Helpers\ResponseHelper;
use Sylapi\Courier\Exceptions\TransportException;
use Sylapi\Courier\Contracts\CourierCreateShipment;
use Sylapi\Courier\Contracts\Response as ResponseContract;

class DhlCourierCreateShipment implements CourierCreateShipment
{
    private $session;

    public function __construct(DhlSession $session)
    {
        $this->session = $session;
    }

    public function createShipment(Shipment $shipment): ResponseContract
    {
        $response = new Response();
        $client = $this->session->client();

        try {
            $request = $this->getShipment($shipment);
            $result = $client->createShipments($request);
            $shipmentId = $result->createShipmentsResult->item->shipmentId ?? null;
            if(!$shipmentId) {
                throw new TransportException('Shipment ID does not exist in response.');
            }
            $response->shipmentId = $shipmentId;
            $response->trackingId = $shipmentId;
        } catch (SoapFault $fault) {
            $e = new TransportException($fault->faultstring, (int) $fault->faultcode);
            ResponseHelper::pushErrorsToResponse($response, [$e]);
        } catch (Exception $e) {
            ResponseHelper::pushErrorsToResponse($response, [$e]);
        }
        
        return $response;
    }

    private function getShipment(Shipment $shipment): createShipments
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
        $shipmentFullData->shipmentDate = date('Y-m-d'); //'2021-03-29'
        $shipmentFullData->content = $shipment->getContent();
        $shipmentFullData->skipRestrictionCheck = false;


        $createShipment = new createShipments();
        $createShipment->authData = $this->session->getAuthData();
        $createShipment->shipments = ['item' => $shipmentFullData];

        return $createShipment;
    }
}
