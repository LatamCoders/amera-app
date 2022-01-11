<?php

namespace App\utils;

use Illuminate\Support\Str;

class UniqueIdentifier
{
    public static function GenerateUid(): string
    {
        return strtolower(Str::random(30));
    }
}
