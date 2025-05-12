<?php

namespace App\Application\Shared\Enum;

enum UserTypeEnum: string
{
    case CUSTOMER = 'customer';
    case VENDOR = 'vendor';
}
