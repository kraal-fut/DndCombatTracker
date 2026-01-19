<?php

namespace App\Enums;

enum StateModifierType: string
{
    case Penalty = 'penalty';
    case Bonus = 'bonus';

    public function label(): string
    {
        return match ($this) {
            self::Penalty => 'Penalty',
            self::Bonus => 'Bonus',
        };
    }
}
