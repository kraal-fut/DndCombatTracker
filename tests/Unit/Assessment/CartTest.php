<?php

declare(strict_types=1);

namespace Tests\Unit\Assessment;

use App\Assessment\Q3\Cart;
use App\Assessment\Q3\Customer;
use App\Assessment\Q3\DTO\Address;
use App\Assessment\Q3\DTO\CartItem;
use App\Assessment\Q3\ShippingRateServiceInterface;
use App\Assessment\Q3\StubShippingRateService;
use Mockery;
use Mockery\MockInterface;

covers(Cart::class, Customer::class, Address::class, CartItem::class, StubShippingRateService::class);

// ---------------------------------------------------------------------------
// Address
// ---------------------------------------------------------------------------

it('formats an address with all fields', function (): void {
    $address = new Address(
        line1: '123 Main St',
        line2: 'Apt 4B',
        city: 'Springfield',
        state: 'IL',
        zip: '62701',
    );

    expect((string) $address)->toBe('123 Main St, Apt 4B, Springfield, IL, 62701');
});

it('omits null line2 from address string', function (): void {
    $address = new Address(
        line1: '456 Oak Ave',
        line2: null,
        city: 'Chicago',
        state: 'IL',
        zip: '60601',
    );

    expect((string) $address)->toBe('456 Oak Ave, Chicago, IL, 60601');
});

// ---------------------------------------------------------------------------
// CartItem
// ---------------------------------------------------------------------------

it('calculates the correct subtotal', function (): void {
    $item = new CartItem(id: 1, name: 'Longsword', quantity: 3, price: 15.00);

    expect($item->subtotal())->toBe(45.00);
});

it('calculates subtotal of zero for zero quantity', function (): void {
    $item = new CartItem(id: 2, name: 'Shield', quantity: 0, price: 9.99);

    expect($item->subtotal())->toBe(0.0);
});

// ---------------------------------------------------------------------------
// Customer
// ---------------------------------------------------------------------------

it('returns the correct full name', function (): void {
    $customer = new Customer(firstName: 'John', lastName: 'Doe');

    expect($customer->getFullName())->toBe('John Doe');
    expect($customer->getFirstName())->toBe('John');
    expect($customer->getLastName())->toBe('Doe');
});

it('holds multiple addresses', function (): void {
    $customer = new Customer(firstName: 'Jane', lastName: 'Smith');
    $addr1 = new Address(line1: '1 A St', line2: null, city: 'City', state: 'ST', zip: '00001');
    $addr2 = new Address(line1: '2 B Ave', line2: null, city: 'Town', state: 'TX', zip: '00002');

    $customer->addAddress($addr1);
    $customer->addAddress($addr2);

    expect($customer->getAddresses())->toHaveCount(2);
    expect($customer->getAddresses()[0])->toBe($addr1);
    expect($customer->getAddresses()[1])->toBe($addr2);
});

it('starts with an empty address collection', function (): void {
    $customer = new Customer(firstName: 'Empty', lastName: 'User');

    expect($customer->getAddresses())->toBeEmpty();
});

// ---------------------------------------------------------------------------
// Cart
// ---------------------------------------------------------------------------

function makeCart(): Cart
{
    return new Cart(customer: new Customer(firstName: 'John', lastName: 'Doe'));
}

function makeItem(int $qty, float $price): CartItem
{
    static $id = 0;
    return new CartItem(id: ++$id, name: 'Item', quantity: $qty, price: $price);
}

it('holds the customer', function (): void {
    $customer = new Customer(firstName: 'John', lastName: 'Doe');
    $cart = new Cart(customer: $customer);

    expect($cart->getCustomer())->toBe($customer);
});

it('starts with an empty item collection', function (): void {
    expect(makeCart()->getItems())->toBeEmpty();
});

it('adds items and returns them', function (): void {
    $cart = makeCart();
    $item = makeItem(qty: 2, price: 10.00);

    $cart->addItem($item);

    expect($cart->getItems())->toHaveCount(1);
    expect($cart->getItems()->first())->toBe($item);
});

it('calculates the correct subtotal for multiple items', function (): void {
    $cart = makeCart();
    $cart->addItem(makeItem(qty: 1, price: 15.00));
    $cart->addItem(makeItem(qty: 2, price: 9.99));
    $cart->addItem(makeItem(qty: 5, price: 0.10));

    expect($cart->subtotal())->toEqualWithDelta(35.48, 0.001);
});

it('calculates 7% tax on the subtotal', function (): void {
    $cart = makeCart();
    $cart->addItem(makeItem(qty: 1, price: 100.00));

    expect($cart->tax())->toBe(7.00);
});

it('returns zero shipping cost when no shipping address is set', function (): void {
    $cart = makeCart();
    $service = Mockery::mock(ShippingRateServiceInterface::class);

    expect($cart->shippingCost($service))->toBe(0.0);
});

it('delegates shipping cost to the ShippingRateService', function (): void {
    $cart = makeCart();
    $address = new Address(line1: '1 St', line2: null, city: 'City', state: 'ST', zip: '00001');
    $cart->setShippingAddress($address);

    /** @var ShippingRateServiceInterface&MockInterface $service */
    $service = Mockery::mock(ShippingRateServiceInterface::class);
    $service->shouldReceive('getRateForAddress')->once()->with($address)->andReturn(12.50);

    expect($cart->shippingCost($service))->toBe(12.50);
});

it('computes the correct total (subtotal + tax + shipping)', function (): void {
    $cart = makeCart();
    $address = new Address(line1: '1 St', line2: null, city: 'City', state: 'ST', zip: '00001');
    $cart->setShippingAddress($address);
    $cart->addItem(makeItem(qty: 1, price: 100.00));

    /** @var ShippingRateServiceInterface&MockInterface $service */
    $service = Mockery::mock(ShippingRateServiceInterface::class);
    $service->shouldReceive('getRateForAddress')->andReturn(5.00);

    // subtotal 100.00 + tax 7.00 + shipping 5.00 = 112.00
    expect($cart->total($service))->toBe(112.00);
});

it('sets and returns the shipping address', function (): void {
    $cart = makeCart();
    $address = new Address(line1: '99 Test Rd', line2: null, city: 'Nowhere', state: 'NA', zip: '99999');

    $cart->setShippingAddress($address);

    expect($cart->getShippingAddress())->toBe($address);
});

it('returns null shipping address when not set', function (): void {
    expect(makeCart()->getShippingAddress())->toBeNull();
});

// ---------------------------------------------------------------------------
// StubShippingRateService
// ---------------------------------------------------------------------------

it('always returns the fixed stub rate', function (): void {
    $service = new StubShippingRateService();
    $address = new Address(line1: '1 St', line2: null, city: 'City', state: 'ST', zip: '00001');

    expect($service->getRateForAddress($address))->toBe(5.99);
});
