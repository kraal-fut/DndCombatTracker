<?php

declare(strict_types=1);

namespace App\Assessment\Q3;

use App\Assessment\Q3\DTO\Address;

/**
 * Stub implementation of ShippingRateServiceInterface for demonstration purposes.
 * Assumes a fixed $5.99 rate — the real integration would call an external API.
 */
final class StubShippingRateService implements ShippingRateServiceInterface
{
    private const float STUB_RATE = 5.99;

    public function getRateForAddress(Address $address): float
    {
        return self::STUB_RATE;
    }
}
