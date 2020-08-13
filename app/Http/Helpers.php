<?php

namespace App\Http;

use Illuminate\Support\Facades\Storage;

class Helpers
{
    public static function save64(string $destin, string $image_64)
    {
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf

        $replace = substr($image_64, 0, strpos($image_64, ',')+1);

        // find substring fro replace here eg: data:image/png;base64,

        $image = str_replace($replace, '', $image_64);

        $image = str_replace(' ', '+', $image);

        Storage::disk('gcs')->put($destin, base64_decode($image));
    }

    public static function delete(string $path)
    {
        Storage::disk('gcs')->delete($path);
    }
}
