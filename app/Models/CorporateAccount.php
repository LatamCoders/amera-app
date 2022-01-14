<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class CorporateAccount extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $hidden = ['amera_user_id'];

    public function CorporateAccountPersonalInfo(): HasOne
    {
        return $this->hasOne(CorporateAccountPersonalInfo::class, 'corporate_account_id');
    }

    public function CorporateAccountPaymentMethod(): HasOne
    {
        return $this->hasOne(CorportateAccountPaymentMethod::class, 'corporate_account_id');
    }

    public function AmeraUser(): BelongsTo
    {
        return $this->belongsTo(AmeraUser::class, 'amera_user_id');
    }
}
