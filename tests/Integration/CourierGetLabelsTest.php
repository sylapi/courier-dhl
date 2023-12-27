<?php

namespace Sylapi\Courier\Dhl\Tests;

use SoapFault;
use Sylapi\Courier\Dhl\CourierGetLabels;
use Sylapi\Courier\Dhl\Entities\LabelType;
use Sylapi\Courier\Exceptions\TransportException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sylapi\Courier\Dhl\Tests\Helpers\SessionTrait;

class CourierGetLabelsTest extends PHPUnitTestCase
{
    use SessionTrait;

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

        $getLabel = new CourierGetLabels($this->sessionMock);
        $labelTypeMock = $this->createMock(LabelType::class);
        $response = $getLabel->getLabel((string) $shipmentId, $labelTypeMock);
        $this->assertEquals('JVBERi0xLjUKJeLjz9MKMyAwI', $response);
    }

    public function testGetLabelFailure()
    {
        $this->soapMock->expects($this->any())->method('__call')->will($this->throwException(new SoapFault('100', 'Error message')));

        $shipmentId = 1234567890;
        $this->expectException(TransportException::class);

        $getLabel = new CourierGetLabels($this->sessionMock);
        $labelTypeMock = $this->createMock(LabelType::class);
        $getLabel->getLabel((string) $shipmentId, $labelTypeMock);
    }
}
