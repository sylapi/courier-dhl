<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

class DhlStorage
{
    private $label;
    private $shipment;

    /**
     * Get the value of shipment
     */ 
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * Set the value of shipment
     *
     * @return  self
     */ 
    public function setShipment($shipment)
    {
        $this->shipment = $shipment;

        return $this;
    }

    /**
     * Get the value of label
     */ 
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @return  self
     */ 
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }
}
