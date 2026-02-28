<?php

declare(strict_types=1);

namespace App\Console\Commands\Assessment;

use App\Assessment\Q3\DTO\Address;
use App\Assessment\Q3\Cart;
use App\Assessment\Q3\DTO\CartItem;
use App\Assessment\Q3\Customer;
use App\Assessment\Q3\StubShippingRateService;
use Illuminate\Console\Command;

final class Q3CartDemoCommand extends Command
{
    protected $signature = 'assessment:q3';

    protected $description = 'Q3: Demo the Cart, Customer, Address and CartItem classes';

    public function handle(): int
    {
        $this->info('--- Assessment Q3: Cart / Customer / Address / CartItem ---');
        $this->newLine();

        $customer = $this->buildCustomer();
        $cart = $this->buildCart($customer);

        $this->printCustomerInfo($customer);
        $this->printCartItems($cart);
        $this->printTotals($cart);

        return self::SUCCESS;
    }

    private function buildCustomer(): Customer
    {
        $customer = new Customer(firstName: 'John', lastName: 'Doe');

        $customer->addAddress(new Address(
            line1: '123 Main St',
            line2: 'Apt 4B',
            city: 'Springfield',
            state: 'IL',
            zip: '62701',
        ));

        $customer->addAddress(new Address(
            line1: '456 Oak Ave',
            line2: null,
            city: 'Chicago',
            state: 'IL',
            zip: '60601',
        ));

        return $customer;
    }

    private function buildCart(Customer $customer): Cart
    {
        $cart = new Cart(customer: $customer);

        $cart->addItem(new CartItem(id: 1, name: 'Longsword', quantity: 1, price: 15.00));
        $cart->addItem(new CartItem(id: 2, name: 'Shield', quantity: 2, price: 9.99));
        $cart->addItem(new CartItem(id: 3, name: 'Torch', quantity: 5, price: 0.10));

        $cart->setShippingAddress(new Address(
            line1: '123 Main St',
            line2: 'Apt 4B',
            city: 'Springfield',
            state: 'IL',
            zip: '62701',
        ));

        return $cart;
    }

    private function printCustomerInfo(Customer $customer): void
    {
        $this->line('<fg=cyan>Customer Name:</> ' . $customer->getFullName());
        $this->line('<fg=cyan>Addresses:</>');

        foreach ($customer->getAddresses() as $i => $address) {
            $this->line("  [{$i}] {$address}");
        }

        $this->newLine();
    }

    private function printCartItems(Cart $cart): void
    {
        $this->line('<fg=cyan>Ships To:</> ' . ($cart->getShippingAddress() ?? '(not set)'));
        $this->newLine();

        $this->line('<fg=cyan>Items In Cart:</>');
        $this->table(
            ['ID', 'Name', 'Qty', 'Unit Price', 'Line Total'],
            $cart->getItems()->map(fn(CartItem $item) => [
                $item->id,
                $item->name,
                $item->quantity,
                '$' . number_format($item->price, 2),
                '$' . number_format($item->subtotal(), 2),
            ])->all(),
        );
    }

    private function printTotals(Cart $cart): void
    {
        $shipping = new StubShippingRateService();

        $this->line('<fg=cyan>Cost Breakdown:</>');
        $this->table(
            ['', 'Amount'],
            [
                ['Subtotal', '$' . number_format($cart->subtotal(), 2)],
                ['Tax (7%)', '$' . number_format($cart->tax(), 2)],
                ['Shipping', '$' . number_format($cart->shippingCost($shipping), 2)],
                ['Total', '$' . number_format($cart->total($shipping), 2)],
            ],
        );
    }
}
