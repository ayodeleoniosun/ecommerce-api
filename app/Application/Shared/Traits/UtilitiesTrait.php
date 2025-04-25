<?php

namespace App\Application\Shared\Traits;

use Illuminate\Support\Str;

trait UtilitiesTrait
{
    public static function generateUUID(): string
    {
        $uuid = Str::uuid()->toString();

        return str_replace('-', '', $uuid);
    }
}
