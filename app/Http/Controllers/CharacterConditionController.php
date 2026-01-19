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
        $character->conditions()->create($request->validated());

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
