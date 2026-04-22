<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// =============================================
// SPMB Task Scheduling
// =============================================

// Cek pendaftar belum bayar setiap jam 00:00 tengah malam
Schedule::command('spmb:expire-pendaftar')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('[Scheduler] spmb:expire-pendaftar berhasil dijalankan');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('[Scheduler] spmb:expire-pendaftar GAGAL dijalankan!');
    });

// Kirim reminder tambahan setiap jam 08:00 pagi
Schedule::command('spmb:expire-pendaftar')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground();
