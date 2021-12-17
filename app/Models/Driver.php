<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Driver extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'driver_id',
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

    /*
     * Relationships
     */
    public function Booking(): HasMany
    {
        return $this->hasMany(Booking::class, 'driver_id');
    }

    public function Vehicle(): HasOne
    {
        return $this->hasOne(Vehicle::class, 'driver_id');
    }

    public function DriverDocuments(): HasOne
    {
        return $this->hasOne(DriverDocument::class, 'driver_id');
    }

    public function Experiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'driver_id');
    }

    public function SelfPayRate(): HasMany
    {
        return $this->hasMany(SelfPayRate::class, 'driver_id');
    }
}
