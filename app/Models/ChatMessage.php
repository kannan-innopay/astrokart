<?php

namespace App\Models;

use App\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['consultation_id', 'sender_id', 'sender_type', 'type', 'message', 'attachment_url', 'metadata', 'read_at'])]
class ChatMessage extends Model
{
    use HasUuid;

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function isChartRequest(): bool
    {
        return $this->type === 'chart_request';
    }

    public function isChartShared(): bool
    {
        return $this->type === 'chart_shared';
    }

    public function isSystemMessage(): bool
    {
        return in_array($this->type, ['chart_request', 'chart_shared', 'system']);
    }
}
