<?php

namespace App\Events;

readonly class CombatStateChanged
{
    public function __construct(
        public int $combatId
    ) {
    }
}
