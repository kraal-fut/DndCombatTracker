<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Combat;
use App\Models\CombatCharacter;
use App\Models\CombatShare;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SharedCombatController extends Controller
{
    public function show(string $token): View|RedirectResponse
    {
        $share = CombatShare::where('share_token', $token)->first();

        if (!$share || !$share->isValid()) {
            abort(404, 'Combat share link is invalid or has expired.');
        }

        /** @var Combat|null $combat */
        $combat = $share->combat()
            ->with(['characters.user', 'characters.conditions', 'characters.stateEffects', 'characters.reactions'])
            ->first();

        if (!$combat) {
            abort(404, 'Combat not found.');
        }

        // If combat is already active and user is a participant, redirect to main combat page
        if ($combat->status === \App\Enums\CombatStatus::Active && auth()->check()) {
            $isParticipant = $combat->characters->where('user_id', auth()->id())->isNotEmpty();
            if ($isParticipant) {
                return redirect()->route('combats.show', $combat);
            }
        }

        $userCharacters = auth()->check()
            ? $combat->characters->where('user_id', auth()->id())
            : collect();

        if (request()->header('X-Partial-Board')) {
            return view('combats._shared_board', compact('combat', 'share', 'userCharacters'));
        }

        return view('combats.shared', compact('combat', 'share', 'userCharacters'));
    }

    public function addCharacter(string $token): View|RedirectResponse
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $share = CombatShare::where('share_token', $token)->first();

        if (!$share || !$share->isValid()) {
            abort(404, 'Combat share link is invalid or has expired.');
        }

        /** @var Combat $combat */
        $combat = $share->combat;

        return view('combats.add-character', compact('combat', 'share'));
    }

    public function storeCharacter(Request $request, string $token): RedirectResponse
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $share = CombatShare::where('share_token', $token)->first();

        if (!$share || !$share->isValid()) {
            abort(404, 'Combat share link is invalid or has expired.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initiative' => 'required|integer|min:1|max:30',
            'max_hp' => 'required|integer|min:1',
            'current_hp' => 'nullable|integer|min:0',
            'armor_class' => 'required|integer|min:1|max:30',
        ]);

        /** @var Combat $combat */
        $combat = $share->combat;

        $character = $combat->characters()->create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'initiative' => $validated['initiative'],
            'original_initiative' => $validated['initiative'],
            'max_hp' => $validated['max_hp'],
            'current_hp' => $validated['current_hp'] ?? $validated['max_hp'],
            'armor_class' => $validated['armor_class'],
            'order' => 0,
            'is_player' => true,
        ]);

        return redirect()->route('combats.shared', $token)
            ->with('success', "Character '{$character->name}' added to combat!");
    }

    public function updateCharacter(Request $request, string $token, CombatCharacter $character): RedirectResponse
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $share = CombatShare::where('share_token', $token)->first();

        if (!$share || !$share->isValid()) {
            abort(404, 'Combat share link is invalid or has expired.');
        }

        if ($character->user_id !== auth()->id()) {
            abort(403, 'You can only edit your own characters.');
        }

        $validated = $request->validate([
            'current_hp' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $character->update($validated);

        return redirect()->route('combats.shared', $token)
            ->with('success', 'Character updated successfully!');
    }
}
