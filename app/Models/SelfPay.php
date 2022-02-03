<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'city',
        'note',
        'profile_picture',
        'active'
    ];

    protected $hidden = ['ca_id'];

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

    /*
     * Relationships
     */
    public function CreditCard(): HasOne
    {
        return $this->hasOne(CreditCard::class, 'selfpay_id');
    }

    public function Booking(): HasMany
    {
        return $this->hasMany(Booking::class, 'selfpay_id');
    }

    public function Experiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'selfpay_id');
    }

    public function DriverRate(): HasMany
    {
        return $this->hasMany(DriverRate::class, 'selfpay_id');
    }

    public function SelfpayRate(): HasMany
    {
        return $this->hasMany(SelfPayRate::class, 'selfpay_id');
    }

    public function CorporateAccount(): BelongsTo
    {
        return $this->belongsTo(CorporateAccount::class, 'ca_id');
    }
}
