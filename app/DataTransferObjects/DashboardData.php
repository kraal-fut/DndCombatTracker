<?php

namespace App\DataTransferObjects;

use Illuminate\Support\Collection;

readonly class DashboardData
{
    public function __construct(
        public int $totalCombats,
        public int $activeCombats,
        public int $totalCharacters,
        public Collection $recentCombats,
    ) {
    }
}
