<?php

namespace App\Infrastructure\Services\Payments\Korapay;

use Illuminate\Http\Client\ConnectionException;

class Charge extends BaseService
{
    /**
     * @throws ConnectionException
     */
    public function initiate(string $chargeData): object
    {
        return $this->http()->post('/v1/charges/card', [
            'charge_data' => $chargeData,
        ])->object();
    }

    public function verify(string $reference): array
    {
        return [];
    }
}
