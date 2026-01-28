<?php

namespace App\Messaging\Commands;

readonly class NextRound
{
    public function __construct(
        public int $combatId
    ) {
    }
}
