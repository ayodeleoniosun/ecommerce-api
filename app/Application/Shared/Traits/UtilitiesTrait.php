<?php

namespace App\Application\Shared\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait UtilitiesTrait
{
    public static function generateUUID(): string
    {
        $uuid = Str::uuid()->toString();

        return str_replace('-', '', $uuid);
    }

    public static function generateRandomCharacters(): string
    {
        $alphanumeric = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($alphanumeric, 5)), 0, 10);
    }

    public static function parseDate($date): string
    {
        return Carbon::parse($date)->format('F jS, Y h:i:s');
    }

    public static function filterColumn(): array
    {
        return [
            'date' => 'created_at',
            'price' => 'price',
            'quantity' => 'quantity',
        ];
    }
}
