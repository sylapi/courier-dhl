<?php

namespace Sylapi\Courier\Dhl\Responses;
use Sylapi\Courier\Dhl\Entities\Booking;
use Sylapi\Courier\Responses\Shipment as ShipmentResponse;
use Sylapi\Courier\Contracts\Response as ResponseContract;

class Shipment extends ShipmentResponse
{
    private $trackingId;

    public function setTrackingId(string $TrackingId): ResponseContract
    {
        $this->trackingId = $TrackingId;

        return $this;
    }

    public function getTrackingId(): ?string
    {
        return $this->trackingId;
    }

    public function getBooking() : ?Booking
    {

        if(!$this->getResponse()) {
            return null;
        }

        $booking = new Booking();
        $booking->setShipmentId($this->getResponse()->getShipmentId());

        return $booking;

    }
}
