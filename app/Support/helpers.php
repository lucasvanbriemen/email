<?php

use App\Helpers\GravatarHelper;
use App\Helpers\SvgHelper;
use App\Helpers\ReadableTimeHelper;
use App\Helpers\IcsHelper;

function gravar(string $email, int $size = 80): string
{
    return GravatarHelper::gravar($email, $size);
}

function svg(string $name): string
{
    return SvgHelper::svg($name);
}

function currentUser()
{
    $current_user = app('current_user');

    // Convert to object if it's an array or an json string
    if (is_array($current_user)) {
        $current_user = (object) $current_user;
    } elseif (is_string($current_user)) {
        $current_user = json_decode($current_user);
    }

    return $current_user;
}

function readableTime(string $date): string
{
    return ReadableTimeHelper::convertDateTimeToReadableTime($date);
}

function parseIcsContent(string $icsContent): array
{
    return IcsHelper::parseIcsContent($icsContent);
}