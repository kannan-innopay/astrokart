<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
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
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->chatMessage->id,
            'message' => $this->chatMessage->message,
            'sender_id' => $this->chatMessage->sender_id,
            'sender_type' => $this->chatMessage->sender_type,
            'sender_name' => $this->chatMessage->sender->name,
            'created_at' => $this->chatMessage->created_at->toIso8601String(),
        ];
    }
}
