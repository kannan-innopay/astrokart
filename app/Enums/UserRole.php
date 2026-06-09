<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Astrologer = 'astrologer';
    case Admin = 'admin';
    case SupportAgent = 'support_agent';
    case FinanceAdmin = 'finance_admin';
    case SuperAdmin = 'super_admin';

    public function isAdmin(): bool
    {
        return in_array($this, [self::Admin, self::SuperAdmin]);
    }
}
