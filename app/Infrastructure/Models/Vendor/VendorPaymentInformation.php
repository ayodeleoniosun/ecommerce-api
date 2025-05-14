<?php

namespace App\Infrastructure\Models\Vendor;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\User\User;
use Database\Factories\VendorPaymentInformationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static updateOrCreate(array $array, array $toArray)
 * @method static where(string $string, string $email)
 */
class VendorPaymentInformation extends Model
{
    use HasFactory, UtilitiesTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    protected $table = 'vendor_payment_information';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'uuid',
        'user_id',
    ];

    protected static function newFactory(): VendorPaymentInformationFactory
    {
        return VendorPaymentInformationFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = self::generateUUID();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
