<?php

namespace App\Http\Controllers;

use App\Models\Combat;
use App\Models\CombatCharacter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = Auth::user();

        // Total Combats
        if ($user->isAdmin()) {
            $totalCombats = Combat::count();
        } elseif ($user->isDM()) {
            $totalCombats = Combat::where('user_id', $user->id)->count();
        } else {
            $totalCombats = Combat::whereHas('characters', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();
        }

        // Active Combats
        $activeQuery = Combat::where('status', 'active');
        if ($user->isDM()) {
            $activeCombats = $activeQuery->where('user_id', $user->id)->count();
        } elseif ($user->isPlayer()) {
            $activeCombats = $activeQuery->whereHas('characters', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();
        } else {
            $activeCombats = $activeQuery->count();
        }

        // Total Characters
        if ($user->isAdmin()) {
            $totalCharacters = CombatCharacter::count();
        } elseif ($user->isDM()) {
            $totalCharacters = CombatCharacter::whereHas('combat', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();
        } else {
            $totalCharacters = CombatCharacter::where('user_id', $user->id)->count();
        }

        // Recent Combats
        $recentQuery = Combat::with('characters');
        if ($user->isAdmin()) {
            $recentCombats = $recentQuery->latest()->take(5)->get();
        } elseif ($user->isDM()) {
            $recentCombats = $recentQuery->where('user_id', $user->id)->latest()->take(5)->get();
        } else {
            $recentCombats = $recentQuery->whereHas('characters', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->latest()->take(5)->get();
        }

        return view('dashboard', compact(
            'totalCombats',
            'activeCombats',
            'totalCharacters',
            'recentCombats'
        ));
    }
}
