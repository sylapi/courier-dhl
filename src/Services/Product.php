<?php

namespace Sylapi\Courier\Dhl\Services;

use Sylapi\Courier\Abstracts\Service;

class Product extends Service
{
    const DEFAULT_SERVICE_PRODUCT = 'AH';

    public function getProduct(): string
    {
        return $this->get('product', self::DEFAULT_SERVICE_PRODUCT);
    }

    public function setProduct(string $product): self
    {
        $this->set('product', $product);
        return $this;
    }

    public function handle(): array
    {
        return ['product' => $this->getProduct()];
    }
}
