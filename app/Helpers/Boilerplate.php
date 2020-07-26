<?php

namespace App\Helpers;

class Boilerplate
{
    /**
     * @param string $key
     * @return bool
     */
    public static function isEnabled($key): bool
    {
        return config('config.' . $key);
    }
}
