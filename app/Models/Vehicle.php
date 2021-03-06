<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'color',
        'year',
        'plate_number',
        'vin_number'
    ];

    public function Driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function VehicleDocuments(): HasOne
    {
        return $this->hasOne(VehicleDocument::class, 'vehicle_id');
    }
}
