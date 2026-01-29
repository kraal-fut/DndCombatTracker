<?php

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Notification;

test('registration sends verification email', function () {
    Notification::fake();

    $this->post('/register', [
        'name' => 'Registration Test',
        'email' => 'regtest@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'regtest@example.com')->first();

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});
