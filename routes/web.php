<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\BoxCustomizationController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::get('/plans', [SubscriptionPlanController::class, 'index'])->name('plans.index');

Route::middleware('auth')->group(function () {
    // Team 1: Core system and subscription workflow.
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::patch('/subscriptions/{subscription}/pause', [SubscriptionController::class, 'pause'])->name('subscriptions.pause');
    Route::patch('/subscriptions/{subscription}/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
    Route::patch('/subscriptions/{subscription}/change-plan', [SubscriptionController::class, 'changePlan'])->name('subscriptions.change-plan');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

    Route::middleware('role:admin')->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // Team 2: Box and customization workflow.
    Route::get('/boxes', [BoxController::class, 'index'])->name('boxes.index');
    Route::get('/boxes/{box}', [BoxController::class, 'show'])->name('boxes.show');
    Route::get('/boxes/{box}/customize', [BoxCustomizationController::class, 'show'])->name('boxes.customize');
    Route::post('/boxes/{box}/swap', [BoxCustomizationController::class, 'swap'])->name('boxes.swap');
    Route::post('/boxes/{box}/add', [BoxCustomizationController::class, 'add'])->name('boxes.add');
    Route::delete('/boxes/{box}/items/{boxItem}', [BoxCustomizationController::class, 'remove'])->name('boxes.remove');

    // Team 3: Delivery tracking workflow.
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/{delivery}', [DeliveryController::class, 'show'])->name('deliveries.show');
    Route::post('/deliveries/{delivery}/claims', [ClaimController::class, 'store'])->name('deliveries.claims.store');
    Route::patch('/deliveries/{delivery}/status', [DeliveryController::class, 'updateStatus'])
        ->middleware('role:admin')
        ->name('deliveries.update-status');

    // Driver workflow
    Route::get('/driver', [DriverController::class, 'index'])->name('driver.index');
    Route::patch('/driver/deliveries/{delivery}/status', [DriverController::class, 'updateStatus'])->name('driver.deliveries.status');
});
