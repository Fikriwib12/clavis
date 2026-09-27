<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

it('renders the reset password form with the email from the link', function () {
    $response = $this->get(route('password.reset', ['token' => 'reset-token', 'email' => 'budi@example.com']));

    $response->assertSee('Atur Ulang Kata Sandi');
    $response->assertSee('value="budi@example.com"', false);
});

it('resets the password with a valid token', function () {
    $user = User::factory()->create(['email' => 'budi@example.com']);
    $token = Password::createToken($user);

    $response = $this->post(route('password.store'), [
        'token' => $token,
        'email' => 'budi@example.com',
        'password' => 'passwordbaru1',
        'password_confirmation' => 'passwordbaru1',
    ]);

    $response->assertRedirectToRoute('login');
    $response->assertSessionHas('status', 'Kata sandi berhasil diatur ulang. Silakan masuk dengan kata sandi baru Anda.');
    expect(Hash::check('passwordbaru1', $user->fresh()->password))->toBeTrue();
});

it('rejects an invalid token', function () {
    $user = User::factory()->create(['email' => 'budi@example.com']);

    $response = $this->post(route('password.store'), [
        'token' => 'invalid-token',
        'email' => 'budi@example.com',
        'password' => 'passwordbaru1',
        'password_confirmation' => 'passwordbaru1',
    ]);

    $response->assertSessionHasErrors(['email' => 'Tautan reset kata sandi tidak valid atau sudah kedaluwarsa.']);
    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});

it('rejects an invalid new password', function (array $passwords, string $message) {
    $user = User::factory()->create(['email' => 'budi@example.com']);
    $token = Password::createToken($user);

    $response = $this->post(route('password.store'), [
        'token' => $token,
        'email' => 'budi@example.com',
        ...$passwords,
    ]);

    $response->assertSessionHasErrors(['password' => $message]);
    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
})->with([
    'shorter than 8 characters' => [['password' => 'rahasia', 'password_confirmation' => 'rahasia'], 'Kata sandi harus terdiri dari 8-16 karakter.'],
    'confirmation does not match' => [['password' => 'passwordbaru1', 'password_confirmation' => 'passwordbaru2'], 'Konfirmasi kata sandi tidak cocok.'],
]);
