<?php

namespace App\Enums;

enum CombatStatus: string
{
    case Preparation = 'preparation';
    case Active = 'active';
    case Paused = 'paused';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Preparation => 'Preparation',
            self::Active => 'Active',
            self::Paused => 'Paused',
            self::Completed => 'Completed',
        };
    }
}
