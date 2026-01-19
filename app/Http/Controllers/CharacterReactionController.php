<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReactionRequest;
use App\Models\CombatCharacter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CharacterReactionController extends Controller
{
    public function create(CombatCharacter $character): View
    {
        return view('character-reactions.create', compact('character'));
    }

    public function store(StoreReactionRequest $request, CombatCharacter $character): RedirectResponse
    {
        $character->reactions()->create($request->validated());

        return redirect()->route('combats.show', $character->combat)
            ->with('success', 'Reaction added to character!');
    }

    public function use(CombatCharacter $character, int $reaction): RedirectResponse
    {
        if ($character->hasUsedReaction()) {
            return back()->with('error', 'This character has already used their reaction this round!');
        }

        $reactionModel = $character->reactions()->findOrFail($reaction);
        $reactionModel->update(['is_used' => true]);

        return back()->with('success', 'Reaction marked as used!');
    }

    public function reset(CombatCharacter $character, int $reaction): RedirectResponse
    {
        $reactionModel = $character->reactions()->findOrFail($reaction);
        $reactionModel->update(['is_used' => false]);

        return back()->with('success', 'Reaction reset!');
    }

    public function destroy(CombatCharacter $character, int $reaction): RedirectResponse
    {
        $reactionModel = $character->reactions()->findOrFail($reaction);
        $reactionModel->delete();

        return back()->with('success', 'Reaction removed!');
    }
}
