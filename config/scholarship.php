<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Deadline
    |--------------------------------------------------------------------------
    |
    | The deadline after which applicants can no longer create, edit, or
    | submit applications. Set as a date string in 'Y-m-d' format.
    | The cutoff is 11:59 PM (23:59:59) on the specified date.
    |
    */
    'application_deadline' => env('APPLICATION_DEADLINE', '2026-07-15'),
];
