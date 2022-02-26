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
        'vehicle_front_image_cverify_at',
        'vehicle_rear_image',
        'vehicle_rear_image_verify_at',
        'vehicle_side_image',
        'vehicle_side_image_verify_at',
        'vehicle_interior_image',
        'vehicle_interior_image_verify_at'
    ];

    public function Vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
