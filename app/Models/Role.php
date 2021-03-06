<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'role'
    ];

    public function AmeraUser(): HasMany
    {
        return $this->hasMany(AmeraUser::class, 'role');
    }
}
