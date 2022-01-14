<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmeraAdmin extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'user',
        'email',
        ];

    protected $hidden = ['amera_user_id'];

    public function AmeraUser(): BelongsTo
    {
        return $this->belongsTo(AmeraUser::class, 'amera_user_id');
    }
}
