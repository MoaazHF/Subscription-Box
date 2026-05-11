<?php

namespace App\Services;

use App\Models\Delivery;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class DeliveryStateTransitionService
{
    /** @var array<string, array<int, string>> */
    private const ALLOWED = [
        Delivery::PENDING => [Delivery::PICKING, Delivery::PACKED, Delivery::SHIPPED, Delivery::OUT_FOR_DELIVERY, Delivery::UNDELIVERABLE],
        Delivery::PICKING => [Delivery::PACKED, Delivery::SHIPPED, Delivery::OUT_FOR_DELIVERY, Delivery::UNDELIVERABLE],
        Delivery::PACKED => [Delivery::SHIPPED, Delivery::OUT_FOR_DELIVERY, Delivery::UNDELIVERABLE],
        Delivery::SHIPPED => [Delivery::OUT_FOR_DELIVERY, Delivery::UNDELIVERABLE],
        Delivery::OUT_FOR_DELIVERY => [Delivery::DELIVERED, Delivery::UNDELIVERABLE],
        Delivery::DELIVERED => [],
        Delivery::UNDELIVERABLE => [],
    ];

    /** @var array<int, string> */
    private const DRIVER_ALLOWED_TARGETS = [
        Delivery::PICKING,
        Delivery::PACKED,
        Delivery::SHIPPED,
        Delivery::OUT_FOR_DELIVERY,
        Delivery::DELIVERED,
        Delivery::UNDELIVERABLE,
    ];

    public function statusFromDriverProgressStep(int $progressStep): string
    {
        $status = Delivery::statusFromDriverProgressStep($progressStep);

        if (! $status) {
            throw ValidationException::withMessages([
                'progress_step' => 'Unsupported driver progress step provided.',
            ]);
        }

        return $status;
    }

    public function assertCanTransition(Delivery $delivery, string $newStatus, bool $isDriverContext = false): void
    {
        if (! in_array($newStatus, Delivery::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'Unsupported delivery status provided.',
            ]);
        }

        if ($isDriverContext && ! in_array($newStatus, self::DRIVER_ALLOWED_TARGETS, true)) {
            throw ValidationException::withMessages([
                'status' => 'Driver cannot set this delivery status.',
            ]);
        }

        if ($delivery->status === $newStatus) {
            return;
        }

        $allowedTargets = Arr::get(self::ALLOWED, $delivery->status, []);

        if (! in_array($newStatus, $allowedTargets, true)) {
            throw ValidationException::withMessages([
                'status' => 'Invalid transition from '.$delivery->status.' to '.$newStatus.'.',
            ]);
        }
    }

    /** @param array<string, mixed> $attributes */
    public function apply(Delivery $delivery, string $newStatus, array $attributes = [], bool $isDriverContext = false): Delivery
    {
        $this->assertCanTransition($delivery, $newStatus, $isDriverContext);

        $delivery->update([
            'status' => $newStatus,
            'tracking_number' => $attributes['tracking_number'] ?? $delivery->tracking_number,
            'estimated_delivery' => $attributes['estimated_delivery'] ?? $delivery->estimated_delivery,
            'delivery_instructions' => $attributes['delivery_instructions'] ?? $delivery->delivery_instructions,
            'eco_dispatch' => (bool) ($attributes['eco_dispatch'] ?? $delivery->eco_dispatch),
            'stops_remaining' => Delivery::STOPS_BY_STATUS[$newStatus],
            'actual_delivery' => $newStatus === Delivery::DELIVERED ? now() : null,
        ]);

        return $delivery->fresh();
    }
}
