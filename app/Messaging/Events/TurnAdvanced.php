<?php

namespace App\Messaging\Events;

readonly class TurnAdvanced
{
    public function __construct(
        public int $combatId,
        public int $newTurnIndex
    ) {
    }
}
