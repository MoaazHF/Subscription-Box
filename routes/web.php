<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\BoxCustomizationController;

// PUBLIC
Route::get('/plans', [SubscriptionController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// AUTH REQUIRED
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    
    // Subscription lifecycle
    Route::get('/subscribe/{plan}', [SubscriptionController::class, 'create']);
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::get('/dashboard-data', [SubscriptionController::class, 'dashboard']);
    
    Route::post('/subscriptions/{id}/pause', [SubscriptionController::class, 'pause']);
    Route::post('/subscriptions/{id}/resume', [SubscriptionController::class, 'resume']);
    
    // F5: Upgrade/Downgrade
    Route::post('/subscriptions/{id}/upgrade', [SubscriptionController::class, 'upgrade']);
    Route::post('/subscriptions/{id}/downgrade', [SubscriptionController::class, 'downgrade']);
    
    // F10: Billing Sync
    Route::post('/subscriptions/{id}/sync-billing', [SubscriptionController::class, 'syncBillingDay']);
    
    // F11: Tax preview (can be called before any payment)
    Route::get('/tax-preview', [SubscriptionController::class, 'taxPreview']);
    
    // Address
    Route::post('/addresses', [AddressController::class, 'store']);
    
    // Boxes
    Route::get('/boxes', [BoxController::class, 'index']);
    Route::get('/boxes/{box}', [BoxController::class, 'show']);
    Route::get('/boxes/{box}/customize', [BoxCustomizationController::class, 'show']);
    Route::post('/boxes/{box}/swap', [BoxCustomizationController::class, 'swap']);
    Route::delete('/boxes/{box}/items/{boxItem}', [BoxCustomizationController::class, 'remove']);
    
    // ADMIN ONLY (F40 + F41)
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/audit-logs', function () {
            return response()->json([
                'logs' => \App\Models\AuditLog::with('user')->latest()->paginate(50)
            ]);
        });
        
        Route::get('/admin/users', function () {
            return response()->json([
                'users' => \App\Models\User::with('role')->paginate(50)
            ]);
        });
    });
    
});