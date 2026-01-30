<?php

namespace App\Services;

use App\DataTransferObjects\AddCharacterData;
use App\Models\Combat;
use App\Models\CombatCharacter;
use App\Models\CharacterCondition;
use App\Models\CharacterStateEffect;
use App\Enums\CombatStatus;

class CombatService
{
    public function createCombat(string $name, int $userId): Combat
    {
        return Combat::create([
            'name' => $name,
            'user_id' => $userId,
            'status' => CombatStatus::Preparation,
            'current_round' => 1,
            'current_turn_index' => 0,
        ]);
    }

    public function addCharacter(Combat $combat, AddCharacterData $data): CombatCharacter
    {
        $maxOrder = $combat->characters()->max('order') ?? 0;

        $character = $combat->characters()->create([
            'name' => $data->name,
            'initiative' => $data->initiative,
            'original_initiative' => $data->initiative,
            'max_hp' => $data->maxHp,
            'current_hp' => $data->currentHp ?? $data->maxHp,
            'is_player' => $data->isPlayer,
            'user_id' => $data->userId,
            'order' => $maxOrder + 1,
        ]);

        if ($combat->status === CombatStatus::Preparation) {
            $this->sortCharactersByInitiative($combat);
        }

        /** @var CombatCharacter $character */
        return $character;
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

            $character->conditions()->each(function (CharacterCondition $condition) {
                if ($condition->duration_rounds !== null) {
                    $condition->duration_rounds--;
                    if ($condition->duration_rounds <= 0) {
                        $condition->delete();
                    } else {
                        $condition->save();
                    }
                }
            });

            $character->stateEffects()->each(function (CharacterStateEffect $effect) {
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
        // Only sort characters when starting combat for the first time (round 1)
        // Don't resort when resuming a paused combat mid-game
        if ($combat->current_round === 1) {
            $this->sortCharactersByInitiative($combat);
        }

        $combat->update([
            'status' => CombatStatus::Active,
            'current_turn_index' => 0,
        ]);
    }

    public function startCombat(Combat $combat): void
    {
        $this->sortCharactersByInitiative($combat);

        $combat->update([
            'status' => CombatStatus::Active,
            'current_turn_index' => 0,
            'current_round' => 1,
        ]);
    }

    public function sortCharactersByInitiative(Combat $combat): void
    {
        // Reset all character orders to 0 so they sort purely by initiative
        $combat->characters()->update(['order' => 0]);
    }

    public function endCombat(Combat $combat): void
    {
        $combat->update(['status' => CombatStatus::Completed]);
    }
}
