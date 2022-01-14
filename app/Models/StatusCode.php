<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusCode extends Model
{
    use HasFactory;

    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'code',
        'status'
    ];

    public function Booking(): HasMany
    {
        return $this->hasMany(Booking::class, 'status');
    }

    public function Cancellation(): HasMany
    {
        return $this->hasMany(Cancellation::class, 'status');
    }
}
