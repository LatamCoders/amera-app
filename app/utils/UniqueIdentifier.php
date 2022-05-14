<?php

namespace App\utils;

use Illuminate\Support\Str;

class UniqueIdentifier
{
    public static function GenerateUid(): string
    {
        return strtolower(Str::random(30));
    }

    public static function GenerateRandomPassword($length = 10): string
    {
        $string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ*#$%&?';

        return substr(str_shuffle($string), 0, $length);
    }
}
