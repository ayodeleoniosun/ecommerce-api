<?php

namespace App\Domain\Order\Actions\Order;

use App\Domain\Order\Interfaces\Order\OrderRepositoryInterface;
use App\Domain\Order\Resources\Order\OrderResourceCollection;

class GetOrdersAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    public function execute(string $currency): OrderResourceCollection
    {
        return new OrderResourceCollection($this->orderRepository->index($currency));
    }
}
