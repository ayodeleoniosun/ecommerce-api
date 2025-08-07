<?php

namespace Tests\Unit\Order\Cart;

use App\Application\Shared\Enum\CurrencyEnum;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Payment\Actions\Card\PayWithCardAction;
use App\Domain\Payment\Dtos\Card\CardData;
use App\Domain\Payment\Dtos\Card\CustomerData;
use App\Domain\Payment\Dtos\Card\InitiateCardPaymentDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\GatewayTypeRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderItem;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Payment\GatewayType;
use App\Infrastructure\Models\Payment\Integration\Korapay\ApiLogsKoraCardPayment;
use App\Infrastructure\Models\Payment\Integration\Korapay\TransactionKoraCardPayment;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Order\OrderItemRepository;
use App\Infrastructure\Repositories\Order\OrderPaymentRepository;
use App\Infrastructure\Repositories\Payment\GatewayRepository;
use App\Infrastructure\Services\Payments\Korapay\KorapayIntegration;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Mockery;

beforeEach(function () {
    $this->orderItemRepo = Mockery::mock(OrderItemRepository::class)->makePartial();
    $this->orderPaymentRepo = Mockery::mock(OrderPaymentRepository::class)->makePartial();
    $this->orderRepo = Mockery::mock(OrderRepositoryInterface::class);
    $this->cardTransactionRepo = Mockery::mock(CardTransactionRepositoryInterface::class);
    $this->gatewayRepo = Mockery::mock(GatewayRepository::class)->makePartial();
    $this->gatewayTypeRepo = Mockery::mock(GatewayTypeRepositoryInterface::class);
    $this->korapayIntegration = Mockery::mock(KorapayIntegration::class, [$this->cardTransactionRepo])->makePartial();

    $this->user = User::factory()->create();

    $this->cart = UserCart::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->order = Order::factory()->create([
        'cart_id' => $this->cart->id,
        'user_id' => $this->user->id,
    ]);

    $this->product = Product::factory()->create();

    $this->productItem = ProductItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_id' => $this->product->id, 'price' => 10000, 'quantity' => 10],
            ['product_id' => $this->product->id, 'price' => 20000, 'quantity' => 15],
            ['product_id' => $this->product->id, 'price' => 30000, 'quantity' => 20],
        ))->create();

    $this->userCartItems = UserCartItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_item_id' => $this->productItem[0]->id, 'quantity' => 2],
            ['product_item_id' => $this->productItem[1]->id, 'quantity' => 3],
            ['product_item_id' => $this->productItem[2]->id, 'quantity' => 5],
        ))
        ->create([
            'cart_id' => $this->cart->id,
        ]);

    $this->orderItems = OrderItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['cart_item_id' => $this->userCartItems[0]->id, 'total_amount' => 20000],
            ['cart_item_id' => $this->userCartItems[1]->id, 'total_amount' => 60000],
            ['cart_item_id' => $this->userCartItems[2]->id, 'total_amount' => 150000],
        ))->create([
            'order_id' => $this->order->id,
        ]);

    $this->orderPayment = OrderPayment::factory()->create([
        'order_id' => $this->order->id,
    ]);

    $this->gatewayType = GatewayType::factory()->create();

    $this->cardData = new CardData(
        name: fake()->firstName().' '.fake()->lastName(),
        number: fake()->creditCardNumber(),
        cvv: '123',
        expiryMonth: fake()->month(),
        expiryYear: '30',
        pin: '1234',
    );

    $this->cardTransactionMock = Mockery::mock(TransactionKoraCardPayment::class)->makePartial();
    $this->cardTransactionMock->id = 1;

    $this->apiLogCardTransactionMock = Mockery::mock(ApiLogsKoraCardPayment::class)->makePartial();
    $this->apiLogCardTransactionMock->id = 1;

    $this->cardTransactionMock->apiLog = $this->apiLogCardTransactionMock;

    $this->cardDataArray = CardData::toArray($this->cardData);

    $this->actingAs($this->user, 'sanctum');

    $this->payWithCard = new PayWithCardAction(
        $this->orderItemRepo,
        $this->orderPaymentRepo,
        $this->orderRepo,
        $this->cardTransactionRepo,
        $this->gatewayRepo,
        $this->gatewayTypeRepo
    );
});

describe('Pay With Card', function () {
    it('should thrown an error if order amount is zero', function () {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->payWithCard->execute($order, $this->cardDataArray);
    })->throws(BadRequestException::class, 'Total order amount must be greater than zero.');

    it('should return a failed status if card is invalid', function () {
        $this->orderPayment->order_amount = $this->orderItems->pluck('total_amount')->sum();
        $this->orderPayment->save();

        $this->cardData->setNumber('invalid_card_number');
        $cardDataArray = CardData::toArray($this->cardData);

        $this->orderPaymentRepo->shouldReceive('store')
            ->once()
            ->andReturn($this->orderPayment);

        $this->gatewayTypeRepo->shouldReceive('findGatewayType')
            ->once()
            ->andReturn($this->gatewayType);

        Config::set('payment.gateways.korapay.webhook_url', 'https://example.com/webhook');

        $this->paymentDto = new InitiateCardPaymentDto(
            amount: $this->orderPayment->order_amount,
            currency: CurrencyEnum::NGN->value,
            card: new CardData(
                name: fake()->firstName().' '.fake()->lastName(),
                number: 'invalid_card_number',
                cvv: '123',
                expiryMonth: fake()->month(),
                expiryYear: '30',
                pin: '1234',
            ),
            customer: new CustomerData(
                email: fake()->email(),
                name: fake()->firstName().' '.fake()->lastName(),
            ),
            redirectUrl: 'https://example.com',
            orderPaymentReference: 'KPY-12345',
        );

        $this->cardTransactionMock->shouldReceive('load')
            ->once()
            ->with('apiLog')
            ->andReturnSelf();

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($this->cardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($this->apiLogCardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('update')
            ->once()
            ->andReturn($this->cardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('update')
            ->once()
            ->andReturn($this->apiLogCardTransactionMock);

        Http::fake([
            '*' => Http::response([
                'status' => true,
                'message' => 'Card charge failed',
                'data' => [
                    'amount' => $this->orderPayment->order_amount,
                    'amount_charged' => $this->orderPayment->order_amount,
                    'auth_model' => AuthModelEnum::PIN->value,
                    'currency' => CurrencyEnum::NGN,
                    'fee' => 0,
                    'vat' => 0,
                    'response_message' => 'Card not supported',
                    'payment_reference' => 'KPY-12345-ref',
                    'status' => 'failed',
                    'transaction_reference' => 'KPY-1234-ref',
                ],
            ]),
        ]);

        $response = $this->payWithCard->execute($this->order, $cardDataArray);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::FAILED->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBeNull()
            ->and($response->getResponseMessage())->toBe('Card not supported')
            ->and($response->getAmountCharged())->toBeNull();
    });

    it('should pay with card successfully using PIN auth model', function () {
        $this->orderPayment->order_amount = $this->orderItems->pluck('total_amount')->sum();
        $this->orderPayment->save();

        $this->orderPaymentRepo->shouldReceive('store')
            ->once()
            ->andReturn($this->orderPayment);

        $this->gatewayTypeRepo->shouldReceive('findGatewayType')
            ->once()
            ->andReturn($this->gatewayType);

        Config::set('payment.gateways.korapay.webhook_url', 'https://example.com/webhook');

        $this->paymentDto = new InitiateCardPaymentDto(
            amount: $this->orderPayment->order_amount,
            currency: CurrencyEnum::NGN->value,
            card: new CardData(
                name: fake()->firstName().' '.fake()->lastName(),
                number: fake()->creditCardNumber(),
                cvv: '123',
                expiryMonth: fake()->month(),
                expiryYear: '30',
                pin: '1234',
            ),
            customer: new CustomerData(
                email: fake()->email(),
                name: fake()->firstName().' '.fake()->lastName(),
            ),
            redirectUrl: 'https://example.com',
            orderPaymentReference: 'KPY-12345',
        );

        $this->cardTransactionMock->shouldReceive('load')
            ->once()
            ->with('apiLog')
            ->andReturnSelf();

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($this->cardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($this->apiLogCardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('update')
            ->once()
            ->andReturn($this->cardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('update')
            ->once()
            ->andReturn($this->apiLogCardTransactionMock);

        Http::fake([
            '*' => Http::response([
                'status' => true,
                'message' => 'Card charged successfully',
                'data' => [
                    'amount' => $this->orderPayment->order_amount,
                    'amount_charged' => $this->orderPayment->order_amount,
                    'auth_model' => AuthModelEnum::PIN->value,
                    'currency' => CurrencyEnum::NGN,
                    'fee' => 0,
                    'vat' => 0,
                    'response_message' => 'Card charged successfully',
                    'payment_reference' => 'KPY-12345-ref',
                    'status' => 'success',
                    'transaction_reference' => 'KPY-1234-ref',
                ],
            ]),
        ]);

        $response = $this->payWithCard->execute($this->order, $this->cardDataArray);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBe(AuthModelEnum::PIN->value)
            ->and($response->getResponseMessage())->toBe('Card charged successfully')
            ->and($response->getAmountCharged())->toBe((float) $this->orderPayment->order_amount);
    });

    it('should pay with card successfully using OTP auth model', function () {
        $this->orderPayment->order_amount = $this->orderItems->pluck('total_amount')->sum();
        $this->orderPayment->save();

        $this->orderPaymentRepo->shouldReceive('store')
            ->once()
            ->andReturn($this->orderPayment);

        $this->gatewayTypeRepo->shouldReceive('findGatewayType')
            ->once()
            ->andReturn($this->gatewayType);

        Config::set('payment.gateways.korapay.webhook_url', 'https://example.com/webhook');

        $this->paymentDto = new InitiateCardPaymentDto(
            amount: $this->orderPayment->order_amount,
            currency: CurrencyEnum::NGN->value,
            card: new CardData(
                name: fake()->firstName().' '.fake()->lastName(),
                number: fake()->creditCardNumber(),
                cvv: '123',
                expiryMonth: fake()->month(),
                expiryYear: '30',
                pin: '1234',
            ),
            customer: new CustomerData(
                email: fake()->email(),
                name: fake()->firstName().' '.fake()->lastName(),
            ),
            redirectUrl: 'https://example.com',
            orderPaymentReference: 'KPY-12345',
        );

        $this->cardTransactionMock->shouldReceive('load')
            ->once()
            ->with('apiLog')
            ->andReturnSelf();

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($this->cardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($this->apiLogCardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('update')
            ->once()
            ->andReturn($this->cardTransactionMock);

        Http::fake([
            '*' => Http::response([
                'status' => true,
                'message' => 'Card charged successfully',
                'data' => [
                    'amount' => $this->orderPayment->order_amount,
                    'amount_charged' => $this->orderPayment->order_amount,
                    'auth_model' => AuthModelEnum::OTP->value,
                    'currency' => CurrencyEnum::NGN,
                    'fee' => 0,
                    'vat' => 0,
                    'response_message' => 'Card charged successfully',
                    'payment_reference' => 'KPY-12345-ref',
                    'status' => 'success',
                    'transaction_reference' => 'KPY-1234-ref',
                ],
            ]),
        ]);

        $response = $this->payWithCard->execute($this->order, $this->cardDataArray);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBe(AuthModelEnum::OTP->value)
            ->and($response->getResponseMessage())->toBe('Card charged successfully')
            ->and($response->getAmountCharged())->toBe((float) $this->orderPayment->order_amount);
    });

    it('should pay with card successfully using AVS auth model', function () {
        $this->orderPayment->order_amount = $this->orderItems->pluck('total_amount')->sum();
        $this->orderPayment->save();

        $this->orderPaymentRepo->shouldReceive('store')
            ->once()
            ->andReturn($this->orderPayment);

        $this->gatewayTypeRepo->shouldReceive('findGatewayType')
            ->once()
            ->andReturn($this->gatewayType);

        Config::set('payment.gateways.korapay.webhook_url', 'https://example.com/webhook');

        $this->paymentDto = new InitiateCardPaymentDto(
            amount: $this->orderPayment->order_amount,
            currency: CurrencyEnum::NGN->value,
            card: new CardData(
                name: fake()->firstName().' '.fake()->lastName(),
                number: fake()->creditCardNumber(),
                cvv: '123',
                expiryMonth: fake()->month(),
                expiryYear: '30',
                pin: '1234',
            ),
            customer: new CustomerData(
                email: fake()->email(),
                name: fake()->firstName().' '.fake()->lastName(),
            ),
            redirectUrl: 'https://example.com',
            orderPaymentReference: 'KPY-12345',
        );

        $this->cardTransactionMock->shouldReceive('load')
            ->once()
            ->with('apiLog')
            ->andReturnSelf();

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($this->cardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($this->apiLogCardTransactionMock);

        $this->cardTransactionRepo->shouldReceive('update')
            ->once()
            ->andReturn($this->cardTransactionMock);

        Http::fake([
            '*' => Http::response([
                'status' => true,
                'message' => 'Card charged successfully',
                'data' => [
                    'amount' => $this->orderPayment->order_amount,
                    'amount_charged' => $this->orderPayment->order_amount,
                    'auth_model' => AuthModelEnum::AVS->value,
                    'currency' => CurrencyEnum::NGN,
                    'fee' => 0,
                    'vat' => 0,
                    'response_message' => 'Card charged successfully',
                    'payment_reference' => 'KPY-12345-ref',
                    'status' => 'success',
                    'transaction_reference' => 'KPY-1234-ref',
                ],
            ]),
        ]);

        $response = $this->payWithCard->execute($this->order, $this->cardDataArray);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::CARD->value)
            ->and($response->getAuthModel())->toBe(AuthModelEnum::AVS->value)
            ->and($response->getResponseMessage())->toBe('Card charged successfully')
            ->and($response->getAmountCharged())->toBe((float) $this->orderPayment->order_amount);
    });
});
