<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service',
        'to',
        'time',
        'price',
    ];

    public function Booking() :BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
