<?php

namespace App\Utils;

class Anonymizer
{
    public static function email(string $email): string
    {
        $regex = '/(.{1,5})(.+)(@.+)/';
        $replace = '$1*****$3';

        return preg_replace($regex, $replace, $email);
    }

    public static function phone(string $phone): string
    {
        $phone = str_replace(' ', '', $phone);

        $regex = '/^(\d{2})(\d{3})(\d{4})(\d{2})$/';
        $replace = '($1) $2**-**$4';

        return preg_replace($regex, $replace, $phone);
    }
}