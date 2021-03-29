<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Courier;

class DhlCourierApiFactory
{
    private $dhlSessionFactory;

    public function __construct(DhlSessionFactory $dhlSessionFactory)
    {
        $this->dhlSessionFactory = $dhlSessionFactory;
    }

    public function create(array $parameters): Courier
    {
        $session = $this->dhlSessionFactory
                    ->session(DhlParameters::create($parameters));

        return new Courier(
            new DhlCourierCreateShipment($session),
            new DhlCourierPostShipment($session),
            new DhlCourierGetLabels($session),
            new DhlCourierGetStatuses($session),
            new DhlCourierMakeShipment(),
            new DhlCourierMakeParcel(),
            new DhlCourierMakeReceiver(),
            new DhlCourierMakeSender(),
            new DhlCourierMakeBooking()
        );
    }
}
