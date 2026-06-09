<?php

namespace App\Enums;

enum ConsultationStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Rejected = 'rejected';

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function isEnded(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled, self::Rejected]);
    }
}
