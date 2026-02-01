<?php

namespace App\Http\Controllers;

use App\Enums\ConditionType;
use App\Http\Requests\StoreConditionRequest;
use App\Models\CombatCharacter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CharacterConditionController extends Controller
{
    public function create(CombatCharacter $character): View
    {
        $conditionTypes = ConditionType::cases();

        return view('character-conditions.create', compact('character', 'conditionTypes'));
    }

    public function store(StoreConditionRequest $request, CombatCharacter $character): RedirectResponse
    {
        $validated = $request->validated();
        $bypassImmunity = (bool) ($validated['bypass_immunity'] ?? false);

        $success = $character->addCondition($validated, $bypassImmunity);

        if (!$success) {
            return back()->with('error', "Character is immune to {$validated['condition_type']}!");
        }

        return redirect()->route('combats.show', $character->combat)
            ->with('success', 'Condition added to character!');
    }

    public function destroy(CombatCharacter $character, int $condition): RedirectResponse
    {
        $conditionModel = $character->conditions()->findOrFail($condition);
        $conditionModel->delete();

        return back()->with('success', 'Condition removed!');
    }
}
