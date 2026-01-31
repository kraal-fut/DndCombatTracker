<?php

namespace App\Enums;

enum HPUpdateType: string
{
    case Damage = 'damage';
    case Heal = 'heal';
    case Temporary = 'temporary';
}
