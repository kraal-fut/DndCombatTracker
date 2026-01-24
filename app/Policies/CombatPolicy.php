<?php

namespace App\Policies;

use App\Models\Combat;
use App\Models\User;

class CombatPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Combat $combat): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDM() && $combat->user_id === $user->id) {
            return true;
        }

        return $combat->characters()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->isDM() || $user->isAdmin();
    }

    public function update(User $user, Combat $combat): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDM() && $combat->user_id === $user->id;
    }

    public function delete(User $user, Combat $combat): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDM() && $combat->user_id === $user->id;
    }

    public function restore(User $user, Combat $combat): bool
    {
        return $this->delete($user, $combat);
    }

    public function forceDelete(User $user, Combat $combat): bool
    {
        return $user->isAdmin();
    }

    public function share(User $user, Combat $combat): bool
    {
        return $this->update($user, $combat);
    }

    public function viewShared(?User $user, Combat $combat): bool
    {
        return true;
    }
}
