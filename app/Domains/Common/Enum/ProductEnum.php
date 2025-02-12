<?php

namespace App\Domains\Common\Enum;

enum  ProductEnum: string
{
    case ACTIVE = 'active';

    case OUT_OF_STOCK = 'out_of_stock';
}
