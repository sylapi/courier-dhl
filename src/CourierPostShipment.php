<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use SoapFault;
use Exception;
use bookCourier;
use Sylapi\Courier\Contracts\Booking;
use Sylapi\Courier\Dhl\Responses\Parcel as ParcelResponse;  
use Sylapi\Courier\Exceptions\TransportException;
use Sylapi\Courier\Contracts\CourierPostShipment as CourierPostShipmentContract;
use Sylapi\Courier\Dhl\Entities\Booking as BookingEntity;
use Sylapi\Courier\Responses\Parcel as ResponseParcel;

class CourierPostShipment implements CourierPostShipmentContract
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    
    public function postShipment(Booking $booking): ResponseParcel
    {
        $client = $this->session->client();
        $response = new ParcelResponse();
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
        /**
         * @var BookingEntity $booking
         */
        $bookCourier->pickupDate = $booking->getPickupDate();
        $bookCourier->pickupTimeFrom = $booking->getPickupTimeFrom();
        $bookCourier->pickupTimeTo = $booking->getPickupTimeTo();;
        $bookCourier->shipmentIdList = [ $booking->getShipmentId() ];

        return $bookCourier;
    }
}
