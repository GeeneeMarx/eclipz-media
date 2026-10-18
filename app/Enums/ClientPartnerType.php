<?php

namespace App\Enums;

enum ClientPartnerType: string
{
    case Client = 'client';
    case Partner = 'partner';

    public function label(): string
    {
        return match ($this) {
            self::Client => 'Client',
            self::Partner => 'Partner',
        };
    }
}
