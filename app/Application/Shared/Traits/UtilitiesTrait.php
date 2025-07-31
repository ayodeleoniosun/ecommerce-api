<?php

namespace App\Application\Shared\Traits;

use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait UtilitiesTrait
{
    public static function generateUUID(): string
    {
        $uuid = Str::uuid()->toString();

        return str_replace('-', '', $uuid);
    }

    public static function generateRandomCharacters(string $prefix = '', int $length = 16): string
    {
        $alphanumeric = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomCharacters = substr(str_shuffle(str_repeat($alphanumeric, 5)), 0, $length);

        return $prefix.$randomCharacters;
    }

    public static function parseDate($date): string
    {
        return Carbon::parse($date)->format('F jS, Y h:i A');
    }

    public static function parseDateOnly($date): string
    {
        return Carbon::parse($date)->format('F jS, Y');
    }

    public static function parseTime($time): string
    {
        return Carbon::parse($time)->format('h:i A');
    }

    public static function completedTransactionStatuses(): array
    {
        return [PaymentStatusEnum::FAILED->value, PaymentStatusEnum::SUCCESS->value];
    }

    public static function requiresAuthorization(string $authModel): bool
    {
        if (in_array($authModel, [AuthModelEnum::NO_AUTH->value, AuthModelEnum::PIN->value])) {
            return false;
        }

        return true;
    }
}
