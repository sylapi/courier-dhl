<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use authData;
use SoapClient;
use Sylapi\Courier\Dhl\DhlStorage;

class DhlSession
{
    private $parameters;
    private $client;
    private $storage;

    public function __construct(DhlParameters $parameters)
    {
        $this->parameters = $parameters;
        $this->client = null;
        $this->storage = null;
    }

    public function parameters(): DhlParameters
    {
        return $this->parameters;
    }

    public function storage(): DhlStorage
    {
        if (!$this->storage) {
            $this->initializeStorage();
        }

        return $this->storage;
    }

    public function initializeStorage(): void
    {
        $this->storage = new DhlStorage();
    }

    public function client(): SoapClient
    {
        if (!$this->client) {
            $this->initializeSession();
        }

        return $this->client;
    }

    private function initializeSession(): void
    {
        $this->client = new \SoapClient($this->parameters->apiUrl, ['trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE]);
        $this->client->soap_defencoding = 'UTF-8';
        $this->client->decode_utf8 = true;
    }

    public function getAuthData(): authData
    {
        $authData = new authData;
        $authData->username = $this->parameters()->login ?? null;
        $authData->password = $this->parameters()->password ?? null;
        return $authData;
    }
}
