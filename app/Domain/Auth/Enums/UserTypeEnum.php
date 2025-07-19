<?php

namespace App\Domain\Auth\Enums;

enum UserTypeEnum: string
{
    case CUSTOMER = 'customer';
    case VENDOR = 'vendor';
}
