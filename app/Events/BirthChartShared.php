<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BirthChartShared implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ChatMessage $chatMessage,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('consultation.'.$this->chatMessage->consultation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chart.shared';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->chatMessage->id,
            'type' => 'chart_shared',
            'message' => $this->chatMessage->message,
            'sender_id' => $this->chatMessage->sender_id,
            'sender_name' => $this->chatMessage->sender->name,
            'metadata' => $this->chatMessage->metadata,
            'created_at' => $this->chatMessage->created_at->toIso8601String(),
        ];
    }
}
