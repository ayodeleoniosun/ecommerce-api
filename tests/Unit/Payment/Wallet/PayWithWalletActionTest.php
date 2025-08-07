<?php

namespace Tests\Unit\Payment;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Order\Interfaces\Order\OrderItemRepositoryInterface;
use App\Domain\Payment\Actions\Wallet\PayWithWalletAction;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\PaymentResponseMessageEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Payment\Enums\WalletTransactionTypeEnum;
use App\Domain\Payment\Interfaces\Wallet\WalletAuditLogRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletOrderPaymentRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletRepositoryInterface;
use App\Domain\Payment\Interfaces\Wallet\WalletTransactionRepositoryInterface;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\UserCartItem;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Inventory\ProductItem;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\OrderItem;
use App\Infrastructure\Models\Order\OrderPayment;
use App\Infrastructure\Models\Order\OrderShipping;
use App\Infrastructure\Models\Payment\Wallet\Wallet;
use App\Infrastructure\Models\Payment\Wallet\WalletAuditLog;
use App\Infrastructure\Models\Payment\Wallet\WalletOrderPayment;
use App\Infrastructure\Models\Payment\Wallet\WalletTransaction;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\Order\OrderPaymentRepository;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Mockery;

beforeEach(function () {
    $this->walletRepo = Mockery::mock(WalletRepositoryInterface::class);
    $this->walletTransactionRepo = Mockery::mock(WalletTransactionRepositoryInterface::class);
    $this->walletAuditLogRepo = Mockery::mock(WalletAuditLogRepositoryInterface::class);
    $this->walletOrderPaymentRepo = Mockery::mock(WalletOrderPaymentRepositoryInterface::class);
    $this->orderItemRepo = Mockery::mock(OrderItemRepositoryInterface::class);
    $this->orderPaymentRepo = Mockery::mock(OrderPaymentRepository::class)->makePartial();

    $this->user = User::factory()->create();

    $this->userCart = UserCart::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->product = Product::factory()->create();

    $this->productItems = ProductItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['price' => 10000, 'quantity' => 10],
            ['price' => 20000, 'quantity' => 15],
            ['price' => 30000, 'quantity' => 20],
        ))->create([
            'product_id' => $this->product->id,
        ]);

    $this->userCart = UserCart::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->order = Order::factory()->create([
        'user_id' => $this->user->id,
        'cart_id' => $this->userCart->id,
        'status' => OrderStatusEnum::SUCCESS->value,
    ]);

    $this->userCartItems = UserCartItem::factory()
        ->count(3)
        ->state(new Sequence(
            ['product_item_id' => $this->productItems[0]->id, 'quantity' => 2],
            ['product_item_id' => $this->productItems[1]->id, 'quantity' => 3],
            ['product_item_id' => $this->productItems[2]->id, 'quantity' => 5],
        ))->create([
            'cart_id' => $this->userCart->id,
        ]);

    $this->orderItems = OrderItem::factory()
        ->count(3)
        ->state(new Sequence(
            [
                'cart_item_id' => $this->userCartItems[0]->id,
                'total_amount' => $this->userCartItems[0]->quantity * $this->productItems[0]->price,
            ],
            [
                'cart_item_id' => $this->userCartItems[1]->id,
                'total_amount' => $this->userCartItems[1]->quantity * $this->productItems[1]->price,
            ],
            [
                'cart_item_id' => $this->userCartItems[2]->id,
                'total_amount' => $this->userCartItems[2]->quantity * $this->productItems[2]->price,
            ],
        ))->create([
            'order_id' => $this->order->id,
        ]);

    $this->orderShipping = OrderShipping::factory()->create([
        'order_id' => $this->order->id,
    ]);

    $this->orderPayment = OrderPayment::factory()->create([
        'order_id' => $this->order->id,
    ]);

    $this->actingAs($this->user, 'sanctum');

    $this->payWithWallet = new PayWithWalletAction(
        $this->walletRepo,
        $this->walletTransactionRepo,
        $this->walletAuditLogRepo,
        $this->walletOrderPaymentRepo,
        $this->orderItemRepo,
        $this->orderPaymentRepo
    );
});

describe('Pay With Wallet', function () {
    it('should return an exception if wallet balance is insufficient', function () {
        $this->walletRepo->shouldReceive('find')
            ->with($this->user->id, $this->order->currency)
            ->andReturn(null);

        $wallet = Wallet::factory()->create([
            'user_id' => $this->user->id,
            'currency' => $this->order->currency,
        ]);

        $this->walletRepo->shouldReceive('create')
            ->with([
                'user_id' => $this->user->id,
                'currency' => $this->order->currency,
            ])->andReturn($wallet);

        $this->payWithWallet->execute($this->order);
    })->throws(BadRequestException::class, 'Insufficient funds');

    it('should pay for order via wallet', function () {
        $this->walletRepo->shouldReceive('find')
            ->with($this->user->id, $this->order->currency)
            ->andReturn(null);

        $wallet = Wallet::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 5000000,
            'currency' => $this->order->currency,
        ]);

        $walletTransaction = WalletTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'amount' => $this->orderPayment->order_amount,
            'amount_charged' => $this->orderPayment->order_amount,
            'type' => WalletTransactionTypeEnum::DEBIT->value,
            'reference' => 'WAL-1234589',
        ]);

        $walletAuditLog = WalletAuditLog::factory()->create([
            'wallet_id' => $wallet->id,
            'transaction_id' => $walletTransaction->id,
            'previous_balance' => 0,
            'new_balance' => $this->orderPayment->order_amount,
        ]);

        $walletOrderPayment = WalletOrderPayment::factory()->create([
            'wallet_transaction_id' => $walletTransaction->id,
            'order_payment_id' => $this->orderPayment->id,
        ]);

        $this->walletRepo->shouldReceive('create')
            ->andReturn($wallet);

        $this->walletTransactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($walletTransaction);

        $this->walletOrderPaymentRepo->shouldReceive('create')
            ->once()
            ->andReturn($walletOrderPayment);

        $this->walletAuditLogRepo->shouldReceive('create')
            ->once()
            ->andReturn($walletAuditLog);

        $currentWallet = $wallet;
        $wallet->balance -= $this->orderPayment->order_amount;
        $wallet->save();

        $this->walletRepo->shouldReceive('decrementBalance')
            ->once()
            ->with($currentWallet, $this->orderPayment->order_amount)
            ->andReturn($wallet);

        $response = $this->payWithWallet->execute($this->order);

        expect($response)->toBeInstanceOf(PaymentResponseDto::class)
            ->and($response->getStatus())->toBe(PaymentStatusEnum::SUCCESS->value)
            ->and($response->getPaymentMethod())->toBe(PaymentTypeEnum::WALLET->value)
            ->and($response->getReference())->toBe($walletTransaction->reference)
            ->and($response->getResponseMessage())->toBe(PaymentResponseMessageEnum::TRANSACTION_SUCCESSFUL->value);
    });
});
