<?php

namespace App\Enums;

enum AdvantageState: string
{
    case Normal = 'normal';
    case Advantage = 'advantage';
    case Disadvantage = 'disadvantage';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Normal',
            self::Advantage => 'Advantage',
            self::Disadvantage => 'Disadvantage',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Normal => 'gray',
            self::Advantage => 'green',
            self::Disadvantage => 'red',
        };
    }
}
