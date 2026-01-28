<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\AddCharacterData;
use App\Http\Requests\StoreCharacterRequest;
use App\Http\Requests\UpdateHpRequest;
use App\Models\Combat;
use App\Services\CombatService;
use App\Messaging\Commands\UpdateCharacterHP;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Enums\HPUpdateType;

class CombatCharacterController extends Controller
{
    public function create(Combat $combat): View
    {
        return view('combat-characters.create', compact('combat'));
    }

    public function store(StoreCharacterRequest $request, Combat $combat, CombatService $combatService): RedirectResponse
    {
        $combatService->addCharacter(
            combat: $combat,
            data: AddCharacterData::fromRequest($request->validated())
        );

        return redirect()->route('combats.show', $combat)
            ->with('success', 'Character added to combat!');
    }

    public function edit(Combat $combat, int $character): View
    {
        $characterModel = $combat->characters()->findOrFail($character);

        return view('combat-characters.edit', [
            'combat' => $combat,
            'character' => $characterModel,
        ]);
    }

    public function update(StoreCharacterRequest $request, Combat $combat, int $character): RedirectResponse
    {
        $characterModel = $combat->characters()->findOrFail($character);
        $characterModel->update($request->validated());

        return redirect()->route('combats.show', $combat)
            ->with('success', 'Character updated successfully!');
    }

    public function destroy(Combat $combat, int $character, CombatService $combatService): RedirectResponse
    {
        $characterModel = $combat->characters()->findOrFail($character);
        $combatService->removeCharacter($characterModel);

        return back()->with('success', 'Character removed from combat!');
    }

    public function destroyAll(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $combatService->removeAllCharacters($combat);

        return back()->with('success', 'All characters removed from combat!');
    }

    public function updateHp(UpdateHpRequest $request, Combat $combat, int $character, CommandBus $commandBus): RedirectResponse
    {
        $characterModel = $combat->characters()->findOrFail($character);

        // Check authorization
        if (!auth()->user()->can('updateHp', $characterModel)) {
            abort(403, 'You are not authorized to update this character\'s HP.');
        }

        $validated = $request->validated();
        $type = HPUpdateType::from($validated['change_type']);

        $commandBus->send(new UpdateCharacterHP(
            combatId: $combat->id,
            characterId: $characterModel->id,
            changeAmount: abs($validated['hp_change']),
            type: $type
        ));

        $message = $type === HPUpdateType::Damage
            ? abs($validated['hp_change']) . ' damage dealt!'
            : abs($validated['hp_change']) . ' HP restored!';

        return back()->with('success', $message);
    }
}
