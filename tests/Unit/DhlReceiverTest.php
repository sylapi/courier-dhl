<?php

namespace Sylapi\Courier\Dhl\Tests\Unit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\DhlReceiver;

class DhlReceiverTest extends PHPUnitTestCase
{
    public function testGetCountryCode()
    {
        $receiver = new DhlReceiver();
        $receiver->setCountryCode('pl');
        $this->assertEquals('PL', $receiver->getCountryCode());
    }

    public function testGetCountryCodeWhenItIsNotDefined()
    {
        $receiver = new DhlReceiver();
        $this->assertEquals(null, $receiver->getCountryCode());
    }
}
