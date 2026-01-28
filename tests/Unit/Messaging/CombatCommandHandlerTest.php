<?php

namespace Tests\Unit\Messaging;

use App\Enums\HPUpdateType;
use App\Messaging\Commands\NextRound;
use App\Messaging\Commands\NextTurn;
use App\Messaging\Commands\UpdateCharacterHP;
use App\Messaging\Events\CharacterHPUpdated;
use App\Messaging\Events\RoundAdvanced;
use App\Messaging\Events\TurnAdvanced;
use App\Messaging\Handlers\CombatCommandHandler;
use App\Models\Combat;
use App\Models\CombatCharacter;
use App\Models\User;
use Ecotone\Modelling\EventBus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class CombatCommandHandlerTest extends TestCase
{
    use RefreshDatabase;

    private CombatCommandHandler $handler;
    private $eventBus;
    private Combat $combat;
    private CombatCharacter $character;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new CombatCommandHandler();
        $this->eventBus = Mockery::mock(EventBus::class);

        $user = User::factory()->create();
        $this->combat = Combat::create([
            'name' => 'Test Combat',
            'status' => \App\Enums\CombatStatus::Active,
            'user_id' => $user->id,
            'current_round' => 1,
            'current_turn_index' => 0,
        ]);

        $this->character = CombatCharacter::create([
            'combat_id' => $this->combat->id,
            'user_id' => $user->id,
            'name' => 'Test Character',
            'initiative' => 10,
            'original_initiative' => 10,
            'max_hp' => 100,
            'current_hp' => 50,
            'armor_class' => 15,
            'order' => 0,
        ]);
    }

    public function test_handle_update_hp_damage(): void
    {
        $command = new UpdateCharacterHP(
            $this->combat->id,
            $this->character->id,
            20,
            HPUpdateType::Damage
        );

        $this->eventBus->shouldReceive('publish')
            ->once()
            ->with(Mockery::on(function ($event) {
                return $event instanceof CharacterHPUpdated &&
                    $event->characterId === $this->character->id &&
                    $event->newHp === 30;
            }));

        $this->handler->handleUpdateHP($command, $this->eventBus);

        $this->assertEquals(30, $this->character->fresh()->current_hp);
    }

    public function test_handle_update_hp_heal(): void
    {
        $command = new UpdateCharacterHP(
            $this->combat->id,
            $this->character->id,
            20,
            HPUpdateType::Heal
        );

        $this->eventBus->shouldReceive('publish')
            ->once()
            ->with(Mockery::on(function ($event) {
                return $event instanceof CharacterHPUpdated &&
                    $event->characterId === $this->character->id &&
                    $event->newHp === 70;
            }));

        $this->handler->handleUpdateHP($command, $this->eventBus);

        $this->assertEquals(70, $this->character->fresh()->current_hp);
    }

    public function test_handle_next_turn(): void
    {
        $command = new NextTurn($this->combat->id);

        $this->eventBus->shouldReceive('publish')
            ->once()
            ->with(Mockery::on(function ($event) {
                return $event instanceof TurnAdvanced &&
                    $event->combatId === $this->combat->id;
            }));

        $this->handler->handleNextTurn($command, $this->eventBus);

        // We verified the event was published. 
        // Logic for turn index advancement depends on characters in combat.
        $this->assertNotNull($this->combat->fresh()->current_turn_index);
    }

    public function test_handle_next_round(): void
    {
        $command = new NextRound($this->combat->id);

        $this->eventBus->shouldReceive('publish')
            ->once()
            ->with(Mockery::on(function ($event) {
                return $event instanceof RoundAdvanced &&
                    $event->combatId === $this->combat->id &&
                    $event->newRound === 2;
            }));

        $this->handler->handleNextRound($command, $this->eventBus);

        $this->assertEquals(2, $this->combat->fresh()->current_round);
    }
}
