<?php

declare(strict_types=1);

namespace App\Assessment\Q3\DTO;

final readonly class Address
{
    public function __construct(
        public string $line1,
        public null|string $line2,
        public string $city,
        public string $state,
        public string $zip,
    ) {
    }

    public function __toString(): string
    {
        $parts = array_filter([$this->line1, $this->line2, $this->city, $this->state, $this->zip]);

        return implode(', ', $parts);
    }
}
