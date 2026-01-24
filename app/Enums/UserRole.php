<?php

namespace App\Enums;

enum UserRole: string
{
    case Player = 'player';
    case DM = 'dm';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Player => 'Player',
            self::DM => 'Dungeon Master',
            self::Admin => 'Administrator',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function isDM(): bool
    {
        return $this === self::DM;
    }

    public function isPlayer(): bool
    {
        return $this === self::Player;
    }
}
