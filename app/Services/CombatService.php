<?php

namespace App\Services;

use App\DataTransferObjects\AddCharacterData;
use App\Models\Combat;
use App\Models\CombatCharacter;
use App\Enums\CombatStatus;

class CombatService
{
    public function createCombat(string $name): Combat
    {
        return Combat::create([
            'name' => $name,
            'status' => CombatStatus::Active,
            'current_round' => 1,
            'current_turn_index' => 0,
        ]);
    }

    public function addCharacter(Combat $combat, AddCharacterData $data): CombatCharacter
    {
        // Get the highest order number across ALL characters
        $order = $combat->characters()->max('order') ?? -1;

        return $combat->characters()->create([
            'name' => $data->name,
            'initiative' => $data->initiative,
            'original_initiative' => $data->initiative,
            'max_hp' => $data->maxHp,
            'current_hp' => $data->currentHp ?? $data->maxHp,
            'armor_class' => $data->armorClass,
            'is_player' => $data->isPlayer,
            'order' => $order + 1,
        ]);
    }

    public function removeCharacter(CombatCharacter $character): void
    {
        $character->delete();
    }

    public function removeAllCharacters(Combat $combat): void
    {
        $combat->characters()->delete();
        $combat->update([
            'current_turn_index' => 0,
            'current_round' => 1,
        ]);
    }

    public function nextTurn(Combat $combat): void
    {
        $combat->nextTurn();
    }

    public function nextRound(Combat $combat): void
    {
        $combat->update([
            'current_turn_index' => 0,
            'current_round' => $combat->current_round + 1,
        ]);
        
        $combat->characters->each(function (CombatCharacter $character) {
            $character->reactions()->update(['is_used' => false]);
            
            $character->conditions()->each(function ($condition) {
                if ($condition->duration_rounds !== null) {
                    $condition->duration_rounds--;
                    if ($condition->duration_rounds <= 0) {
                        $condition->delete();
                    } else {
                        $condition->save();
                    }
                }
            });
            
            $character->stateEffects()->each(function ($effect) {
                if ($effect->duration_rounds !== null) {
                    $effect->duration_rounds--;
                    if ($effect->duration_rounds <= 0) {
                        $effect->delete();
                    } else {
                        $effect->save();
                    }
                }
            });
        });
    }

    public function pauseCombat(Combat $combat): void
    {
        $combat->update(['status' => CombatStatus::Paused]);
    }

    public function resumeCombat(Combat $combat): void
    {
        $combat->update(['status' => CombatStatus::Active]);
    }

    public function endCombat(Combat $combat): void
    {
        $combat->update(['status' => CombatStatus::Completed]);
    }
}
