<?php

use App\Models\User;

it('returns a bearer token that authenticates later requests', function () {
    $user = User::factory()->create(['username' => 'budi12345']);

    $response = $this->postJson(route('api.v1.login'), [
        'username' => 'budi12345',
        'password' => 'password',
    ]);

    $response->assertOk();
    $response->assertJsonPath('message', 'Login berhasil.');
    $response->assertJsonPath('data.token_type', 'Bearer');
    $token = $response->json('data.access_token');
    expect($user->fresh()->api_token)->not->toBeNull()->not->toBe($token);
    $this->withToken($token)->getJson(route('api.v1.candidate.show'))->assertJsonPath('data.username', 'budi12345');
});

it('returns 422 when the password is wrong', function () {
    $user = User::factory()->create(['username' => 'budi12345']);

    $response = $this->postJson(route('api.v1.login'), [
        'username' => 'budi12345',
        'password' => 'salahsalah',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['password' => 'Kata sandi yang Anda masukkan salah.']);
    expect($user->fresh()->api_token)->toBeNull();
});

it('returns 422 when the username is not registered', function () {
    $response = $this->postJson(route('api.v1.login'), [
        'username' => 'tidakada123',
        'password' => 'password',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['username' => 'Username tidak terdaftar.']);
});

it('revokes the token on logout', function () {
    $user = User::factory()->create();
    $token = $user->issueApiToken();

    $response = $this->withToken($token)->postJson(route('api.v1.logout'));

    $response->assertOk();
    $response->assertJsonPath('message', 'Logout berhasil. Token sudah tidak berlaku.');
    expect($user->fresh()->api_token)->toBeNull();
});

it('returns 401 when logging out without a token', function () {
    $response = $this->postJson(route('api.v1.logout'));

    $response->assertUnauthorized();
});
