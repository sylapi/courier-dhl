<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use ArrayObject;

class DhlParameters extends ArrayObject
{
    const DEFAULT_LABEL_TYPE = 'LBLP';
    const DEFAULT_PICKUP_TIME_FROM = '10:00';
    const DEFAULT_PICKUP_TIME_TO = '16:00';
    const DEFAULT_PARCEL_TYPE = DhlParcelType::PACKAGE;
    const DEFAULT_ADDRESS_TYPE = 'B';
    const DEFAULT_SERVICE_PRODUCT = 'AH';

    public static function create(array $parameters): self
    {
        return new self($parameters, ArrayObject::ARRAY_AS_PROPS);
    }

    public function getPaymentMethod(): ?string
    {
        return ($this->hasProperty('paymentMethod')) ? $this->paymentMethod : null;
    }

    public function getPayerType(): ?string
    {
        return ($this->hasProperty('payerType')) ? $this->payerType : null;
    }

    public function getAccountNumber(): ?string
    {
        return ($this->hasProperty('accountNumber')) ? $this->accountNumber : null;
    }

    public function getShipmentDate(): ?string
    {
        return ($this->hasProperty('shipmentDate')) ? $this->shipmentDate : null;
    }

    public function getPickupDate(): string
    {
        return  ($this->hasProperty('pickupDate')) ? $this->pickupDate :  date('Y-m-d');
    }

    public function getPickupTimeFrom(): string
    {
        return  ($this->hasProperty('pickupTimeFrom')) ? $this->pickupTimeFrom :  self::DEFAULT_PICKUP_TIME_FROM;
    }

    public function getParcelType(): string
    {
        return  ($this->hasProperty('parcelType')) ? $this->parcelType :  self::DEFAULT_PARCEL_TYPE;
    }

    public function getPickupTimeTo(): string
    {
        return  ($this->hasProperty('pickupTimeTo')) ? $this->pickupTimeTo :  self::DEFAULT_PICKUP_TIME_TO;
    }

    public function getLabelType(): string
    {
        return  ($this->hasProperty('labelType')) ? $this->labelType : self::DEFAULT_LABEL_TYPE;
    }

    public function getAddressType(): string
    {
        return  ($this->hasProperty('addressType')) ? $this->addressType :  self::DEFAULT_ADDRESS_TYPE;
    }

    public function getService(): array
    {
        $service = (
            $this->hasProperty('service') 
                && is_array($this->service)
            )? 
            $this->service
            :[];

        if(!isset($service['product'])) {
            $service['product'] = self::DEFAULT_SERVICE_PRODUCT;
        }
        
        return $service;
    }

    public function hasProperty(string $name)
    {
        return property_exists($this, $name);
    }
}
