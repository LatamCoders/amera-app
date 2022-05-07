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
        $string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-_*!#$%&/?¡¿';
        $stringLength = strlen($string);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $string[rand(0, $stringLength - 1)];
        }

        return $randomString;
    }
}
