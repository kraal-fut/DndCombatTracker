<?php

namespace App\DataTransferObjects;

readonly class AddCharacterData
{
    public function __construct(
        public string $name,
        public int $initiative,
        public ?int $maxHp = null,
        public ?int $currentHp = null,
        public bool $isPlayer = false,
        public ?int $userId = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            initiative: $data['initiative'],
            maxHp: $data['max_hp'] ?? null,
            currentHp: $data['current_hp'] ?? null,
            isPlayer: $data['is_player'] ?? false,
            userId: $data['user_id'] ?? null,
        );
    }
}
