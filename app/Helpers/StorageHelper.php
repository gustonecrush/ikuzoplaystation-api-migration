<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class StorageHelper
{
    public static function store($file, string $to)
    {
        return $file->storeAs(
            $to,
            Str::random(40) . '.' . $file->getClientOriginalExtension(),
            'public'
        );
    }
    
    public static function storePng($file, string $to)
    {
        return $file->storeAs($to, Str::random(40) . '.png', 'public');
    }
}