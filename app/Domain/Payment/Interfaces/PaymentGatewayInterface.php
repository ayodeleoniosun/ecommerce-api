<?php

namespace App\Domain\Payment\Interfaces;

interface PaymentGatewayInterface
{
    public function initialize();

    public function verify();
}
