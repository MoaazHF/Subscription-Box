<?php

namespace App\Notifications;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeliveryStatusChanged extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Delivery $delivery,
        public readonly string $previousStatus
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification (database channel).
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'delivery_id' => $this->delivery->id,
            'tracking_number' => $this->delivery->tracking_number,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->delivery->status,
            'message' => sprintf(
                'Your delivery status has been updated from "%s" to "%s".',
                ucfirst(str_replace('_', ' ', $this->previousStatus)),
                ucfirst(str_replace('_', ' ', $this->delivery->status))
            ),
        ];
    }
}
