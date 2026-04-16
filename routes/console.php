<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your scheduled tasks. These
| tasks are executed on a regular schedule using Laravel's task scheduler.
| Register your scheduled commands here.
|
*/

// Clean up expired password reset tokens daily at 2 AM
Schedule::command('auth:clear-resets')->dailyAt('02:00');

// Dispatch queued jobs for social media posts that are due to publish
if (config('business.features.social_media', false)) {
    Schedule::command('social:publish-scheduled')->everyMinute();
}
