<?php

namespace App\Domain\Payment\Enums;

enum AuthModelEnum: string
{
    case CARD_ENROLL = 'CARD_ENROLL';
    case NO_AUTH = 'NO_AUTH';
    case PIN = 'PIN';
    case THREE_DS = '3DS';
    case AVS = 'AVS';
    case OTP = 'OTP';
}
