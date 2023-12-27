<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Exception;
use getLabels;
use SoapFault;
use Sylapi\Courier\Dhl\Responses\Label as LabelResponse;
use Sylapi\Courier\Helpers\ResponseHelper;
use Sylapi\Courier\Contracts\CourierGetLabels as CourierGetLabelsContract;
use Sylapi\Courier\Exceptions\TransportException;
use Sylapi\Courier\Contracts\Response as ResponseContract;
use Sylapi\Courier\Contracts\LabelType as LabelTypeContract;


class CourierGetLabels implements CourierGetLabelsContract
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getLabel(string $shipmentId,  LabelTypeContract $labelType): ResponseContract
    {
        $storageLabel = $this->session->storage()->getLabel();
        if($storageLabel) {
            return new LabelResponse(base64_decode((string) $storageLabel));
        }

        $client = $this->session->client();
        try {
            $request = $this->getLabelsRequest($shipmentId, $labelType);
            $result = $client->getLabels($request);
            $labelData = $result->getLabelsResult->item->labelData ?? null;

            if ($labelData === null) {
                throw new TransportException('LabelData does not exist in the response.');
            }
            
            $label = new LabelResponse(base64_decode((string) $labelData));

        } catch (SoapFault $fault) {
            throw new TransportException($fault->faultstring);
        } catch (Exception $e) {
            throw new TransportException($e->getMessage(), $e->getCode());
        }        

        return $label;
    }

    private function getLabelsRequest(string $shipmentId, LabelTypeContract $labelType): getLabels
    {
        $getLabels = new getLabels();
        $getLabels->authData = $this->session->getAuthData();
        $getLabels->itemsToPrint = [
            [
                'labelType' => $labelType->getLabelType(),
                'shipmentId' => $shipmentId
            ]
        ];
        return $getLabels;
    }    
}
