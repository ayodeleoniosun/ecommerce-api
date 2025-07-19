<?php

namespace Database\Seeders;

use App\Application\Shared\Enum\CurrencyEnum;
use App\Domain\Payment\Enums\PaymentCategoryEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Infrastructure\Models\Payment\Gateway;
use App\Infrastructure\Models\Payment\GatewayConfiguration;
use App\Infrastructure\Models\Payment\GatewayType;
use Kdabrow\SeederOnce\SeederOnce;

class GatewaySeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('seeders/data/gateways.json'));
        $gateways = json_decode($json, true);

        foreach ($gateways as $gateway) {
            $gateway = Gateway::create([
                'slug' => $gateway['slug'],
                'name' => $gateway['name'],
            ]);

            GatewayConfiguration::create([
                'type' => PaymentTypeEnum::CARD->value,
                'category' => PaymentCategoryEnum::COLLECTION->value,
                'currency' => CurrencyEnum::NGN->value,
                'gateway_id' => $gateway->id,
                'settings' => json_encode([
                    'transaction_limit' => [
                        'max' => 1000000,
                        'min' => 100,
                    ],
                ]),
            ]);
        }

        GatewayType::create([
            'type' => PaymentTypeEnum::CARD->value,
            'category' => PaymentCategoryEnum::COLLECTION->value,
            'currency' => CurrencyEnum::NGN->value,
            'primary_gateway_id' => 1,
            'secondary_gateway_id' => 2,
        ]);
    }
}
