<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporateAccountPersonalInfo extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function CorporateAccount(): BelongsTo
    {
        return $this->belongsTo(CorporateAccount::class, 'corporate_account_id');
    }
}
