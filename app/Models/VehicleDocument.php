<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDocument extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'vehicle_front_image',
        'vehicle_front_image_check',
        'vehicle_rear_image',
        'vehicle_rear_image_check',
        'vehicle_side_image',
        'vehicle_side_image_check',
        'vehicle_interior_image',
        'vehicle_interior_image_check'
    ];

    public function Vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
