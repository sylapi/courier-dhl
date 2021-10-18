<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Exception;
use getLabels;
use SoapFault;
use Sylapi\Courier\Dhl\DhlSession;
use Sylapi\Courier\Entities\Label;
use Sylapi\Courier\Helpers\ResponseHelper;
use Sylapi\Courier\Contracts\CourierGetLabels;
use Sylapi\Courier\Exceptions\TransportException;
use Sylapi\Courier\Contracts\Label as LabelContract;

class DhlCourierGetLabels implements CourierGetLabels
{
    private $session;

    public function __construct(DhlSession $session)
    {
        $this->session = $session;
    }

    public function getLabel(string $shipmentId): LabelContract
    {
        $client = $this->session->client();
        try {
            $request = $this->getLabelsRequest($shipmentId);
            $result = $client->getLabels($request);
            $labelData = $result->getLabelsResult->item->labelData ?? null;

            if ($labelData === null) {
                throw new TransportException('LabelData does not exist in the response.');
            }
            
            $label = new Label((string) base64_decode($labelData));

        } catch (SoapFault $fault) {
            $e = new TransportException($fault->faultstring);
            $label = new Label(null);
            ResponseHelper::pushErrorsToResponse($label, [$e]);
        } catch (Exception $e) {
            $label = new Label(null);
            ResponseHelper::pushErrorsToResponse($label, [$e]);
        }        

        return $label;
    }

    private function getLabelsRequest(string $shipmentId): getLabels
    {
        $getLabels = new getLabels();
        $getLabels->authData = $this->session->getAuthData();
        $getLabels->itemsToPrint = [
            [
                'labelType' => $this->session->parameters()->getLabelType(),
                'shipmentId' => $shipmentId
            ]
        ];
        return $getLabels;
    }    
}
