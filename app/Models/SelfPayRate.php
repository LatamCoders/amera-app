<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelfPayRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate',
        'comments',
        'driver_id',
        'booking_id'
    ];

    public function Driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function Booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
