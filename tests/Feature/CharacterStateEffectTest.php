<?php

use App\DataTransferObjects\AddCharacterData;
use App\Enums\AdvantageState;
use App\Enums\StateModifierType;
use App\Models\Combat;
use App\Models\User;
use App\Enums\UserRole;

test('can add state effect to character', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('characters.state-effects.store', $character), [
        'modifier_type' => StateModifierType::Bonus->value,
        'name' => 'Bless',
        'value' => 3,
        'advantage_state' => AdvantageState::Normal->value,
        'duration_rounds' => 5,
    ]);

    $response->assertRedirect(route('combats.show', $combat));
    $this->assertDatabaseHas('character_state_effects', [
        'combat_character_id' => $character->id,
        'modifier_type' => StateModifierType::Bonus->value,
        'name' => 'Bless',
        'value' => 3,
    ]);
});

test('can add advantage state effect', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('characters.state-effects.store', $character), [
        'modifier_type' => StateModifierType::Bonus->value,
        'name' => 'Lucky',
        'value' => 0,
        'advantage_state' => AdvantageState::Advantage->value,
    ]);

    $response->assertRedirect(route('combats.show', $combat));
    $this->assertDatabaseHas('character_state_effects', [
        'combat_character_id' => $character->id,
        'advantage_state' => AdvantageState::Advantage->value,
    ]);
});

test('can remove state effect from character', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $user->id,
    ]);
    $effect = $character->stateEffects()->create([
        'modifier_type' => StateModifierType::Bonus,
        'name' => 'Bless',
        'value' => 3,
        'advantage_state' => AdvantageState::Normal,
    ]);

    $response = $this->actingAs($user)->delete(route('characters.state-effects.destroy', [$character, $effect]));

    $response->assertRedirect();
    $this->assertDatabaseMissing('character_state_effects', ['id' => $effect->id]);
});

test('state effects with duration are reduced on next round', function () {
    $service = new App\Services\CombatService();
    $user = User::factory()->create();
    $combat = $service->createCombat('Test Battle', $user->id);

    $character = $service->addCharacter($combat, new AddCharacterData('Fighter', 15));
    $effect = $character->stateEffects()->create([
        'modifier_type' => StateModifierType::Bonus,
        'name' => 'Bless',
        'value' => 3,
        'advantage_state' => AdvantageState::Normal,
        'duration_rounds' => 3,
    ]);

    expect($effect->duration_rounds)->toBe(3);

    $service->nextRound($combat);

    expect($effect->fresh()->duration_rounds)->toBe(2);
});
