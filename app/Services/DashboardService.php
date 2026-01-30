<?php

namespace App\Services;

use App\DataTransferObjects\DashboardData;
use App\Models\User;
use App\Repositories\CombatCharacterRepository;
use App\Repositories\CombatRepository;

class DashboardService
{
    public function __construct(
        private readonly CombatRepository $combatRepository,
        private readonly CombatCharacterRepository $characterRepository
    ) {
    }

    public function getStatistics(User $user): DashboardData
    {
        return new DashboardData(
            totalCombats: $this->getTotalCombats($user),
            activeCombats: $this->getActiveCombats($user),
            totalCharacters: $this->getTotalCharacters($user),
            recentCombats: $this->getRecentCombats($user)
        );
    }

    private function getTotalCombats(User $user): int
    {
        if ($user->isAdmin()) {
            return $this->combatRepository->countAll();
        }

        if ($user->isDM()) {
            return $this->combatRepository->countForDM($user);
        }

        return $this->combatRepository->countForPlayer($user);
    }

    private function getActiveCombats(User $user): int
    {
        if ($user->isAdmin()) {
            return $this->combatRepository->countActiveAll();
        }

        if ($user->isDM()) {
            return $this->combatRepository->countActiveForDM($user);
        }

        return $this->combatRepository->countActiveForPlayer($user);
    }

    private function getTotalCharacters(User $user): int
    {
        if ($user->isAdmin()) {
            return $this->characterRepository->countAll();
        }

        if ($user->isDM()) {
            return $this->characterRepository->countForDM($user);
        }

        return $this->characterRepository->countForPlayer($user);
    }

    private function getRecentCombats(User $user)
    {
        if ($user->isAdmin()) {
            return $this->combatRepository->getRecentAll();
        }

        if ($user->isDM()) {
            return $this->combatRepository->getRecentForDM($user);
        }

        return $this->combatRepository->getRecentForPlayer($user);
    }
}
