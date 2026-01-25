<?php

use App\Models\Combat;
use App\Models\CombatCharacter;
use App\Models\User;
use App\Enums\UserRole;
use App\DataTransferObjects\AddCharacterData;

// update tests
test('admin can update any character', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $dm->id,
    ]);

    $response = $this->actingAs($admin)->post(
        route('combats.characters.update-hp', [$combat, $character]),
        ['hp_change' => 10, 'change_type' => 'damage']
    );

    $response->assertRedirect();
});

test('dm can update characters in their combat', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $dm->id,
    ]);

    $response = $this->actingAs($dm)->post(
        route('combats.characters.update-hp', [$combat, $character]),
        ['hp_change' => 10, 'change_type' => 'damage']
    );

    $response->assertRedirect();
});

test('dm cannot update characters in other dm combats', function () {
    $dm1 = User::factory()->create(['role' => UserRole::DM]);
    $dm2 = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm1->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $dm1->id,
    ]);

    $response = $this->actingAs($dm2)->post(
        route('combats.characters.update-hp', [$combat, $character]),
        ['hp_change' => 10, 'change_type' => 'damage']
    );

    $response->assertStatus(403);
});

test('player cannot update other players characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player1 = User::factory()->create(['role' => UserRole::Player]);
    $player2 = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $player2->id,
    ]);

    $response = $this->actingAs($player1)->post(
        route('combats.characters.update-hp', [$combat, $character]),
        ['hp_change' => 10, 'change_type' => 'damage']
    );

    $response->assertStatus(403);
});

test('player cannot update npc characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $npc = $combat->characters()->create([
        'name' => 'Goblin',
        'initiative' => 12,
        'original_initiative' => 12,
        'is_player' => false,
        'user_id' => $dm->id,
    ]);

    $response = $this->actingAs($player)->post(
        route('combats.characters.update-hp', [$combat, $npc]),
        ['hp_change' => 10, 'change_type' => 'damage']
    );

    $response->assertStatus(403);
});

test('player can update hp on their own characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'is_player' => true,
        'user_id' => $player->id,
        'current_hp' => 50,
        'max_hp' => 50,
    ]);

    $response = $this->actingAs($player)->post(
        route('combats.characters.update-hp', [$combat, $character]),
        ['hp_change' => 10, 'change_type' => 'damage']
    );

    $response->assertRedirect();
    expect($character->fresh()->current_hp)->toBe(40);
});

// delete tests
test('admin can delete any character', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $dm->id,
    ]);

    $response = $this->actingAs($admin)->delete(route('combats.characters.destroy', [$combat, $character]));

    $response->assertRedirect();
    $this->assertDatabaseMissing('combat_characters', ['id' => $character->id]);
});

test('dm can delete characters in their combat', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $dm->id,
    ]);

    $response = $this->actingAs($dm)->delete(route('combats.characters.destroy', [$combat, $character]));

    $response->assertRedirect();
    $this->assertDatabaseMissing('combat_characters', ['id' => $character->id]);
});

test('dm cannot delete characters in other dm combats', function () {
    $dm1 = User::factory()->create(['role' => UserRole::DM]);
    $dm2 = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm1->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $dm1->id,
    ]);

    $response = $this->actingAs($dm2)->delete(route('combats.characters.destroy', [$combat, $character]));

    $response->assertStatus(302);
});

test('player cannot delete other players characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player1 = User::factory()->create(['role' => UserRole::Player]);
    $player2 = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $player2->id,
    ]);

    $response = $this->actingAs($player1)->delete(route('combats.characters.destroy', [$combat, $character]));

    $response->assertStatus(302);
});

test('player cannot delete npc characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);
    $npc = $combat->characters()->create([
        'name' => 'Goblin',
        'initiative' => 12,
        'original_initiative' => 12,
        'is_player' => false,
        'user_id' => $dm->id,
    ]);

    $response = $this->actingAs($player)->post(
        route('combats.characters.update-hp', [$combat, $npc]),
        ['hp_change' => 10, 'change_type' => 'damage']
    );

    $response->assertStatus(403);
});

test('player can view stats of other player characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player1 = User::factory()->create(['role' => UserRole::Player]);
    $player2 = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $char1 = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'is_player' => true,
        'user_id' => $player1->id,
        'current_hp' => 50,
        'max_hp' => 50,
    ]);

    // Viewer must be in combat to see it
    $combat->characters()->create([
        'name' => 'Cleric',
        'initiative' => 10,
        'original_initiative' => 10,
        'is_player' => true,
        'user_id' => $player2->id,
    ]);

    $response = $this->actingAs($player2)->get(route('combats.show', $combat));

    $response->assertOk();
    $response->assertSee('HP: 50/50');
});
