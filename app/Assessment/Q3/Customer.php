<?php

declare(strict_types=1);

namespace App\Assessment\Q3;

use App\Assessment\Q3\DTO\Address;

final class Customer
{
    /** @var Address[] */
    private array $addresses = [];

    public function __construct(
        private string $firstName,
        private string $lastName,
    ) {
    }

    public function addAddress(Address $address): void
    {
        $this->addresses[] = $address;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return Address[]
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }
}
