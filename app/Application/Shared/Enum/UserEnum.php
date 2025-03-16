<?php

namespace App\Application\Shared\Enum;

enum UserEnum: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
