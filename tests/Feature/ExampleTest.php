<?php

use App\Models\User;
use App\Enums\UserRole;

it('returns a successful response', function () {
    $user = User::factory()->create(['role' => UserRole::Player]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
});
