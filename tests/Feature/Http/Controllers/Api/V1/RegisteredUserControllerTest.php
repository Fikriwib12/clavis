<?php

use Illuminate\Testing\Fluent\AssertableJson;

/**
 * @param  array<string, string>  $overrides
 * @return array<string, string>
 */
function apiAccountPayload(array $overrides = []): array
{
    return [
        'name' => 'Budi Santoso',
        'username' => 'budi12345',
        'email' => 'budi@example.com',
        'password' => 'rahasia123',
        'phone' => '081234567890',
        ...$overrides,
    ];
}

it('creates a candidate account and returns 201', function () {
    $response = $this->postJson(route('api.v1.register'), apiAccountPayload());

    $response->assertCreated();
    $response->assertJson(fn (AssertableJson $json) => $json
        ->where('message', 'Registrasi akun berhasil. Silakan login untuk mendapatkan token.')
        ->has('data', fn (AssertableJson $json) => $json
            ->whereType('id', 'integer')
            ->where('name', 'Budi Santoso')
            ->where('username', 'budi12345')
            ->where('email', 'budi@example.com')
            ->where('phone', '081234567890')
            ->whereType('created_at', 'string')
        )
    );
    $this->assertDatabaseHas('users', ['username' => 'budi12345', 'email' => 'budi@example.com']);
});

it('returns 422 with the validation message for invalid data', function () {
    $response = $this->postJson(route('api.v1.register'), apiAccountPayload(['username' => 'budi']));

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['username' => 'Nama pengguna harus terdiri dari 6-15 karakter.']);
    $this->assertDatabaseCount('users', 0);
});
