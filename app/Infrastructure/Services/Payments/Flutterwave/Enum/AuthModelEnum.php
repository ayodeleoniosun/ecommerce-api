<?php

namespace App\Infrastructure\Services\Payments\Flutterwave\Enum;

enum AuthModelEnum: string
{
    case VBVSECURECODE = 'OTP';
    case THREE_DS = '3DS';
    case AVS = 'AVS';

    public static function label($authModel): self
    {
        return collect(self::cases())
            ->first(fn ($case) => $case->name === $authModel);
    }
}
