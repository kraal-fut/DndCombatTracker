<?php

namespace App\Enums;

enum DamageType: string
{
    case Acid = 'acid';
    case Bludgeoning = 'bludgeoning';
    case Cold = 'cold';
    case Fire = 'fire';
    case Force = 'force';
    case Lightning = 'lightning';
    case Necrotic = 'necrotic';
    case Piercing = 'piercing';
    case Poison = 'poison';
    case Psychic = 'psychic';
    case Radiant = 'radiant';
    case Slashing = 'slashing';
    case Thunder = 'thunder';

    public function label(): string
    {
        return match ($this) {
            self::Acid => 'Acid',
            self::Bludgeoning => 'Bludgeoning',
            self::Cold => 'Cold',
            self::Fire => 'Fire',
            self::Force => 'Force',
            self::Lightning => 'Lightning',
            self::Necrotic => 'Necrotic',
            self::Piercing => 'Piercing',
            self::Poison => 'Poison',
            self::Psychic => 'Psychic',
            self::Radiant => 'Radiant',
            self::Slashing => 'Slashing',
            self::Thunder => 'Thunder',
        };
    }
}
