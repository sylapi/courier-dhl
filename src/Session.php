<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use authData;
use SoapClient;
use Sylapi\Courier\Dhl\Entities\Credentials;

class Session
{
    private $credentials;
    private $client;
    private $storage;

    public function __construct(Credentials $credentials)
    {
        $this->credentials = $credentials;
        $this->client = null;
        $this->storage = null;
    }


    public function storage(): Storage
    {
        if (!$this->storage) {
            $this->storage = $this->initializeStorage();
        }

        return $this->storage;
    }

    public function initializeStorage(): Storage
    {
        $this->storage = new Storage();
        return $this->storage;
    }

    public function client(): SoapClient
    {
        if (!$this->client) {
            $this->client = $this->initializeSession();
        }

        return $this->client;
    }

    private function initializeSession(): SoapClient
    {
        $this->client = new \SoapClient($this->credentials->getApiUrl(), ['trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE, 'soap_defencoding' => 'UTF-8', 'decode_utf8' => true]);
        
        return $this->client;
    }

    public function getAuthData(): authData
    {
        $authData = new authData;
        $authData->username = $this->credentials->getLogin();
        $authData->password = $this->credentials->getPassword();
        return $authData;
    }
}
