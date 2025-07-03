<?php

namespace Database\Factories;

use App\Application\Shared\Enum\CartStatusEnum;
use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Constants\Currencies;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserCart>
 */
class UserCartFactory extends Factory
{
    use UtilitiesTrait;

    protected $model = UserCart::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => self::generateUUID(),
            'user_id' => User::factory()->create()->id,
            'status' => CartStatusEnum::PENDING->value,
            'currency' => Currencies::NGN->value,
        ];
    }
}
