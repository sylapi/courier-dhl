<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

class DhlSessionFactory
{
    private $sessions = [];
    private $parameters;

    const API_LIVE = 'https://dhl24.com.pl/webapi2';
    const API_SANDBOX = 'https://sandbox.dhl24.com.pl/webapi2';

    public function session(DhlParameters $parameters): DhlSession
    {
        $this->parameters = $parameters;
        $this->parameters->apiUrl = ($this->parameters->sandbox) ? self::API_SANDBOX : self::API_LIVE;

        $key = sha1($this->parameters->apiUrl.':'.$this->parameters->login.':'.$this->parameters->password);

        return (isset($this->sessions[$key])) ? $this->sessions[$key] : ($this->sessions[$key] = new DhlSession($this->parameters));
    }
}
