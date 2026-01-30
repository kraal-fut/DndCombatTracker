<?php

namespace App\Repositories;

use App\Models\CombatCharacter;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CombatCharacterRepository
{
    public function countAll(): int
    {
        return CombatCharacter::count();
    }

    public function countForDM(User $user): int
    {
        return CombatCharacter::whereHas('combat', function (Builder $q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();
    }

    public function countForPlayer(User $user): int
    {
        return CombatCharacter::where('user_id', $user->id)->count();
    }
}
