<?php

namespace App\Infrastructure\Services\Payments\Flutterwave\Enum;

enum AuthorizationModeEnum: string
{
    case REDIRECT = 'redirect';
}
