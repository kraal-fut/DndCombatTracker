<?php

namespace Tests\Unit\Messaging;

use App\Events\CombatStateChanged;
use App\Events\CombatUpdated;
use App\Messaging\Events\CharacterHPUpdated;
use App\Messaging\Events\RoundAdvanced;
use App\Messaging\Events\TurnAdvanced;
use App\Messaging\Handlers\CombatEventHandler;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CombatEventHandlerTest extends TestCase
{
    private CombatEventHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new CombatEventHandler();
        Event::fake();
    }

    public function testHandleCombatStateChangedDispatchesBroadcastEvent(): void
    {
        $event = new CombatStateChanged(1);
        $this->handler->handleCombatStateChanged($event);

        Event::assertDispatched(CombatUpdated::class, function ($event) {
            return $event->combatId === 1;
        });
    }

    public function testHandleHpUpdatedDispatchesBroadcastEvent(): void
    {
        $event = new CharacterHPUpdated(1, 1, 90);
        $this->handler->handleCharacterHPUpdated($event);

        Event::assertDispatched(CombatUpdated::class, function ($event) {
            return $event->combatId === 1;
        });
    }

    public function testHandleTurnAdvancedDispatchesBroadcastEvent(): void
    {
        $event = new TurnAdvanced(1, 1);
        $this->handler->handleTurnAdvanced($event);

        Event::assertDispatched(CombatUpdated::class, function ($event) {
            return $event->combatId === 1;
        });
    }

    public function testHandleRoundAdvancedDispatchesBroadcastEvent(): void
    {
        $event = new RoundAdvanced(1, 2);
        $this->handler->handleRoundAdvanced($event);

        Event::assertDispatched(CombatUpdated::class, function ($event) {
            return $event->combatId === 1;
        });
    }
}
