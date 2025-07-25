<?php

namespace App\Domain\Payment\Interfaces\Wallet;

use App\Infrastructure\Models\Payment\Wallet\WalletAuditLog;

interface WalletAuditLogRepositoryInterface
{
    public function create(array $data): WalletAuditLog;
}
