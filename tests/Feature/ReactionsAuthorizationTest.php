<?php

use App\Models\Combat;
use App\Models\User;
use App\Enums\UserRole;

// Add reaction tests
test('player can add reactions to their own characters', function () {
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
        route('characters.reactions.store', $character),
        ['name' => 'Shield']
    );

    $response->assertRedirect();
    $this->assertDatabaseHas('character_reactions', [
        'combat_character_id' => $character->id,
        'name' => 'Shield',
    ]);
});


test('dm can add reactions to any character in their combat', function () {
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
        route('characters.reactions.store', $character),
        ['name' => 'Shield']
    );

    $response->assertRedirect();
    $this->assertDatabaseHas('character_reactions', [
        'combat_character_id' => $character->id,
        'name' => 'Shield',
    ]);
});

test('admin can add reactions to any character', function () {
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
        route('characters.reactions.store', $character),
        ['name' => 'Shield']
    );

    $response->assertRedirect();
    $this->assertDatabaseHas('character_reactions', [
        'combat_character_id' => $character->id,
        'name' => 'Shield',
    ]);
});

// Use reaction tests
test('player can use reactions on their own characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $player->id,
    ]);
    $reaction = $character->reactions()->create([
        'name' => 'Shield',
        'is_used' => false,
    ]);

    $response = $this->actingAs($player)->post(
        route('characters.reactions.use', [$character, $reaction])
    );

    $response->assertRedirect();
    expect($reaction->fresh()->is_used)->toBeTrue();
});


test('dm can use reactions on any character in their combat', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $player->id,
    ]);
    $reaction = $character->reactions()->create([
        'name' => 'Shield',
        'is_used' => false,
    ]);

    $response = $this->actingAs($dm)->post(
        route('characters.reactions.use', [$character, $reaction])
    );

    $response->assertRedirect();
    expect($reaction->fresh()->is_used)->toBeTrue();
});

// Reset reaction tests
test('player can reset reactions on their own characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $player->id,
    ]);
    $reaction = $character->reactions()->create([
        'name' => 'Shield',
        'is_used' => true,
    ]);

    $response = $this->actingAs($player)->post(
        route('characters.reactions.reset', [$character, $reaction])
    );

    $response->assertRedirect();
    expect($reaction->fresh()->is_used)->toBeFalse();
});


test('dm can reset reactions on any character in their combat', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $player->id,
    ]);
    $reaction = $character->reactions()->create([
        'name' => 'Shield',
        'is_used' => true,
    ]);

    $response = $this->actingAs($dm)->post(
        route('characters.reactions.reset', [$character, $reaction])
    );

    $response->assertRedirect();
    expect($reaction->fresh()->is_used)->toBeFalse();
});

// Remove reaction tests
test('player can remove reactions from their own characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $player->id,
    ]);
    $reaction = $character->reactions()->create([
        'name' => 'Shield',
    ]);

    $response = $this->actingAs($player)->delete(
        route('characters.reactions.destroy', [$character, $reaction])
    );

    $response->assertRedirect();
    $this->assertDatabaseMissing('character_reactions', ['id' => $reaction->id]);
});


test('dm can remove reactions from any character in their combat', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $player->id,
    ]);
    $reaction = $character->reactions()->create([
        'name' => 'Shield',
    ]);

    $response = $this->actingAs($dm)->delete(
        route('characters.reactions.destroy', [$character, $reaction])
    );

    $response->assertRedirect();
    $this->assertDatabaseMissing('character_reactions', ['id' => $reaction->id]);
});

test('admin can remove reactions from any character', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Wizard',
        'initiative' => 18,
        'original_initiative' => 18,
        'user_id' => $dm->id,
    ]);
    $reaction = $character->reactions()->create([
        'name' => 'Shield',
    ]);

    $response = $this->actingAs($admin)->delete(
        route('characters.reactions.destroy', [$character, $reaction])
    );

    $response->assertRedirect();
    $this->assertDatabaseMissing('character_reactions', ['id' => $reaction->id]);
});
