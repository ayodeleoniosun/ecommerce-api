<?php

namespace App\Application\Shared\Enum;

enum CartOperationEnum: string
{
    case INCREMENT = 'increment';
    case DECREMENT = 'decrement';
}
