<?php

namespace App\Domain\Payment\Constants;

enum AuthModelEnum: string
{
    case PIN = 'PIN';
    case THREE_DS = '3DS';
    case AVS = 'AVS';
    case OTP = 'OTP';
}
