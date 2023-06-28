<?php

namespace App\Helpers;

class Otp
{
    public static $otpExpiration = 60;

    public static function generate(array $range){
        if (empty($range)) { 
            return rand(100000,999999);
        }
        return rand($range[0],$range[1]);
    }
}