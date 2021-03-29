<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use SoapFault;
use Exception;
use bookCourier;
use Sylapi\Courier\Dhl\DhlSession;
use Sylapi\Courier\Contracts\Booking;
use Sylapi\Courier\Entities\Response;
use Sylapi\Courier\Helpers\ResponseHelper;
use Sylapi\Courier\Contracts\CourierPostShipment;
use Sylapi\Courier\Exceptions\TransportException;
use Sylapi\Courier\Contracts\Response as ResponseContract;

class DhlCourierPostShipment implements CourierPostShipment
{
    private $session;

    public function __construct(DhlSession $session)
    {
        $this->session = $session;
    }
    
    public function postShipment(Booking $booking): ResponseContract
    {
        $client = $this->session->client();
        $response = new Response();
        try {
            $request = $this->getBookCourier($booking);
            
            $result = $client->bookCourier($request);

            $response->shipmentId = $booking->getShipmentId();
            $response->trackingId =  $booking->getShipmentId();
        } catch (SoapFault $fault) {
            $excaption = new TransportException($fault->faultstring);
            ResponseHelper::pushErrorsToResponse($response, [$excaption]);
        } catch (Exception $excaption) {
            ResponseHelper::pushErrorsToResponse($response, [$excaption]);
        }
       
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
