<?php

namespace App\utils;

use Illuminate\Support\Facades\Storage;

class UploadImage
{
    public static function UploadProfileImage($image, $number): ?string
    {
        if ($image == null || $image == '') {
            return null;
        }

        $filename = "$number.{$image->getClientOriginalExtension()}";

        Storage::disk('public')->putFileAs('profiles', $image, $filename);

        return Storage::disk('public')->url("profiles/$filename");
    }
}
