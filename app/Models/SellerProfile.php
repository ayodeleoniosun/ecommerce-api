<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProfile extends Model
{
    protected $guarded = ['id', 'uuid'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
