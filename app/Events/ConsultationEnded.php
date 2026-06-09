<?php

namespace App\Events;

use App\Models\Consultation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsultationEnded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Consultation $consultation,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('consultation.'.$this->consultation->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'consultation.ended';
    }

    public function broadcastWith(): array
    {
        return [
            'consultation_id' => $this->consultation->id,
            'ended_by' => $this->consultation->ended_by,
            'end_reason' => $this->consultation->end_reason,
            'duration_seconds' => $this->consultation->duration_seconds,
            'gross_amount' => $this->consultation->gross_amount,
        ];
    }
}
