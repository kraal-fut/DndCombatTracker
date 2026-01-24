<?php

use App\Models\Combat;
use App\Models\User;
use App\Enums\UserRole;

// viewAny tests
test('all authenticated users can view combat list', function () {
    $player = User::factory()->create(['role' => UserRole::Player]);

    $response = $this->actingAs($player)->get(route('combats.index'));

    $response->assertStatus(200);
});

// view tests
test('admin can view any combat', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $response = $this->actingAs($admin)->get(route('combats.show', $combat));

    $response->assertStatus(200);
});

test('dm can view their own combats', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $response = $this->actingAs($dm)->get(route('combats.show', $combat));

    $response->assertStatus(200);
});

test('player can view combats where they have characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'user_id' => $player->id,
    ]);

    $response = $this->actingAs($player)->get(route('combats.show', $combat));

    $response->assertStatus(200);
});

test('player cannot view combats where they have no characters', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $response = $this->actingAs($player)->get(route('combats.show', $combat));

    $response->assertForbidden();
});

// create tests
test('dm can create combats', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);

    $response = $this->actingAs($dm)->get(route('combats.create'));

    $response->assertStatus(200);
});

test('admin can create combats', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $response = $this->actingAs($admin)->get(route('combats.create'));

    $response->assertStatus(200);
});

test('player cannot create combats', function () {
    $player = User::factory()->create(['role' => UserRole::Player]);

    $response = $this->actingAs($player)->get(route('combats.create'));

    $response->assertForbidden();
});

// update tests
test('admin can update any combat', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $response = $this->actingAs($admin)->post(route('combats.next-turn', $combat));

    $response->assertRedirect();
});

test('dm can update their own combats', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $response = $this->actingAs($dm)->post(route('combats.next-turn', $combat));

    $response->assertRedirect();
});

test('dm cannot update other dm combats', function () {
    $dm1 = User::factory()->create(['role' => UserRole::DM]);
    $dm2 = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm1->id]);

    $response = $this->actingAs($dm2)->post(route('combats.next-turn', $combat));

    $response->assertForbidden();
});

test('player cannot update combats', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $response = $this->actingAs($player)->post(route('combats.next-turn', $combat));

    $response->assertForbidden();
});

// delete tests
test('admin can delete any combat', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $response = $this->actingAs($admin)->delete(route('combats.destroy', $combat));

    $response->assertRedirect();
    $this->assertDatabaseMissing('combats', ['id' => $combat->id]);
});

test('dm can delete their own combats', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $response = $this->actingAs($dm)->delete(route('combats.destroy', $combat));

    $response->assertRedirect();
    $this->assertDatabaseMissing('combats', ['id' => $combat->id]);
});

test('dm cannot delete other dm combats', function () {
    $dm1 = User::factory()->create(['role' => UserRole::DM]);
    $dm2 = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm1->id]);

    $response = $this->actingAs($dm2)->delete(route('combats.destroy', $combat));

    $response->assertForbidden();
});

test('player cannot delete combats', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $response = $this->actingAs($player)->delete(route('combats.destroy', $combat));

    $response->assertForbidden();
});

// share tests
test('dm can share their own combats', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $response = $this->actingAs($dm)->post(route('combats.share.generate', $combat));

    $response->assertRedirect();
    expect($combat->fresh()->shares)->toHaveCount(1);
});

test('player cannot share combats', function () {
    $dm = User::factory()->create(['role' => UserRole::DM]);
    $player = User::factory()->create(['role' => UserRole::Player]);
    $combat = Combat::create(['name' => 'Test', 'user_id' => $dm->id]);

    $response = $this->actingAs($player)->post(route('combats.share.generate', $combat));

    $response->assertForbidden();
});
