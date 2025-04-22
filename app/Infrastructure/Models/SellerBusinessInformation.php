<?php

namespace App\Infrastructure\Models;

use Database\Factories\SellerBusinessInformationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static updateOrCreate(array $array, array $toArray)
 * @method static where(string $string, string $email)
 */
class SellerBusinessInformation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    protected $table = 'seller_business_information';

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

    protected static function newFactory(): SellerBusinessInformationFactory
    {
        return SellerBusinessInformationFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
