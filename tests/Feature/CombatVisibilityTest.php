<?php

use App\Models\Combat;
use App\Models\User;
use App\Enums\UserRole;
use App\Models\CombatCharacter;

test('admins can see all combats', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $dm = User::factory()->create(['role' => UserRole::DM]);

    Combat::factory()->count(3)->create(['user_id' => $dm->id]);

    $response = $this->actingAs($admin)->get(route('combats.index'));

    $response->assertStatus(200);
    $response->assertViewHas('combats', function ($combats) {
        return $combats->count() === 3;
    });
});

test('dms only see their own combats or combats they have characters in', function () {
    $dm1 = User::factory()->create(['role' => UserRole::DM]);
    $dm2 = User::factory()->create(['role' => UserRole::DM]);

    // Combat owned by DM1
    Combat::factory()->create(['user_id' => $dm1->id, 'name' => 'DM1 Combat']);

    // Combat owned by DM2
    Combat::factory()->create(['user_id' => $dm2->id, 'name' => 'DM2 Combat']);

    // Combat owned by DM2 but DM1 has a character in it
    $otherCombat = Combat::factory()->create(['user_id' => $dm2->id, 'name' => 'Shared Combat']);
    CombatCharacter::factory()->create([
        'combat_id' => $otherCombat->id,
        'user_id' => $dm1->id
    ]);

    $response = $this->actingAs($dm1)->get(route('combats.index'));

    $response->assertStatus(200);
    $response->assertViewHas('combats', function ($combats) {
        return $combats->count() === 2 &&
            $combats->pluck('name')->contains('DM1 Combat') &&
            $combats->pluck('name')->contains('Shared Combat') &&
            !$combats->pluck('name')->contains('DM2 Combat');
    });
});

test('players only see combats they have characters in', function () {
    $player = User::factory()->create(['role' => UserRole::Player]);
    $dm = User::factory()->create(['role' => UserRole::DM]);

    // Combat player is part of
    $combat1 = Combat::factory()->create(['user_id' => $dm->id, 'name' => 'Player Combat']);
    CombatCharacter::factory()->create([
        'combat_id' => $combat1->id,
        'user_id' => $player->id
    ]);

    // Combat player is NOT part of
    Combat::factory()->create(['user_id' => $dm->id, 'name' => 'Private Combat']);

    $response = $this->actingAs($player)->get(route('combats.index'));

    $response->assertStatus(200);
    $response->assertViewHas('combats', function ($combats) {
        return $combats->count() === 1 &&
            $combats->first()->name === 'Player Combat';
    });
});
