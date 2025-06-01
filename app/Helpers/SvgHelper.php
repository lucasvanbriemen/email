<?php

namespace App\Helpers;

class SvgHelper
{
    public static function svg($svg_name)
    {
        // Get the root of the project
        $path = base_path('public/images/' . $svg_name . '.svg');

        if (!file_exists($path)) {
            return 'No SVG found';
        }

        return file_get_contents($path);
    }
}
