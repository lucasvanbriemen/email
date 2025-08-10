<?php


namespace App\Helpers;

class IcsHelper
{
    public static function parseIcsContent(string $icsContent): array
    {
        $lines = explode("\n", $icsContent);
        $events = [];
        $currentEvent = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if (strpos($line, 'BEGIN:VEVENT') === 0) {
                $currentEvent = [];
            } elseif (strpos($line, 'END:VEVENT') === 0 && $currentEvent) {
                $events[] = $currentEvent;
                $currentEvent = null;
            } elseif ($currentEvent !== null) {
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $currentEvent[trim($parts[0])] = trim($parts[1]);
                }
            }
        }

        return $events;
    }
}
