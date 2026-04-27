<?php

use App\Http\Controllers\BoxController;
use App\Http\Controllers\BoxCustomizationController;
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
});

Route::get('/', function () {
    return view('welcome');
});


Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');


Route::get('/subscribe/{plan}', [SubscriptionController::class, 'create'])->name('subscribe');