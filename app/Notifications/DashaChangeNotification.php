<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class DashaChangeNotification extends Notification
{
    public function __construct(
        private string $type,
        private string $lord,
        private string $startDate,
        private string $interpretation = '',
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $label = $this->type === 'mahadasha' ? 'Mahadasha' : 'Antardasha';

        return [
            'type' => $this->type,
            'lord' => $this->lord,
            'start_date' => $this->startDate,
            'interpretation' => $this->interpretation,
            'message' => "Your {$this->lord} {$label} begins on {$this->startDate}.",
        ];
    }
}
