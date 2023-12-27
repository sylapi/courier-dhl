<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use SoapFault;
use Exception;
use bookCourier;
use Sylapi\Courier\Contracts\Booking;
use Sylapi\Courier\Dhl\Responses\Shipment as ShipmentResponse;  
use Sylapi\Courier\Exceptions\TransportException;
use Sylapi\Courier\Contracts\Response as ResponseContract;
use Sylapi\Courier\Contracts\CourierPostShipment as CourierPostShipmentContract;

class CourierPostShipment implements CourierPostShipmentContract
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    
    public function postShipment(Booking $booking): ResponseContract
    {
        $client = $this->session->client();
        $response = new ShipmentResponse();
        try {
            $request = $this->getBookCourier($booking);
            $result = $client->bookCourier($request);
        } catch (SoapFault $fault) {
            throw new TransportException($fault->faultstring, (int) $fault->faultcode);
           
        } catch (Exception $e) {
            throw new TransportException($e->getMessage(), $e->getCode());
        }
       
        $response->setResponse($result);
        $response->setShipmentId((string) $booking->getShipmentId());
        $response->setTrackingId((string) $booking->getShipmentId());

        return $response;
    }

    private function getBookCourier(Booking $booking)
    {
        $bookCourier = new bookCourier();
        $bookCourier->authData = $this->session->getAuthData();
        $bookCourier->pickupDate = $this->session->parameters()->getPickupDate();
        $bookCourier->pickupTimeFrom = $this->session->parameters()->getPickupTimeFrom();
        $bookCourier->pickupTimeTo = $this->session->parameters()->getPickupTimeTo();;
        $bookCourier->shipmentIdList = [ $booking->getShipmentId() ];

        return $bookCourier;
    }
}
