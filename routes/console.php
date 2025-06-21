<?php

use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule untuk menjalankan update expired membership setiap hari jam 00:01
Schedule::command('membership:update-expired')
    ->dailyAt('00:01')
    ->withoutOverlapping();