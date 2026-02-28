<?php

declare(strict_types=1);

namespace Tests\Unit\Assessment;

use App\Assessment\Q1\GuestDataPrinterService;

covers(GuestDataPrinterService::class);

$printer = new GuestDataPrinterService();

it('prints a flat string value', function () use ($printer): void {
    $output = captureOutput(fn() => $printer->print(['first_name' => 'Marco']));

    expect($output)->toContain('First Name: Marco');
});

it('prints null values as (null)', function () use ($printer): void {
    $output = captureOutput(fn() => $printer->print(['middle_name' => null]));

    expect($output)->toContain('Middle Name: (null)');
});

it('prints boolean true as "true"', function () use ($printer): void {
    $output = captureOutput(fn() => $printer->print(['is_checked_in' => true]));

    expect($output)->toContain('Is Checked In: true');
});

it('prints boolean false as "false"', function () use ($printer): void {
    $output = captureOutput(fn() => $printer->print(['allow_charges' => false]));

    expect($output)->toContain('Allow Charges: false');
});

it('prints integer values', function () use ($printer): void {
    $output = captureOutput(fn() => $printer->print(['guest_id' => 177]));

    expect($output)->toContain('Guest Id: 177');
});

it('recurses into nested arrays and prints their keys', function () use ($printer): void {
    $output = captureOutput(fn() => $printer->print([
        'guest_booking' => [
            ['booking_number' => 20008683, 'room_no' => 'A0073'],
        ],
    ]));

    expect($output)
        ->toContain('Guest Booking:')
        ->toContain('Booking Number: 20008683')
        ->toContain('Room No: A0073');
});

it('handles deeply nested structures', function () use ($printer): void {
    $output = captureOutput(fn() => $printer->print([
        'level_one' => [
            'level_two' => [
                'value' => 'deep',
            ],
        ],
    ]));

    expect($output)
        ->toContain('Level One:')
        ->toContain('Level Two:')
        ->toContain('Value: deep');
});

it('prints all guests in the full data structure', function () use ($printer): void {
    $data = [
        ['first_name' => 'Marco', 'last_name' => 'Burns'],
        ['first_name' => 'Bob Jr ', 'last_name' => 'Hemingway'],
    ];

    $output = captureOutput(fn() => $printer->print($data));

    expect($output)
        ->toContain('Marco')
        ->toContain('Burns')
        ->toContain('Bob Jr')
        ->toContain('Hemingway');
});

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function captureOutput(callable $fn): string
{
    ob_start();
    $fn();
    return (string) ob_get_clean();
}
