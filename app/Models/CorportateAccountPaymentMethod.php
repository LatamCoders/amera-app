<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorportateAccountPaymentMethod extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $hidden = ['corporate_account_id'];

    public function CorporateAccount(): BelongsTo
    {
        return $this->belongsTo(CorporateAccount::class, 'corporate_account_id');
    }
}
