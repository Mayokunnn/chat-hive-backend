<?php

use Illuminate\Support\Carbon;

if (!function_exists('quickRandom')) {
    function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
}

if (!function_exists('now')) {
    function now($timezone = null)
    {
        return Carbon::now($timezone);
    }
}
