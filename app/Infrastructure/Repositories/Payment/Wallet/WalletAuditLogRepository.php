<?php

namespace App\Infrastructure\Repositories\Payment\Wallet;

use App\Domain\Payment\Interfaces\Wallet\WalletAuditLogRepositoryInterface;
use App\Infrastructure\Models\Payment\Wallet\WalletAuditLog;
use App\Infrastructure\Repositories\BaseRepository;

class WalletAuditLogRepository extends BaseRepository implements WalletAuditLogRepositoryInterface
{
    public function create(array $data): WalletAuditLog
    {
        return WalletAuditLog::create($data);
    }
}
