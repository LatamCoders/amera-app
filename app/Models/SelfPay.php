<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class SelfPay extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory;

    public $table = 'selfpay';

    protected $fillable = [
        'client_id',
        'name',
        'lastname',
        'gender',
        'birthday',
        'phone_number',
        'email',
        'address',
        'profile_picture'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function CreditCard(): HasOne
    {
        return $this->hasOne(CreditCard::class, 'selfpay_id');
    }

    public function Booking(): HasMany
    {
        return $this->hasMany(Booking::class, 'selfpay_id');
    }

    public function AdditionalService(): HasMany
    {
        return $this->hasMany(AdditionalService::class, 'selfpay_id');
    }
}
