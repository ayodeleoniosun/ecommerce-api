<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'uuid'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
