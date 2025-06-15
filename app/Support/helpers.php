<?php

use App\Helpers\GravatarHelper;
use App\Helpers\SvgHelper;
use App\Helpers\OllamaHelper;

function gravar(string $email, int $size = 80): string
{
    return GravatarHelper::gravar($email, $size);
}

function svg(string $name): string
{
    return SvgHelper::svg($name);
}

function ollama(string $system_prompt, string $prompt): array
{
    return OllamaHelper::ollama($system_prompt, $prompt);
}
