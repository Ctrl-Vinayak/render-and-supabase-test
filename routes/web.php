<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/check-status-render-xyz', function() {
    // 1. CPU-tijd van dit specifieke Laravel verzoek (User + System time)
    $cpuUsage = getrusage();
    $cpuTimeUser = ($cpuUsage['ru_utime.tv_sec'] * 1000) + intval($cpuUsage['ru_utime.tv_usec'] / 1000);
    $cpuTimeSys  = ($cpuUsage['ru_stime.tv_sec'] * 1000) + intval($cpuUsage['ru_stime.tv_usec'] / 1000);
    $totaleCpuTijdMs = $cpuTimeUser + $cpuTimeSys;

    // 2. Algemene serverbelasting (Laatste 1, 5 en 15 minuten)
    // Let op: Net als bij de RAM-uitlezing is dit de load van de héle Render-hostmachine.
    $loadAvg = function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];

    return response()->json([
        'laravel_geheugen' => [
            'huidig_ram_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'piek_ram_mb'   => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'max_limiet'    => '512 MB (Render Free Plan)'
        ],
        'laravel_cpu_verbruik' => [
            'script_verwerkingstijd_cpu_ms' => $totaleCpuTijdMs . ' ms',
            'uitleg' => 'Dit is de pure CPU-rekenkracht die nodig was voor deze specifieke pagina-aanvraag.'
        ],
        'render_server_belasting' => [
            'load_afgelopen_1_minuut'  => round($loadAvg[0], 2),
            'load_afgelopen_5_minuten' => round($loadAvg[1], 2),
            'uitleg' => 'Een load onder de 1.00 betekent dat de CPU van de server het rustig heeft.'
        ]
    ]);
});