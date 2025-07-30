<?php

namespace App\Infrastructure\Services\Payments\Flutterwave\Enum;

enum AuthModelEnum: string
{
    case VBVSECURECODE = 'VBVSECURECODE';
    case THREE_DS = '3DS';
    case AVS = 'AVS';
    case OTP = 'OTP';

    public function label(): string
    {
        return match ($this) {
            self::VBVSECURECODE => 'pin',
            self::THREE_DS => '3ds',
            self::AVS => 'avs',
            self::OTP => 'otp'
        };
    }
}
