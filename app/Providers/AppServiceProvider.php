<?php

namespace App\Providers;

use App\Models\Claim;
use App\Models\Delivery;
use App\Models\Subscription;
use App\Models\User;
use App\Policies\ClaimPolicy;
use App\Policies\DeliveryPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Subscription::class, SubscriptionPolicy::class);
        Gate::policy(Delivery::class, DeliveryPolicy::class);
        Gate::policy(Claim::class, ClaimPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
