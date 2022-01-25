<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'selfpay_id',
        'booking_date',
        'start_date',
        'pickup_time',
        'city',
        'surgery_type',
        'appoinment_datetime',
        'from',
        'to',
        'driver_id'
    ];

    public function SelfPay(): BelongsTo
    {
        return $this->belongsTo(SelfPay::class, 'selfpay_id');
    }

    public function Driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function StatusCode(): BelongsTo
    {
        return $this->belongsTo(StatusCode::class, 'status');
    }

    public function Cancellation(): HasOne
    {
        return $this->hasOne(Cancellation::class, 'driver_id');
    }

    public function AmeraRate(): HasOne
    {
        return $this->hasOne(Experience::class, 'booking_id');
    }

    public function DriverRate(): HasOne
    {
        return $this->hasOne(Driver::class, 'booking_id');
    }

    public function SelfPayRate(): HasOne
    {
        return $this->hasOne(SelfPay::class, 'booking_id');
    }
}
