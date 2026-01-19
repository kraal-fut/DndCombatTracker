<?php

namespace App\Enums;

enum ConditionType: string
{
    case Blinded = 'blinded';
    case Charmed = 'charmed';
    case Deafened = 'deafened';
    case Frightened = 'frightened';
    case Grappled = 'grappled';
    case Incapacitated = 'incapacitated';
    case Invisible = 'invisible';
    case Paralyzed = 'paralyzed';
    case Petrified = 'petrified';
    case Poisoned = 'poisoned';
    case Prone = 'prone';
    case Restrained = 'restrained';
    case Stunned = 'stunned';
    case Unconscious = 'unconscious';
    case Exhaustion = 'exhaustion';
    case Concentration = 'concentration';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Blinded => 'Blinded',
            self::Charmed => 'Charmed',
            self::Deafened => 'Deafened',
            self::Frightened => 'Frightened',
            self::Grappled => 'Grappled',
            self::Incapacitated => 'Incapacitated',
            self::Invisible => 'Invisible',
            self::Paralyzed => 'Paralyzed',
            self::Petrified => 'Petrified',
            self::Poisoned => 'Poisoned',
            self::Prone => 'Prone',
            self::Restrained => 'Restrained',
            self::Stunned => 'Stunned',
            self::Unconscious => 'Unconscious',
            self::Exhaustion => 'Exhaustion',
            self::Concentration => 'Concentration',
            self::Custom => 'Custom',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Blinded, self::Deafened => 'gray',
            self::Charmed, self::Frightened => 'yellow',
            self::Grappled, self::Restrained, self::Prone => 'orange',
            self::Incapacitated, self::Stunned, self::Paralyzed, self::Petrified, self::Unconscious => 'red',
            self::Poisoned => 'green',
            self::Invisible => 'blue',
            self::Exhaustion => 'purple',
            self::Concentration => 'indigo',
            self::Custom => 'slate',
        };
    }
}
