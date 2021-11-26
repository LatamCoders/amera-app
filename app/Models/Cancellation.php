<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cancellation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'status',
        'fee',
        'cancellation_date'
    ];

    public function Booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
