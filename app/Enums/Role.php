<?php

namespace App\Enums;

/**
 * Known role values. The "role" column on users is a plain string (see migration),
 * NOT a database enum — adding a new role here does not require a migration.
 * This enum exists purely for type-safe checks in code (Role::Admin->value).
 */
enum Role: string
{
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
        };
    }
}
