<?php

namespace App\Messaging\Events;

readonly class CharacterHPUpdated
{
    public function __construct(
        public int $combatId,
        public int $characterId,
        public int $newHp
    ) {
    }
}
