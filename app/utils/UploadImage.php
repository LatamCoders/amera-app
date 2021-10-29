<?php

namespace App\utils;

class UploadImage
{
    public static function UploadProfileImage($image, $number)
    {
        $profileImage = $number . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('profiles'), $profileImage);

        return asset('profiles/' . $number . '.' . $image->getClientOriginalExtension());
    }
}
