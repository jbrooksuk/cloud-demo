<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Demo\CacheController;
use App\Http\Controllers\Demo\DatabaseController;
use App\Http\Controllers\Demo\ExceptionController;
use App\Http\Controllers\Demo\ObjectStorageController;
use App\Http\Controllers\Demo\QueueController;
use App\Http\Controllers\Demo\WebsocketsController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('demo')->name('demo.')->group(function () {
        Route::get('websockets', [WebsocketsController::class, 'index'])->name('websockets');
        Route::post('websockets', [WebsocketsController::class, 'send'])->name('websockets.send');
        Route::post('websockets/typing', [WebsocketsController::class, 'typing'])->name('websockets.typing');

        Route::get('database', [DatabaseController::class, 'index'])->name('database');

        Route::get('object-storage', [ObjectStorageController::class, 'index'])->name('object-storage');
        Route::post('object-storage', [ObjectStorageController::class, 'upload'])->name('object-storage.upload');
        Route::delete('object-storage', [ObjectStorageController::class, 'destroy'])->name('object-storage.destroy');

        Route::get('cache', [CacheController::class, 'index'])->name('cache');
        Route::post('cache', [CacheController::class, 'store'])->name('cache.store');
        Route::post('cache/increment', [CacheController::class, 'increment'])->name('cache.increment');
        Route::delete('cache', [CacheController::class, 'flush'])->name('cache.flush');

        Route::get('queue', [QueueController::class, 'index'])->name('queue');
        Route::post('queue', [QueueController::class, 'dispatch'])->name('queue.dispatch');

        Route::get('exception', ExceptionController::class)->name('exception');
    });
});

require __DIR__.'/settings.php';
