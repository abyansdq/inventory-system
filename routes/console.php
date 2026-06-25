<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cek stok menipis setiap hari pukul 08:00
Schedule::command('inventory:check-stock')->dailyAt('08:00');

// Bersihkan activity log lebih dari 90 hari
Schedule::command('activitylog:clean --days=90')->monthly();