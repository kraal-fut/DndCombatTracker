<?php

declare(strict_types=1);

namespace Tests\Unit\Assessment;

use App\Assessment\Q2\NestedSortService;

covers(NestedSortService::class);

$sorter = new NestedSortService();

it('sorts a flat list by a single string key', function () use ($sorter): void {
    $data = [
        ['last_name' => 'Santiago'],
        ['last_name' => 'Burns'],
        ['last_name' => 'Hemingway'],
    ];

    $sorted = $sorter->sort($data, ['last_name']);

    expect(array_column($sorted, 'last_name'))->toBe(['Burns', 'Hemingway', 'Santiago']);
});

it('sorts case-insensitively', function () use ($sorter): void {
    $data = [
        ['last_name' => 'zebra'],
        ['last_name' => 'Apple'],
        ['last_name' => 'mango'],
    ];

    $sorted = $sorter->sort($data, ['last_name']);

    expect(array_column($sorted, 'last_name'))->toBe(['Apple', 'mango', 'zebra']);
});

it('sorts by multiple keys, using the second key as tiebreaker', function () use ($sorter): void {
    $data = [
        ['last_name' => 'Smith', 'first_name' => 'Zara'],
        ['last_name' => 'Smith', 'first_name' => 'Alice'],
        ['last_name' => 'Adams', 'first_name' => 'Bob'],
    ];

    $sorted = $sorter->sort($data, ['last_name', 'first_name']);

    expect($sorted[0]['last_name'])->toBe('Adams');
    expect($sorted[1]['first_name'])->toBe('Alice');
    expect($sorted[2]['first_name'])->toBe('Zara');
});

it('sorts by integer key', function () use ($sorter): void {
    $data = [
        ['account_id' => 10000522],
        ['account_id' => 10000013],
        ['account_id' => 20009503],
    ];

    $sorted = $sorter->sort($data, ['account_id']);

    expect(array_column($sorted, 'account_id'))->toBe([10000013, 10000522, 20009503]);
});

it('recursively sorts nested arrays by the given key', function () use ($sorter): void {
    $data = [
        [
            'last_name' => 'Burns',
            'guest_account' => [
                ['account_id' => 20009503],
            ],
        ],
        [
            'last_name' => 'Adams',
            'guest_account' => [
                ['account_id' => 10000013],
            ],
        ],
    ];

    $sorted = $sorter->sort($data, ['last_name', 'account_id']);

    expect($sorted[0]['last_name'])->toBe('Adams');
    expect($sorted[1]['last_name'])->toBe('Burns');
});

it('leaves an empty array unchanged', function () use ($sorter): void {
    expect($sorter->sort([], ['last_name']))->toBe([]);
});

it('leaves items with no matching key in their original relative order', function () use ($sorter): void {
    $data = [
        ['foo' => 'z'],
        ['foo' => 'a'],
    ];

    $sorted = $sorter->sort($data, ['last_name']);

    expect($sorted)->toBe($data);
});

it('sorts the full guest dataset by last_name then account_id', function () use ($sorter): void {
    $data = [
        ['last_name' => 'Santiago', 'guest_account' => [['account_id' => 10000013]]],
        ['last_name' => 'Burns', 'guest_account' => [['account_id' => 20009503]]],
        ['last_name' => 'Jammes', 'guest_account' => [['account_id' => 10000015]]],
        ['last_name' => 'Flowers ', 'guest_account' => [['account_id' => 10000519]]],
        ['last_name' => 'Hemingway', 'guest_account' => [['account_id' => 10000522]]],
    ];

    $sorted = $sorter->sort($data, ['last_name', 'account_id']);

    expect(array_column($sorted, 'last_name'))->toBe([
        'Burns',
        'Flowers ',
        'Hemingway',
        'Jammes',
        'Santiago',
    ]);
});
