<?php

namespace App\Enums;

enum AstrologerDocumentType: string
{
    case Aadhaar = 'aadhaar';
    case Pan = 'pan';
    case Certificate = 'certificate';

    public function label(): string
    {
        return match ($this) {
            self::Aadhaar => 'Aadhaar Card',
            self::Pan => 'PAN Card',
            self::Certificate => 'Qualification Certificate',
        };
    }

    public function requiresNumber(): bool
    {
        return $this === self::Aadhaar || $this === self::Pan;
    }
}
