<?php

namespace App\Repositories;

use App\Models\Combat;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CombatRepository
{
    public function countAll(): int
    {
        return Combat::count();
    }

    public function countForDM(User $user): int
    {
        return Combat::where('user_id', $user->id)->count();
    }

    public function countForPlayer(User $user): int
    {
        return Combat::whereHas('characters', function (Builder $q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();
    }

    public function countActiveAll(): int
    {
        return Combat::where('status', 'active')->count();
    }

    public function countActiveForDM(User $user): int
    {
        return Combat::where('status', 'active')
            ->where('user_id', $user->id)
            ->count();
    }

    public function countActiveForPlayer(User $user): int
    {
        return Combat::where('status', 'active')
            ->whereHas('characters', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();
    }

    public function getRecentAll(int $limit = 5): Collection
    {
        return Combat::with('characters')->latest()->take($limit)->get();
    }

    public function getRecentForDM(User $user, int $limit = 5): Collection
    {
        return Combat::with('characters')
            ->where('user_id', $user->id)
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getRecentForPlayer(User $user, int $limit = 5): Collection
    {
        return Combat::with('characters')
            ->whereHas('characters', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->take($limit)
            ->get();
    }
}
