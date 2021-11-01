<?php

namespace App\utils;

use Illuminate\Support\Facades\Storage;

class UploadImage
{
    public static function UploadProfileImage($image, $number): string
    {
        $filename = "$number.{$image->getClientOriginalExtension()}";

        Storage::disk('spaces')->putFileAs('profiles', $image, $filename);

        return Storage::disk('spaces')->url("profiles/$filename");
    }
}
