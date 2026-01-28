<?php

namespace App\Messaging\Handlers;

use App\Messaging\Commands\NextRound;
use App\Messaging\Commands\NextTurn;
use App\Messaging\Commands\UpdateCharacterHP;
use App\Messaging\Events\CharacterHPUpdated;
use App\Messaging\Events\RoundAdvanced;
use App\Messaging\Events\TurnAdvanced;
use App\Models\Combat;
use App\Models\CombatCharacter;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\EventBus;
use App\Enums\HPUpdateType;

class CombatCommandHandler
{
    #[CommandHandler]
    public function handleUpdateHP(UpdateCharacterHP $command, EventBus $eventBus): void
    {
        $character = CombatCharacter::findOrFail($command->characterId);

        if ($command->type === HPUpdateType::Damage) {
            $character->current_hp = max(0, $character->current_hp - $command->changeAmount);
        } else {
            $character->current_hp = min($character->max_hp, $character->current_hp + $command->changeAmount);
        }

        $character->save();

        $eventBus->publish(new CharacterHPUpdated(
            $command->combatId,
            $command->characterId,
            $character->current_hp
        ));
    }

    #[CommandHandler]
    public function handleNextTurn(NextTurn $command, EventBus $eventBus): void
    {
        $combat = Combat::findOrFail($command->combatId);
        $combat->nextTurn();
        $combat->save();

        $eventBus->publish(new TurnAdvanced(
            $command->combatId,
            $combat->current_turn_index
        ));
    }

    #[CommandHandler]
    public function handleNextRound(NextRound $command, EventBus $eventBus): void
    {
        $combat = Combat::findOrFail($command->combatId);
        $combat->nextRound();
        $combat->save();

        $eventBus->publish(new RoundAdvanced(
            $command->combatId,
            $combat->current_round
        ));
    }
}
