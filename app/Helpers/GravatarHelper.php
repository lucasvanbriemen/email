<?php

namespace App\Helpers;

class GravatarHelper
{
    public static function gravar(string $email, int $size = 80): string
    {
        $email = strtolower(trim($email));
        $hash = md5($email);
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=identicon";
    }
}
