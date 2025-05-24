<?php

namespace App\Application\Shared\Enum;

enum DeliveryTypeEnum: string
{
    case PICKUP_STATION = 'pickup_station';
    case DOOR_DELIVERY = 'door_delivery';
}
