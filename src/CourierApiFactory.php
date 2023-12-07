<?php

declare(strict_types=1);

namespace Sylapi\Courier\Dhl;

use Sylapi\Courier\Courier;
use Sylapi\Courier\Contracts\Credentials;

class DhlCourierApiFactory
{
    private $dhlSessionFactory;

    public function __construct(SessionFactory $sessionFactory)
    {
        $this->dhlSessionFactory = $sessionFactory;
    }

    public function create(Credentials $credentials): Courier
    {

        
        $session = $this->dhlSessionFactory
                    ->session($credentials);

        return new Courier(
            new CourierCreateShipment($session),
            new CourierPostShipment($session),
            new CourierGetLabels($session),
            new CourierGetStatuses($session),
            new CourierMakeShipment(),
            new CourierMakeParcel(),
            new CourierMakeReceiver(),
            new CourierMakeSender(),
            new CourierMakeService(),
            new CourierMakeOptions(),
            new CourierMakeBooking(),
            new CourierMakeLabelType(),
        );
    }
}
