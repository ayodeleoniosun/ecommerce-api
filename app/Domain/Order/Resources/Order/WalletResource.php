<?php

namespace App\Domain\Order\Resources\Order;

use App\Application\Shared\Traits\UtilitiesTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    use UtilitiesTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'currency' => $this->currency,
            'balance' => $this->balance,
        ];
    }
}
