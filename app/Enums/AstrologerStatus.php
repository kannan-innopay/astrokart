<?php

namespace App\Enums;

enum AstrologerStatus: string
{
    case Applied = 'applied';
    case PendingVerification = 'pending_verification';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Suspended = 'suspended';
    case Inactive = 'inactive';

    public function canGoOnline(): bool
    {
        return $this === self::Approved;
    }
}
