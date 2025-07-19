<?php


namespace App\Helpers;

class ReadableTimeHelper
{
    public static function convertDateTimeToReadableTime(string $dateTime): string
    {
        $timestamp = strtotime($dateTime);
        $startOfDay = strtotime('today');
        $timeDifference = $startOfDay - $timestamp;

        // Today
        if (date('Y-m-d', $timestamp) === date('Y-m-d')) {
            // Today at 12:00
            return 'Today at ' . date('H:i', $timestamp);
        }

        // Yesterday
        if (date('Y-m-d', $timestamp) === date('Y-m-d', strtotime('yesterday'))) {
            // Yesterday at 12:00
            return 'Yesterday at ' . date('H:i', $timestamp);
        }

        // This week
        $weekStart = strtotime('last Sunday');
        if ($timestamp >= $weekStart) {
            // Monday of the week at 12:00
            return date('l \a\t H:i', $timestamp);
        }

        // This month
        $monthStart = strtotime('first day of this month');
        if ($timestamp >= $monthStart) {
            // January 1 at 12:00
            return date('F j \a\t H:i', $timestamp);
        }

        // Older dates
        // January 1, 2023 at 12:00
        return date('F j, Y \a\t H:i', $timestamp);
    }
}
