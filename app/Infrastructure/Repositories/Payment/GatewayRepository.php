<?php

namespace App\Infrastructure\Repositories\Payment;

use App\Domain\Payment\Interfaces\GatewayRepositoryInterface;
use App\Infrastructure\Repositories\BaseRepository;

class GatewayRepository extends BaseRepository implements GatewayRepositoryInterface {}
