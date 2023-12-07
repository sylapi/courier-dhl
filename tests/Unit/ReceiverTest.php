<?php

namespace Sylapi\Courier\Dhl\Tests\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\Entities\Receiver;

class ReceiverTest extends PHPUnitTestCase
{
    public function testGetCountryCode()
    {
        $receiver = new Receiver();
        $receiver->setCountryCode('pl');
        $this->assertEquals('PL', $receiver->getCountryCode());
    }

    public function testGetCountryCodeWhenItIsNotDefined()
    {
        $receiver = new Receiver();
        $this->assertEquals(null, $receiver->getCountryCode());
    }
}
