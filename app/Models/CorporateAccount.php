<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class CorporateAccount extends Model
{
    use HasFactory;

    public function CorporateAccountPersonalInfo(): HasOne
    {
        return $this->hasOne(CorporateAccountPersonalInfo::class, 'corporate_account_id');
    }
}
