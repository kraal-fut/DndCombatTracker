<?php

declare(strict_types=1);

namespace App\Assessment\Q3\DTO;

final readonly class CartItem
{
    public function __construct(
        public int $id,
        public string $name,
        public int $quantity,
        public float $price,
    ) {
    }

    public function subtotal(): float
    {
        return $this->price * $this->quantity;
    }
}
