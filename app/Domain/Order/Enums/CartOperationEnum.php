<?php

namespace App\Domain\Order\Enums;

enum CartOperationEnum: string
{
    case INCREMENT = 'increment';
    case DECREMENT = 'decrement';
}
