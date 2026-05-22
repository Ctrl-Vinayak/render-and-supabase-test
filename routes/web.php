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
    $laravelMemory = round(memory_get_usage(true) / 1024 / 1024, 2);
    $totalMem = 'Onbekend (Geen Linux)';
    
    if (file_exists('/proc/meminfo')) {
        $meminfo = file_get_contents('/proc/meminfo');
        $totalMem = substr($meminfo, 0, 200); 
    }

    return response()->json([
        'laravel_ram_mb' => $laravelMemory,
        'container_meminfo_raw' => explode("\n", $totalMem)
    ]);
});