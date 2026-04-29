<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\BoxCustomizationController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DeliveryZoneController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverManagementController;
use App\Http\Controllers\FlashSaleController;
use App\Http\Controllers\GiftSubscriptionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\RetentionOfferController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\SocialPostController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\WarehouseStaffController;
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

    Route::get('/boxes', [BoxController::class, 'index'])->name('boxes.index');
    Route::get('/boxes/{box}', [BoxController::class, 'show'])->name('boxes.show');
    Route::get('/boxes/{box}/customize', [BoxCustomizationController::class, 'show'])->name('boxes.customize');
    Route::post('/boxes/{box}/swap', [BoxCustomizationController::class, 'swap'])->name('boxes.swap');
    Route::post('/boxes/{box}/add', [BoxCustomizationController::class, 'add'])->name('boxes.add');
    Route::delete('/boxes/{box}/items/{boxItem}', [BoxCustomizationController::class, 'remove'])->name('boxes.remove');

    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/{delivery}', [DeliveryController::class, 'show'])->name('deliveries.show');
    Route::post('/deliveries/{delivery}/claims', [ClaimController::class, 'store'])->name('deliveries.claims.store');
    Route::patch('/deliveries/{delivery}/status', [DeliveryController::class, 'updateStatus'])
        ->middleware('role:admin')
        ->name('deliveries.update-status');

    Route::middleware('role:driver')->group(function () {
        Route::get('/driver', [DriverController::class, 'index'])->name('driver.index');
        Route::patch('/driver/deliveries/{delivery}/status', [DriverController::class, 'updateStatus'])->name('driver.deliveries.status');
    });

    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::post('/referrals', [ReferralController::class, 'store'])->name('referrals.store');
    Route::patch('/referrals/{referral}/confirm', [ReferralController::class, 'confirm'])->name('referrals.confirm');
    Route::patch('/referrals/{referral}/reject', [ReferralController::class, 'reject'])->name('referrals.reject');

    Route::get('/promo-codes', [PromoCodeController::class, 'index'])->name('promo-codes.index');
    Route::post('/promo-codes', [PromoCodeController::class, 'store'])->middleware('role:admin')->name('promo-codes.store');
    Route::post('/promo-code-usages', [PromoCodeController::class, 'apply'])->name('promo-code-usages.store');

    Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
    Route::post('/rewards', [RewardController::class, 'issue'])->middleware('role:admin')->name('rewards.issue');
    Route::patch('/rewards/{reward}/apply', [RewardController::class, 'apply'])->name('rewards.apply');

    Route::get('/gift-subscriptions', [GiftSubscriptionController::class, 'index'])->name('gift-subscriptions.index');
    Route::post('/gift-subscriptions/purchase', [GiftSubscriptionController::class, 'purchase'])->name('gift-subscriptions.purchase');
    Route::post('/gift-subscriptions/activate', [GiftSubscriptionController::class, 'activate'])->name('gift-subscriptions.activate');

    Route::get('/flash-sales', [FlashSaleController::class, 'index'])->name('flash-sales.index');
    Route::post('/flash-sales', [FlashSaleController::class, 'store'])->middleware('role:admin')->name('flash-sales.store');
    Route::post('/flash-sales/{flashSale}/claim', [FlashSaleController::class, 'claim'])->name('flash-sales.claim');

    Route::get('/social-posts', [SocialPostController::class, 'index'])->name('social-posts.index');
    Route::post('/social-posts', [SocialPostController::class, 'store'])->name('social-posts.store');
    Route::delete('/social-posts/{socialPost}', [SocialPostController::class, 'destroy'])->name('social-posts.destroy');

    Route::get('/retention-offers', [RetentionOfferController::class, 'index'])->name('retention-offers.index');
    Route::post('/subscriptions/{subscription}/retention-offers', [RetentionOfferController::class, 'store'])->name('retention-offers.store');
    Route::patch('/retention-offers/{retentionOffer}', [RetentionOfferController::class, 'update'])->name('retention-offers.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/sent', [NotificationController::class, 'markSent'])->name('notifications.mark-sent');

    Route::middleware('role:admin')->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        Route::get('/ops/drivers', [DriverManagementController::class, 'index'])->name('drivers.index');
        Route::post('/ops/drivers', [DriverManagementController::class, 'store'])->name('drivers.store');
        Route::patch('/ops/drivers/{driver}/toggle', [DriverManagementController::class, 'toggle'])->name('drivers.toggle');

        Route::get('/ops/warehouse-staff', [WarehouseStaffController::class, 'index'])->name('warehouse-staff.index');
        Route::post('/ops/warehouse-staff', [WarehouseStaffController::class, 'store'])->name('warehouse-staff.store');

        Route::get('/ops/delivery-zones', [DeliveryZoneController::class, 'index'])->name('delivery-zones.index');
        Route::post('/ops/delivery-zones', [DeliveryZoneController::class, 'store'])->name('delivery-zones.store');
        Route::patch('/ops/delivery-zones/{deliveryZone}/toggle', [DeliveryZoneController::class, 'toggleServiceability'])->name('delivery-zones.toggle');
    });
});
