<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'name',
        'lastname',
        'gender',
        'birthday',
        'phone_number',
        'email',
        'address',
        'profile_picture',
        'vehicle_front_image',
        'vehicle_rear_image',
        'vehicle_side_image',
        'vehicle_interior_image',
        'driver_license',
        'proof_of_insurance'
    ];

    public function Booking(): HasMany
    {
        return $this->hasMany(Booking::class, 'driver_id');
    }
}
