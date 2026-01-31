<?php

declare(strict_types=1);

namespace App\Messaging\Commands;

use App\DTOs\HPUpdatePayload;

readonly class UpdateCharacterHP
{
    public function __construct(
        public int $combatId,
        public int $characterId,
        public HPUpdatePayload $payload
    ) {
    }
}
