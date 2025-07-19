<?php

namespace App\Domain\Order\Actions\Order;

use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Order\Resources\Order\OrderResource;
use App\Infrastructure\Models\Order\Order;

class GetOrderAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    public function execute(string $UUID): OrderResource
    {
        $order = $this->orderRepository->findByColumn(
            Order::class,
            'uuid',
            $UUID,
        );

        throw_if(! $order, ResourceNotFoundException::class, 'Invalid order');

        $record = $order->load('items', 'shipping', 'payment');

        return new OrderResource($record);
    }
}
