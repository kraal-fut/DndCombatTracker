<?php

declare(strict_types=1);

namespace App\Assessment\Q3;

use App\Assessment\Q3\DTO\Address;
use App\Assessment\Q3\DTO\CartItem;
use Illuminate\Support\Collection;

final class Cart
{
    private const float TAX_RATE = 0.07;

    /** @var Collection<int, CartItem> */
    private Collection $items;

    private null|Address $shippingAddress = null;

    public function __construct(
        private Customer $customer,
    ) {
        $this->items = collect();
    }

    public function addItem(CartItem $item): void
    {
        $this->items->push($item);
    }

    public function setShippingAddress(Address $address): void
    {
        $this->shippingAddress = $address;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getShippingAddress(): null|Address
    {
        return $this->shippingAddress;
    }

    public function subtotal(): float
    {
        return $this->items->sum(fn(CartItem $item) => $item->subtotal());
    }

    public function tax(): float
    {
        return round($this->subtotal() * self::TAX_RATE, 2);
    }

    public function shippingCost(ShippingRateServiceInterface $shippingRateService): float
    {
        if ($this->shippingAddress === null) {
            return 0.0;
        }

        return $shippingRateService->getRateForAddress($this->shippingAddress);
    }

    public function total(ShippingRateServiceInterface $shippingRateService): float
    {
        return round(
            $this->subtotal() + $this->tax() + $this->shippingCost($shippingRateService),
            2,
        );
    }
}
