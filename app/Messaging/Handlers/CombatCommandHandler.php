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

        match ($command->payload->type) {
            HPUpdateType::Damage => $character->applyDamage(
                $command->payload->damages,
                $command->payload->ignoreResist
            ),
            HPUpdateType::Heal => $character->applyHealing($command->payload->changeAmount),
            HPUpdateType::Temporary => $character->setTemporaryHp($command->payload->changeAmount),
        };
        $character->save();

        $eventBus->publish(new CharacterHPUpdated(
            $command->combatId,
            $command->characterId,
            $character->current_hp,
            $character->temporary_hp
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
