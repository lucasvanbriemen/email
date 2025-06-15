<?php


namespace App\Helpers;

use Cloudstudio\Ollama\Facades\Ollama;

class OllamaHelper
{
    public static function ollama($system_promt, $promt)
    {
        return Ollama::agent($system_promt)
            ->prompt($promt)
            ->options(['temperature' => 0.8])
            ->stream(false)
            ->ask();
    }
}
