<?php

declare(strict_types=1);

namespace App\Console\Commands\Assessment;

use App\Assessment\Q2\NestedSortService;
use Illuminate\Console\Command;

final class Q2SortNestedDataCommand extends Command
{
    protected $signature = 'assessment:q2
        {--keys=last_name,account_id : Comma-separated list of keys to sort by}';

    protected $description = 'Q2: Sort the nested guest data structure by one or more keys at any depth';

    public function handle(NestedSortService $sorter): int
    {
        $keysOption = $this->option('keys');
        $keys = array_map('trim', explode(',', (string) $keysOption));

        $this->info('--- Assessment Q2: Sorting nested data by: ' . implode(', ', $keys) . ' ---');
        $this->newLine();

        $data = $this->guestData();
        $sorted = $sorter->sort($data, $keys);

        foreach ($sorted as $index => $guest) {
            $this->line("Guest #{$index}:");
            $this->line("  last_name:  {$guest['last_name']}");
            $this->line("  first_name: {$guest['first_name']}");

            foreach ($guest['guest_account'] as $account) {
                $this->line("  account_id: {$account['account_id']}");
            }
            $this->newLine();
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function guestData(): array
    {
        return [
            [
                'guest_id' => 177,
                'guest_type' => 'crew',
                'first_name' => 'Marco',
                'middle_name' => null,
                'last_name' => 'Burns',
                'gender' => 'M',
                'guest_booking' => [
                    [
                        'booking_number' => 20008683,
                        'ship_code' => 'OST',
                        'room_no' => 'A0073',
                        'start_time' => 1438214400,
                        'end_time' => 1483142400,
                        'is_checked_in' => true,
                    ],
                ],
                'guest_account' => [
                    [
                        'account_id' => 20009503,
                        'status_id' => 2,
                        'account_limit' => 0,
                        'allow_charges' => true,
                    ],
                ],
            ],
            [
                'guest_id' => 10000113,
                'guest_type' => 'crew',
                'first_name' => 'Bob Jr ',
                'middle_name' => 'Charles',
                'last_name' => 'Hemingway',
                'gender' => 'M',
                'guest_booking' => [
                    [
                        'booking_number' => 10000013,
                        'room_no' => 'B0092',
                        'is_checked_in' => true,
                    ],
                ],
                'guest_account' => [
                    [
                        'account_id' => 10000522,
                        'account_limit' => 300,
                        'allow_charges' => true,
                    ],
                ],
            ],
            [
                'guest_id' => 10000114,
                'guest_type' => 'crew',
                'first_name' => 'Al ',
                'middle_name' => 'Bert',
                'last_name' => 'Santiago',
                'gender' => 'M',
                'guest_booking' => [
                    [
                        'booking_number' => 10000014,
                        'room_no' => 'A0018',
                        'is_checked_in' => true,
                    ],
                ],
                'guest_account' => [
                    [
                        'account_id' => 10000013,
                        'account_limit' => 300,
                        'allow_charges' => false,
                    ],
                ],
            ],
            [
                'guest_id' => 10000115,
                'guest_type' => 'crew',
                'first_name' => 'Red ',
                'middle_name' => 'Ruby',
                'last_name' => 'Flowers ',
                'gender' => 'F',
                'guest_booking' => [
                    [
                        'booking_number' => 10000015,
                        'room_no' => 'A0051',
                        'is_checked_in' => true,
                    ],
                ],
                'guest_account' => [
                    [
                        'account_id' => 10000519,
                        'account_limit' => 300,
                        'allow_charges' => true,
                    ],
                ],
            ],
            [
                'guest_id' => 10000116,
                'guest_type' => 'crew',
                'first_name' => 'Ismael ',
                'middle_name' => 'Jean-Vital',
                'last_name' => 'Jammes',
                'gender' => 'M',
                'guest_booking' => [
                    [
                        'booking_number' => 10000016,
                        'room_no' => 'A0023',
                        'is_checked_in' => true,
                    ],
                ],
                'guest_account' => [
                    [
                        'account_id' => 10000015,
                        'account_limit' => 300,
                        'allow_charges' => true,
                    ],
                ],
            ],
        ];
    }
}
