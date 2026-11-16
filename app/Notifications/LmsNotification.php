<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LmsNotification extends Notification
{
    use Queueable;

    /**
     * @param  array{
     *     title: string,
     *     message: string,
     *     url?: string|null,
     *     icon?: string|null,
     *     category?: string|null,
     *     badge_variant?: string|null,
     *     action_label?: string|null,
     *     metadata?: array<string, mixed>
     * }  $payload
     */
    public function __construct(
        public array $payload = []
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return array_merge([
            'title' => 'System Notification',
            'message' => '',
            'url' => null,
            'icon' => 'bell',
            'category' => 'general',
            'badge_variant' => 'info',
            'action_label' => 'View Details',
        ], $this->payload);
    }
}
