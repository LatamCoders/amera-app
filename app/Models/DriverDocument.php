<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DriverDocument extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'driver_license',
        'driver_license_check',
        'proof_of_insurance',
        'proof_of_insurance_check'
    ];

    public function Driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
