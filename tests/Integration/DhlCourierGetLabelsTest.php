<?php

namespace Sylapi\Courier\Dhl\Tests;

use SoapFault;
use Sylapi\Courier\Entities\Label;
use Sylapi\Courier\Dhl\DhlCourierGetLabels;
use Sylapi\Courier\Exceptions\TransportException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\Tests\Helpers\DhlSessionTrait;

class DhlCourierGetLabelsTest extends PHPUnitTestCase
{
    use DhlSessionTrait;

    private $soapMock = null;
    private $sessionMock = null;

    public function setUp(): void
    {
        $this->soapMock = $this->getSoapMock();
        $this->sessionMock = $this->getSessionMock($this->soapMock);
    }

    public function testGetLabelSuccess()
    {
        $localXml = simplexml_load_string(file_get_contents(__DIR__.'/Mock/getLabelsSuccess.xml'));
        $this->soapMock->expects($this->any())->method('__call')->will($this->returnValue($localXml));
        
        $shipmentId = 1234567890;

        $getLabel = new DhlCourierGetLabels($this->sessionMock);
        $response = $getLabel->getLabel((string) $shipmentId);

        $this->assertInstanceOf(Label::class, $response);
        $this->assertEquals('JVBERi0xLjUKJeLjz9MKMyAwI',(string) $response);
    }

    public function testGetLabelFailure()
    {
        $this->soapMock->expects($this->any())->method('__call')->will($this->throwException(new SoapFault('100', 'Error message')));

        $shipmentId = 1234567890;

        $getLabel = new DhlCourierGetLabels($this->sessionMock);
        $response = $getLabel->getLabel((string) $shipmentId);

        $this->assertInstanceOf(Label::class, $response);
        $this->assertTrue($response->hasErrors());
        $this->assertInstanceOf(TransportException::class, $response->getFirstError());
    }
}
