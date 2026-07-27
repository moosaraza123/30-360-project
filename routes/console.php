<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Data minimisation: strip IPs from anonymous calculation records after 90 days
Schedule::command('calculations:prune-pii')->dailyAt('03:15');
