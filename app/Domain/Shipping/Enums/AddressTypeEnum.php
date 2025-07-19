<?php

namespace App\Domain\Shipping\Enums;

enum AddressTypeEnum: string
{
    case DEFAULT = 'default';
    case OTHERS = 'others';
}
