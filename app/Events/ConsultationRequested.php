<?php

namespace App\Events;

use App\Models\Consultation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsultationRequested implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Consultation $consultation,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('astrologer.'.$this->consultation->astrologer->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'consultation.requested';
    }

    public function broadcastWith(): array
    {
        return [
            'consultation_id' => $this->consultation->id,
            'user_name' => $this->consultation->user->name,
            'user_mobile' => $this->consultation->user->mobile,
            'price_per_minute' => $this->consultation->price_per_minute,
        ];
    }
}
