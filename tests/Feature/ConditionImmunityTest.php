<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Combat;
use App\Models\CombatCharacter;
use App\Models\User;
use App\Enums\ConditionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConditionImmunityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Combat $combat;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->combat = Combat::factory()->create(['user_id' => $this->user->id]);
    }

    public function testCharacterWithImmunityBlocksConditionApplication(): void
    {
        /** @var CombatCharacter $character */
        $character = CombatCharacter::factory()->create([
            'combat_id' => $this->combat->id,
            'condition_immunities' => [ConditionType::Poisoned->value]
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('characters.conditions.store', $character), [
                'condition_type' => ConditionType::Poisoned->value,
                'duration_rounds' => 10,
            ]);

        $response->assertSessionHas('error');
        $this->assertCount(0, $character->fresh()->conditions);
    }

    public function testCharacterWithoutImmunityAllowsConditionApplication(): void
    {
        /** @var CombatCharacter $character */
        $character = CombatCharacter::factory()->create([
            'combat_id' => $this->combat->id,
            'condition_immunities' => [ConditionType::Charmed->value]
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('characters.conditions.store', $character), [
                'condition_type' => ConditionType::Poisoned->value,
                'duration_rounds' => 10,
            ]);

        $response->assertSessionHas('success');
        $this->assertCount(1, $character->fresh()->conditions);
    }

    public function testBypassImmunityAllowsApplicationToImmuneCharacter(): void
    {
        /** @var CombatCharacter $character */
        $character = CombatCharacter::factory()->create([
            'combat_id' => $this->combat->id,
            'condition_immunities' => [ConditionType::Poisoned->value]
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('characters.conditions.store', $character), [
                'condition_type' => ConditionType::Poisoned->value,
                'duration_rounds' => 10,
                'bypass_immunity' => true,
            ]);

        $response->assertSessionHas('success');
        $this->assertCount(1, $character->fresh()->conditions);
    }

    public function testConditionImmunitiesArePersistedOnCharacterCreation(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('combats.characters.store', $this->combat), [
                'name' => 'Immune Hero',
                'initiative' => 15,
                'max_hp' => 100,
                'current_hp' => 100,
                'condition_immunities' => [
                    ConditionType::Poisoned->value,
                    ConditionType::Frightened->value
                ],
            ]);

        $response->assertRedirect(route('combats.show', $this->combat));

        $character = CombatCharacter::where('name', 'Immune Hero')->first();
        $this->assertNotNull($character);
        $this->assertContains(ConditionType::Poisoned->value, $character->condition_immunities);
        $this->assertContains(ConditionType::Frightened->value, $character->condition_immunities);
    }
}
