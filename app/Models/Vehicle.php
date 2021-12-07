<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_front_image',
        'vehicle_front_image_check',
        'vehicle_rear_image',
        'vehicle_rear_image_check',
        'vehicle_side_image',
        'vehicle_side_image_check',
        'vehicle_interior_image',
        'vehicle_interior_image_check',
        'driver_license',
        'driver_license_check',
        'proof_of_insurance',
        'proof_of_insurance_check'
    ];

    public function Driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
