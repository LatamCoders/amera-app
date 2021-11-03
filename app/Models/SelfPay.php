<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfPay extends Model
{
    use HasFactory;

    public $table = 'selfpay';

    protected $fillable = [
        'client_id',
        'name',
        'lastname',
        'phone_number',
        'email',
        'address',
        'profile_picture'
    ];
}
