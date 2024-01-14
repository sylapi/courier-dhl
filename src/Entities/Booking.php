<?php

declare(strict_types=1);


namespace Sylapi\Courier\Dhl\Entities;

use Sylapi\Courier\Abstracts\Booking as BookingAbstract;

class Booking extends BookingAbstract
{
    private ?string $pickupDate;
    private ?string $pickupTimeFrom;
    private ?string $pickupTimeTo;

    public function getPickupDate(): ?string
    {
        return $this->pickupDate;
    }

    public function setPickupDate(?string $pickupDate): self
    {
        $this->pickupDate = $pickupDate;
        return $this;
    }

    public function getPickupTimeFrom(): ?string
    {
        return $this->pickupTimeFrom;
    }

    public function setPickupTimeFrom(?string $pickupTimeFrom): self
    {
        $this->pickupTimeFrom = $pickupTimeFrom;
        return $this;
    }

    public function getPickupTimeTo(): ?string
    {
        return $this->pickupTimeTo;
    }

    public function setPickupTimeTo(?string $pickupTimeTo): self
    {
        $this->pickupTimeTo = $pickupTimeTo;
        return $this;
    }
    
    public function validate(): bool
    {
        return true;
    }
}
