<?php

namespace App\Infrastructure\Models\User;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\Cart\Wishlist;
use App\Infrastructure\Models\Inventory\Product;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Shipping\Address\CustomerShippingAddress;
use Database\Factories\User\UserFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property mixed $firstname
 * @property mixed $lastname
 * @property mixed $email
 * @property mixed $email_verified_at
 *
 * @method static create(array $array)
 * @method static where(string $string, string $email)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, MustVerifyEmail, Notifiable, UtilitiesTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    protected $guard_name = 'api';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'uuid',
        'password',
        'remember_token',
    ];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = self::generateUUID();
        });
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(UserCart::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function verification(): HasOne
    {
        return $this->hasOne(UserVerification::class);
    }

    public function shippingAddresses(): HasMany
    {
        return $this->hasMany(CustomerShippingAddress::class);
    }

    public function customer(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    protected function fullname(): Attribute
    {
        return Attribute::make(
            get: function () {
                return ucwords("{$this->firstname} {$this->lastname}");
            },
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
