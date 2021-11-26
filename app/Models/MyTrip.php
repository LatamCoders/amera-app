<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyTrip extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'from',
        'to',
        'trip_start',
        'trip_end'
    ];

    public function SelfPay(): BelongsTo
    {
        return $this->belongsTo(SelfPay::class, 'selfpay_id');
    }
}
