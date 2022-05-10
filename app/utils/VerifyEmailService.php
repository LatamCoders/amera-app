<?php

namespace App\utils;

use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class VerifyEmailService
{
    public static function SendCode($email, $class, $key, $expiredTime = 5)
    {
        $code = rand(10000, 99999);

        Mail::to($email)->send(new $class($code));

        Cache::put($key, $code, now()->addMinutes($expiredTime));
    }

    public static function VerifyCode($inCode, $key): string
    {
       $code = Cache::get($key);

       if ($code != (int)$inCode) {
           throw new BadRequestException("Invalid code");
       }

        return 'Code verify successfully';
    }
}
