<?php

use App\Helpers\GravatarHelper;

if (!function_exists('gravar')) {
    function gravar(string $email, int $size = 80): string
    {
        return GravatarHelper::gravar($email, $size);
    }
}
