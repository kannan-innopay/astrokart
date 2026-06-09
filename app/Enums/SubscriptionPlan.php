<?php

namespace App\Enums;

enum SubscriptionPlan: string
{
    case Daily = 'daily';
    case Monthly = 'monthly';
    case Yearly = 'yearly';

    public function price(): int
    {
        return match ($this) {
            self::Daily => 300,       // ₹3
            self::Monthly => 9900,    // ₹99
            self::Yearly => 79900,    // ₹799
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'Daily Premium Pass',
            self::Monthly => 'Monthly Premium',
            self::Yearly => 'Yearly Premium',
        };
    }

    public function durationDays(): int
    {
        return match ($this) {
            self::Daily => 1,
            self::Monthly => 30,
            self::Yearly => 365,
        };
    }

    public function priceFormatted(): string
    {
        return '₹' . number_format($this->price() / 100);
    }
}
