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
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);

                    // Store multiple ATTENDEE entries
                    if (strpos($key, 'ATTENDEE') === 0) {
                        $currentEvent['ATTENDEE'][] = $value;
                    } else {
                        $currentEvent[$key] = $value;
                    }
                }
            }
        }

        return $events;
    }

    /**
     * Extract and normalize an ICS date-time for a given property (e.g., DTSTART, DTEND).
     * Handles variations including Zulu (UTC), TZID, and date-only values.
     * Returns a MySQL-friendly datetime string in the requested timezone or null when unavailable.
     */
    public static function getDateTime(array $event, string $property, string $targetTimezone = 'Europe/Amsterdam'): ?string
    {
        $value = null;
        $tzid = null;

        // Find the first key that starts with the requested property name
        foreach ($event as $key => $val) {
            if (stripos($key, $property) === 0) {
                $value = trim((string) $val);

                // Parse params present in key like: DTSTART;TZID=Europe/Amsterdam;VALUE=DATE
                $paramsPart = substr($key, strlen($property));
                if ($paramsPart) {
                    $paramsPart = ltrim($paramsPart, ';');
                    $params = array_filter(array_map('trim', explode(';', $paramsPart)));
                    foreach ($params as $param) {
                        $bits = explode('=', $param, 2);
                        $pKey = strtoupper(trim($bits[0] ?? ''));
                        $pVal = trim($bits[1] ?? '');
                        if ($pKey === 'TZID' && $pVal) {
                            $tzid = $pVal;
                        }
                    }
                }
                break;
            }
        }

        if (!$value) {
            return null;
        }

        // Attempt to parse common ICS formats
        try {
            // Lazy-load Carbon without hard dependency at top of file
            $carbonClass = '\\Carbon\\Carbon';

            // DATE only: YYYYMMDD
            if (preg_match('/^\d{8}$/', $value)) {
                /** @var \Carbon\Carbon $dt */
                $dt = $carbonClass::createFromFormat('Ymd', $value, $tzid ?: 'UTC')->startOfDay();
                return $dt->setTimezone($targetTimezone)->toDateTimeString();
            }

            // UTC Zulu: YYYYMMDDTHHMMSSZ
            if (preg_match('/^\d{8}T\d{6}Z$/', $value)) {
                /** @var \Carbon\Carbon $dt */
                $dt = $carbonClass::createFromFormat('Ymd\\THis\\Z', $value, 'UTC');
                return $dt->setTimezone($targetTimezone)->toDateTimeString();
            }

            // Local with explicit offset: YYYYMMDDTHHMMSS+HHMM or -HHMM
            if (preg_match('/^\d{8}T\d{6}[+-]\d{4}$/', $value)) {
                /** @var \Carbon\Carbon $dt */
                $dt = $carbonClass::createFromFormat('Ymd\\THisO', $value);
                return $dt->setTimezone($targetTimezone)->toDateTimeString();
            }

            // Local without offset but TZID provided: YYYYMMDDTHHMMSS
            if (preg_match('/^\d{8}T\d{6}$/', $value)) {
                /** @var \Carbon\Carbon $dt */
                $dt = $carbonClass::createFromFormat('Ymd\\THis', $value, $tzid ?: 'UTC');
                return $dt->setTimezone($targetTimezone)->toDateTimeString();
            }

            // Fallback parse attempt
            /** @var \Carbon\Carbon $dt */
            $dt = $carbonClass::parse($value, $tzid ?: 'UTC');
            return $dt->setTimezone($targetTimezone)->toDateTimeString();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
