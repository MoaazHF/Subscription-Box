<?php

namespace App\Services\NotificationChannels;

use App\Models\Notification;

interface NotificationChannel
{
    public function send(Notification $notification): void;

    public function name(): string;
}
