<?php

namespace App\ScreenMessages;

class CallbackUrl
{
    public static function to(string $path): string
    {
        $domain = rtrim((string) config('app.callback_domain'), '/');
        $path = '/'.ltrim($path, '/');

        return $domain.$path;
    }
}
