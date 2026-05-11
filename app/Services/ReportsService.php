<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\Delivery;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReportsService
{
    /** @param array{q?:string,from?:string,to?:string,status?:string} $filters */
    public function rows(string $type, array $filters): Collection
    {
        return match ($type) {
            'subscriptions' => $this->subscriptionQuery($filters)->get(),
            'deliveries' => $this->deliveryQuery($filters)->get(),
            'claims' => $this->claimQuery($filters)->get(),
            'payments' => $this->paymentQuery($filters)->get(),
            'notifications' => $this->notificationQuery($filters)->get(),
            default => collect(),
        };
    }

    /** @param array{q?:string,from?:string,to?:string,status?:string} $filters */
    public function headings(string $type): array
    {
        return match ($type) {
            'subscriptions' => ['id', 'user_email', 'plan', 'status', 'auto_renew', 'start_date', 'next_billing_date'],
            'deliveries' => ['id', 'tracking_number', 'status', 'subscriber_email', 'estimated_delivery', 'eco_dispatch'],
            'claims' => ['id', 'type', 'status', 'delivery_id', 'subscription_id', 'submitted_at', 'resolved_at'],
            'payments' => ['id', 'subscription_id', 'amount', 'tax_amount', 'status', 'gateway_ref', 'created_at'],
            'notifications' => ['id', 'user_id', 'type', 'event_type', 'status', 'channel', 'retry_count', 'sent_at'],
            default => [],
        };
    }

    /** @param array{q?:string,from?:string,to?:string,status?:string} $filters */
    private function subscriptionQuery(array $filters): Builder
    {
        return Subscription::query()
            ->with(['user', 'plan'])
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->whereHas('user', fn (Builder $userQuery) => $userQuery->where('email', 'like', "%{$search}%"));
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to))
            ->latest('created_at');
    }

    /** @param array{q?:string,from?:string,to?:string,status?:string} $filters */
    private function deliveryQuery(array $filters): Builder
    {
        return Delivery::query()
            ->with(['box.subscription.user'])
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->where('tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('box.subscription.user', fn (Builder $userQuery) => $userQuery->where('email', 'like', "%{$search}%"));
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to))
            ->latest('created_at');
    }

    /** @param array{q?:string,from?:string,to?:string,status?:string} $filters */
    private function claimQuery(array $filters): Builder
    {
        return Claim::query()
            ->when($filters['q'] ?? null, fn (Builder $query, string $search) => $query->where('description', 'like', "%{$search}%"))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('submitted_at', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('submitted_at', '<=', $to))
            ->latest('submitted_at');
    }

    /** @param array{q?:string,from?:string,to?:string,status?:string} $filters */
    private function paymentQuery(array $filters): Builder
    {
        return Payment::query()
            ->when($filters['q'] ?? null, fn (Builder $query, string $search) => $query->where('gateway_ref', 'like', "%{$search}%"))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to))
            ->latest('created_at');
    }

    /** @param array{q?:string,from?:string,to?:string,status?:string} $filters */
    private function notificationQuery(array $filters): Builder
    {
        return Notification::query()
            ->when($filters['q'] ?? null, fn (Builder $query, string $search) => $query->where('subject', 'like', "%{$search}%"))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to))
            ->latest('created_at');
    }
}
