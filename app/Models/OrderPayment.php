<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPayment extends Model
{
    protected $guarded = ['id', 'uuid'];

    public function order(): BelongsTo {
        return $this->belongsTo(Order::class);
    }
}
