<?php

use App\Models\Combat;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\ConditionType;

// Add condition tests
test('player can add conditions to their own characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $player->id,
    ]);

    $response = $this->actingAs($player)->post(
        route('characters.conditions.store', $character),
        ['condition_type' => ConditionType::Poisoned->value]
    );

    $response->assertRedirect();
    $this->assertDatabaseHas('character_conditions', [
        'combat_character_id' => $character->id,
        'condition_type' => ConditionType::Poisoned->value,
    ]);
});


test('dm can add conditions to any character in their combat', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $player->id,
    ]);

    $response = $this->actingAs($dm)->post(
        route('characters.conditions.store', $character),
        ['condition_type' => ConditionType::Poisoned->value]
    );

    $response->assertRedirect();
    $this->assertDatabaseHas('character_conditions', [
        'combat_character_id' => $character->id,
        'condition_type' => ConditionType::Poisoned->value,
    ]);
});

test('admin can add conditions to any character', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $dm->id,
    ]);

    $response = $this->actingAs($admin)->post(
        route('characters.conditions.store', $character),
        ['condition_type' => ConditionType::Poisoned->value]
    );

    $response->assertRedirect();
    $this->assertDatabaseHas('character_conditions', [
        'combat_character_id' => $character->id,
        'condition_type' => ConditionType::Poisoned->value,
    ]);
});

// Remove condition tests
test('player can remove conditions from their own characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $player->id,
    ]);
    $condition = $character->conditions()->create([
        'condition_type' => ConditionType::Poisoned,
    ]);

    $response = $this->actingAs($player)->delete(
        route('characters.conditions.destroy', [$character, $condition])
    );

    $response->assertRedirect();
    $this->assertDatabaseMissing('character_conditions', ['id' => $condition->id]);
});


test('dm can remove conditions from any character in their combat', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $player->id,
    ]);
    $condition = $character->conditions()->create([
        'condition_type' => ConditionType::Poisoned,
    ]);

    $response = $this->actingAs($dm)->delete(
        route('characters.conditions.destroy', [$character, $condition])
    );

    $response->assertRedirect();
    $this->assertDatabaseMissing('character_conditions', ['id' => $condition->id]);
});

test('admin can remove conditions from any character', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $dm->id,
    ]);
    $condition = $character->conditions()->create([
        'condition_type' => ConditionType::Poisoned,
    ]);

    $response = $this->actingAs($admin)->delete(
        route('characters.conditions.destroy', [$character, $condition])
    );

    $response->assertRedirect();
    $this->assertDatabaseMissing('character_conditions', ['id' => $condition->id]);
});
