<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\HPUpdateType;

readonly class HPUpdatePayload
{
    /**
     * @param DamageEntry[] $damages
     */
    public function __construct(
        public HPUpdateType $type,
        public int $changeAmount = 0,
        public array $damages = [],
        public bool $ignoreResist = false
    ) {
    }
}
