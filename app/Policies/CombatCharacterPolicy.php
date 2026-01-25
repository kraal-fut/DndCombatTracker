<?php

namespace App\Policies;

use App\Models\CombatCharacter;
use App\Models\User;

class CombatCharacterPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CombatCharacter $combatCharacter): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDM() && $combatCharacter->combat->user_id === $user->id) {
            return true;
        }

        return $combatCharacter->user_id === $user->id;
    }

    public function viewStats(User $user, CombatCharacter $combatCharacter): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDM() && $combatCharacter->combat->user_id === $user->id) {
            return true;
        }

        return $combatCharacter->user_id === $user->id || $combatCharacter->is_player;
    }

    public function create(User $user): bool
    {
        return $user->isDM() || $user->isAdmin();
    }

    public function update(User $user, CombatCharacter $combatCharacter): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDM() && $combatCharacter->combat->user_id === $user->id;
    }

    public function updateHp(User $user, CombatCharacter $combatCharacter): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDM() && $combatCharacter->combat->user_id === $user->id) {
            return true;
        }

        // Players can update HP on their own characters
        return $combatCharacter->user_id === $user->id;
    }

    public function delete(User $user, CombatCharacter $combatCharacter): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDM() && $combatCharacter->combat->user_id === $user->id;
    }

    public function restore(User $user, CombatCharacter $combatCharacter): bool
    {
        return $this->delete($user, $combatCharacter);
    }

    public function forceDelete(User $user, CombatCharacter $combatCharacter): bool
    {
        return $user->isAdmin();
    }
}
