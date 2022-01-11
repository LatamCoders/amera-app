<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmeraAdmin extends Model
{
    use HasFactory;

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
