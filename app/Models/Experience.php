<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'amera_rate',
        'comments',
        'driver_id',
        'selfpay_id',
        'booking_id',
    ];

    /*
     * Relationships
     */
    public function SelfPay(): BelongsTo
    {
        return $this->belongsTo(SelfPay::class, 'selfpay_id');
    }

    public function Driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function Booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
