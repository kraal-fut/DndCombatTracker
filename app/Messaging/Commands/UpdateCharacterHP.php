<?php

namespace App\Messaging\Commands;

use App\Enums\HPUpdateType;

readonly class UpdateCharacterHP
{
    public function __construct(
        public int $combatId,
        public int $characterId,
        public int $changeAmount,
        public HPUpdateType $type
    ) {
    }
}
