<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Fetching Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains settings for the email fetching system
    | that dispatches jobs to fetch emails from multiple IMAP accounts in parallel.
    |
    */

    /*
    | Fetch Interval (seconds)
    | How often the dispatcher sends jobs to fetch emails from each profile
    | Recommended: 30 seconds for balance between responsiveness and load
    */
    'fetch_interval' => env('EMAIL_FETCH_INTERVAL', 30),

    /*
    | Queue Name
    | The queue to dispatch email fetching jobs to
    */
    'queue_name' => env('EMAIL_FETCH_QUEUE', 'email_fetching'),

    /*
    | Maximum Retries
    | Number of times to retry a failed job
    */
    'max_retries' => env('EMAIL_FETCH_MAX_RETRIES', 3),

    /*
    | Timeout (seconds)
    | Maximum time allowed for a single job to run
    */
    'timeout' => env('EMAIL_FETCH_TIMEOUT', 120),

    /*
    | Enabled
    | Whether the email fetching system is active
    */
    'enabled' => env('EMAIL_FETCH_ENABLED', true),

    /*
    | Retry After (seconds)
    | Time to wait before retrying a failed job
    */
    'retry_after' => env('EMAIL_FETCH_RETRY_AFTER', 90),
];
