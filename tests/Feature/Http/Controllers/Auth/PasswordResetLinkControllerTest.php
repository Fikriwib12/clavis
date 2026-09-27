<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

it('renders the forgot password form', function () {
    $response = $this->get(route('password.request'));

    $response->assertSee('Lupa Kata Sandi');
});

it('emails a password reset link to the account', function () {
    $user = User::factory()->create(['email' => 'budi@example.com']);
    Notification::fake();

    $response = $this->from(route('password.request'))->post(route('password.email'), [
        'email' => 'budi@example.com',
    ]);

    $response->assertRedirectToRoute('password.request');
    $response->assertSessionHas('status', 'Tautan untuk mengatur ulang kata sandi telah dikirim ke email Anda.');
    Notification::assertSentTo($user, ResetPassword::class);
});

it('rejects an email without an account', function () {
    Notification::fake();

    $response = $this->post(route('password.email'), ['email' => 'tidakada@example.com']);

    $response->assertSessionHasErrors(['email' => 'Kami tidak menemukan akun dengan email tersebut.']);
    Notification::assertNothingSent();
});

it('rejects a malformed email', function () {
    Notification::fake();

    $response = $this->post(route('password.email'), ['email' => 'budi@example']);

    $response->assertSessionHasErrors(['email' => 'Email harus berupa alamat email yang valid.']);
    Notification::assertNothingSent();
});
