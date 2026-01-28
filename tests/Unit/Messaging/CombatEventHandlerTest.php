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

    public function test_handle_combat_state_changed_dispatches_broadcast_event(): void
    {
        $event = new CombatStateChanged(1);
        $this->handler->handleCombatStateChanged($event);

        Event::assertDispatched(CombatUpdated::class, function ($event) {
            return $event->combatId === 1;
        });
    }

    public function test_handle_hp_updated_dispatches_broadcast_event(): void
    {
        $event = new CharacterHPUpdated(1, 1, 90);
        $this->handler->handleCharacterHPUpdated($event);

        Event::assertDispatched(CombatUpdated::class, function ($event) {
            return $event->combatId === 1;
        });
    }

    public function test_handle_turn_advanced_dispatches_broadcast_event(): void
    {
        $event = new TurnAdvanced(1, 1);
        $this->handler->handleTurnAdvanced($event);

        Event::assertDispatched(CombatUpdated::class, function ($event) {
            return $event->combatId === 1;
        });
    }

    public function test_handle_round_advanced_dispatches_broadcast_event(): void
    {
        $event = new RoundAdvanced(1, 2);
        $this->handler->handleRoundAdvanced($event);

        Event::assertDispatched(CombatUpdated::class, function ($event) {
            return $event->combatId === 1;
        });
    }
}
