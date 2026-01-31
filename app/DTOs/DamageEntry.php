<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class DamageEntry
{
    public function __construct(
        public int $amount,
        public string $type
    ) {
    }
}
