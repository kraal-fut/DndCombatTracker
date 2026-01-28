<?php

namespace App\Messaging\Events;

readonly class RoundAdvanced
{
    public function __construct(
        public int $combatId,
        public int $newRound
    ) {
    }
}
