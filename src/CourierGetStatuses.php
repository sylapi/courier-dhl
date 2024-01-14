<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Exception;
use SoapFault;
use getTrackAndTraceInfo;
use Sylapi\Courier\Dhl\StatusTransformer;
use Sylapi\Courier\Exceptions\TransportException;
use Sylapi\Courier\Contracts\Response as ResponseContract;
use Sylapi\Courier\Dhl\Responses\Status as StatusResponse;
use Sylapi\Courier\Contracts\CourierGetStatuses as CourierGetStatusesContract;
use Sylapi\Courier\Responses\Status as ResponseStatus;

class CourierGetStatuses implements CourierGetStatusesContract
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getStatus(string $shipmentId): ResponseStatus
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
            return new StatusResponse((string) new StatusTransformer((string) $event->status));

        } catch (SoapFault $fault) {
            throw new TransportException($fault->faultstring, (int) $fault->faultcode);
        } catch (Exception $e) {
            throw new TransportException($e->getMessage(), $e->getCode());
        }
    }

    private function getTrackAndTraceInfo(string $shipmentId): getTrackAndTraceInfo
    {
        $getTrackAndTraceInfo = new getTrackAndTraceInfo();
        $getTrackAndTraceInfo->authData = $this->session->getAuthData();
        $getTrackAndTraceInfo->shipmentId = $shipmentId;
        return $getTrackAndTraceInfo;
    }
}
