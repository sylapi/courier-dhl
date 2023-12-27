<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl\Entities;

use Sylapi\Courier\Dhl\Enums\ParcelType;
use Sylapi\Courier\Dhl\Enums\ShipmentReturnType;
use Sylapi\Courier\Abstracts\Options as OptionsAbstract;

class Options extends OptionsAbstract
{
    const DEFAULT_LABEL_TYPE = 'LBLP';
    const DEFAULT_PARCEL_TYPE = ParcelType::PACKAGE;
    const DEFAULT_ADDRESS_TYPE = 'B';
    const DEFAULT_SERVICE_PRODUCT = 'AH';    
    const DEFAULT_RETURN_SERVICE = ShipmentReturnType::ZK;

    public function getShipmentReturnService(): string
    {
        return  ($this->has('shipmentReturn') && $this->get('shipmentReturn')['shipmentReturnService']) 
            ? $this->get('shipmentReturn')['shipmentReturnService'] : self::DEFAULT_RETURN_SERVICE;
    }

    public function getService(): array
    {
        $service = (
            $this->has('service') 
                && is_array($this->get('service'))
            )? 
            $this->get('service')
            :[];

        if(!isset($service['product'])) {
            $service['product'] = self::DEFAULT_SERVICE_PRODUCT;
        }
        
        return $service;
    }    

    public function validate(): bool
    {
        return true;
    }
}
