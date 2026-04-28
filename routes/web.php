<?php

use App\Http\Controllers\AdminDeliveryController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\BoxCustomizationController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/boxes', [BoxController::class, 'index'])->name('boxes.index');
    Route::get('/boxes/{box}', [BoxController::class, 'show'])->name('boxes.show');

    // Customization routes
    Route::get('/boxes/{box}/customize', [BoxCustomizationController::class, 'show'])->name('boxes.customize');
    Route::post('/boxes/{box}/swap', [BoxCustomizationController::class, 'swap'])->name('boxes.swap');
    Route::delete('/boxes/{box}/items/{boxItem}', [BoxCustomizationController::class, 'remove'])->name('boxes.remove');

    // Delivery routes
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/{delivery}', [DeliveryController::class, 'show'])->name('deliveries.show');
    Route::patch('/deliveries/{delivery}', [DeliveryController::class, 'update'])->name('deliveries.update');

    // Claims routes
    Route::get('/deliveries/{delivery}/claims/create', [ClaimController::class, 'create'])->name('claims.create');
    Route::post('/deliveries/{delivery}/claims', [ClaimController::class, 'store'])->name('claims.store');
    Route::get('/claims/{claim}', [ClaimController::class, 'show'])->name('claims.show');
});

// Driver routes (uses driver_id query param until a dedicated driver auth guard is added)
Route::get('/driver/deliveries', [DriverController::class, 'index'])->name('driver.index');
Route::get('/driver/deliveries/{delivery}', [DriverController::class, 'show'])->name('driver.show');
Route::patch('/driver/deliveries/{delivery}', [DriverController::class, 'update'])->name('driver.update');

// Admin routes (protect with admin middleware when auth roles are implemented by Team 1)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/deliveries', [AdminDeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/{delivery}', [AdminDeliveryController::class, 'show'])->name('deliveries.show');
    Route::patch('/deliveries/{delivery}', [AdminDeliveryController::class, 'update'])->name('deliveries.update');
    Route::patch('/claims/{claim}', [AdminDeliveryController::class, 'updateClaim'])->name('claims.update');
});
