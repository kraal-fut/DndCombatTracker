<?php

namespace App\Messaging\Commands;

readonly class NextTurn
{
    public function __construct(
        public int $combatId
    ) {
    }
}
