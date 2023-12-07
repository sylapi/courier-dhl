<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Exception;
use SoapFault;
use getTrackAndTraceInfo;
use Sylapi\Courier\Entities\Status;
use Sylapi\Courier\Enums\StatusType;
use Sylapi\Courier\Helpers\ResponseHelper;
use Sylapi\Courier\Exceptions\TransportException;
use Sylapi\Courier\Contracts\Response as ResponseContract;
use Sylapi\Courier\Dhl\DhlStatusTransformer;
use Sylapi\Courier\Contracts\CourierGetStatuses as CourierGetStatusesContract;

class CourierGetStatuses implements CourierGetStatusesContract
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getStatus(string $shipmentId): ResponseContract
    {
        $client = $this->session->client();
        try {
            $request = $this->getTrackAndTraceInfo($shipmentId);
            $result = $client->getTrackAndTraceInfo($request);

            $events = $result->getTrackAndTraceInfoResult->events->item ?? [];
            
            if(!is_array($events) || count($events) < 1) {
                throw new TransportException('Status does not exist in the response.');
            }

            $event = end($events);
            $status =  new Status((string) new DhlStatusTransformer((string) $event->status));

        } catch (SoapFault $fault) {
            $e = new TransportException($fault->faultstring, (int) $fault->faultcode);
            $status = new Status(StatusType::APP_RESPONSE_ERROR);
            ResponseHelper::pushErrorsToResponse($status, [$e]);
        } catch (Exception $e) {
            $status = new Status(StatusType::APP_RESPONSE_ERROR);
            ResponseHelper::pushErrorsToResponse($status, [$e]);
        }
        
        return $status;
    }

    private function getTrackAndTraceInfo(string $shipmentId): getTrackAndTraceInfo
    {
        $getTrackAndTraceInfo = new getTrackAndTraceInfo();
        $getTrackAndTraceInfo->authData = $this->session->getAuthData();
        $getTrackAndTraceInfo->shipmentId = $shipmentId;
        return $getTrackAndTraceInfo;
    }
}
