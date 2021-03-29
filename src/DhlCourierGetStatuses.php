<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Exception;
use SoapFault;
use getTrackAndTraceInfo;
use Sylapi\Courier\Entities\Status;
use Sylapi\Courier\Enums\StatusType;
use Sylapi\Courier\Helpers\ResponseHelper;
use Sylapi\Courier\Contracts\CourierGetStatuses;
use Sylapi\Courier\Exceptions\TransportException;
use Sylapi\Courier\Contracts\Status as StatusContract;

class DhlCourierGetStatuses implements CourierGetStatuses
{
    private $session;

    public function __construct(DhlSession $session)
    {
        $this->session = $session;
    }

    public function getStatus(string $shipmentId): StatusContract
    {
        $clinet = $this->session->client();
        try {
            $request = $this->getTrackAndTraceInfo($shipmentId);
            $result = $clinet->getTrackAndTraceInfo($request);

            // TODO: 
            var_dump($result->getTrackAndTraceInfoResult);
            $status = new Status(null);

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
