<?php

use App\Helpers\GravatarHelper;
use App\Helpers\SvgHelper;
use App\Helpers\ReadableTimeHelper;
use App\Helpers\AiHelper;

function gravar(string $email, int $size = 80): string
{
    return GravatarHelper::gravar($email, $size);
}

function svg(string $name): string
{
    return SvgHelper::svg($name);
}

function aiSummery(string $text)
{
    return AiHelper::summarize($text);
}

function readableTime(string $date): string
{
    return ReadableTimeHelper::convertDateTimeToReadableTime($date);
}
