<?php

namespace App\Application\Shared\Enum;

enum UserStatusEnum: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
