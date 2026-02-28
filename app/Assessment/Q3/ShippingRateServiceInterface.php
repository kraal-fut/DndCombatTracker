<?php

declare(strict_types=1);

namespace App\Assessment\Q3;

use App\Assessment\Q3\DTO\Address;

/**
 * Stub for an external shipping rate API integration.
 * The actual implementation is assumed to exist elsewhere in the system.
 */
interface ShippingRateServiceInterface
{
    public function getRateForAddress(Address $address): float;
}
