<?php

namespace App\Messaging\Handlers;

use App\Events\CombatStateChanged;
use App\Events\CombatUpdated;
use App\Messaging\Events\CharacterHPUpdated;
use App\Messaging\Events\RoundAdvanced;
use App\Messaging\Events\TurnAdvanced;
use Ecotone\Modelling\Attribute\EventHandler;
use Illuminate\Support\Facades\Event;

class CombatEventHandler
{
    #[EventHandler]
    public function handleCombatStateChanged(CombatStateChanged $event): void
    {
        // Broadcast the update to Laravel Echo/Reverb
        Event::dispatch(new CombatUpdated($event->combatId));
    }

    #[EventHandler]
    public function handleCharacterHPUpdated(CharacterHPUpdated $event): void
    {
        // Broadcast the update to Laravel Echo/Reverb
        Event::dispatch(new CombatUpdated($event->combatId));
    }

    #[EventHandler]
    public function handleTurnAdvanced(TurnAdvanced $event): void
    {
        // Broadcast the update to Laravel Echo/Reverb
        Event::dispatch(new CombatUpdated($event->combatId));
    }

    #[EventHandler]
    public function handleRoundAdvanced(RoundAdvanced $event): void
    {
        Event::dispatch(new CombatUpdated($event->combatId));
    }
}
