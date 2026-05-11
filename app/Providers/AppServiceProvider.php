<?php

namespace App\Providers;

use App\Models\Claim;
use App\Models\Delivery;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\User;
use App\Policies\ClaimPolicy;
use App\Policies\DeliveryPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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

        View::composer('layouts.app', function ($view): void {
            if (! Auth::check()) {
                $view->with([
                    'headerNotificationCount' => 0,
                    'headerRecentNotifications' => collect(),
                ]);

                return;
            }

            $query = Notification::query()->where('user_id', (string) Auth::id());

            $view->with([
                'headerNotificationCount' => (clone $query)->count(),
                'headerRecentNotifications' => (clone $query)
                    ->latest('created_at')
                    ->limit(6)
                    ->get(['id', 'event_type', 'subject', 'status', 'created_at']),
            ]);
        });
    }
}
