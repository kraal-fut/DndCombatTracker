<?php

use App\Models\User;
use App\Enums\UserRole;

test('new users are assigned player role by default', function () {
    $response = $this->post('/register', [
        'name' => 'Test Player',
        'email' => 'player@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    $user = User::where('email', 'player@example.com')->first();
    expect($user->role)->toBe(UserRole::Player);
});

test('player can access dashboard after registration', function () {
    $response = $this->post('/register', [
        'name' => 'Test Player',
        'email' => 'player@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));

    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});

test('player cannot create combats after registration', function () {
    $user = User::factory()->create(['role' => UserRole::Player]);

    $response = $this->actingAs($user)->get(route('combats.create'));

    $response->assertForbidden();
});

test('player cannot access admin panel after registration', function () {
    $user = User::factory()->create(['role' => UserRole::Player]);

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertForbidden();
});

test('user role is displayed correctly in navigation after login', function () {
    $user = User::factory()->create([
        'role' => UserRole::Player,
        'email' => 'test@example.com',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Player');
});

test('dm role is displayed correctly in navigation', function () {
    $user = User::factory()->create([
        'role' => UserRole::DM,
        'email' => 'dm@example.com',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Dungeon Master');
});

test('admin role is displayed correctly in navigation', function () {
    $user = User::factory()->create([
        'role' => UserRole::Admin,
        'email' => 'admin@example.com',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Administrator');
});
