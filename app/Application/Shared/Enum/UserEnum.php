<?php

namespace App\Domains\Common\Enum;

enum  UserEnum: string
{
    case PENDING = 'pending';

    case ACTIVE = 'active';

    case INACTIVE = 'inactive';
}
