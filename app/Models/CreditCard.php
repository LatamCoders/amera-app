<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
        'ccv',
        'date',
        'selfpay_id',
    ];

    public function SelfPay(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SelfPay::class, 'selfpay_id');
    }
}
