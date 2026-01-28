<?php

namespace App\Http\Controllers;

use App\Enums\AdvantageState;
use App\Enums\StateModifierType;
use App\Http\Requests\StoreStateEffectRequest;
use App\Models\CombatCharacter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CharacterStateEffectController extends Controller
{
    public function create(CombatCharacter $character): View
    {
        $modifierTypes = StateModifierType::cases();
        $advantageStates = AdvantageState::cases();

        return view('character-state-effects.create', compact('character', 'modifierTypes', 'advantageStates'));
    }

    public function store(StoreStateEffectRequest $request, CombatCharacter $character): RedirectResponse
    {
        $character->stateEffects()->create($request->validated());

        return redirect()->route('combats.show', $character->combat)
            ->with('success', 'State effect added to character!');
    }

    public function destroy(CombatCharacter $character, int $stateEffect): RedirectResponse
    {
        $stateEffectModel = $character->stateEffects()->findOrFail($stateEffect);
        $stateEffectModel->delete();

        return back()->with('success', 'State effect removed!');
    }
}
