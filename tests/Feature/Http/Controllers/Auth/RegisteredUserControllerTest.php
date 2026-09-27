<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * @param  array<string, string>  $overrides
 * @return array<string, string>
 */
function webAccountPayload(array $overrides = []): array
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

it('renders the registration form', function () {
    $response = $this->get(route('register'));

    $response->assertSee('Buat Akun Baru');
});

it('creates the account and asks the user to log in', function () {
    $response = $this->post(route('register.store'), webAccountPayload(['name' => '  Budi   Santoso ']));

    $response->assertRedirectToRoute('login');
    $response->assertSessionHas('status', 'Akun berhasil dibuat. Silakan masuk untuk mendaftarkan tim Anda.');
    $this->assertDatabaseHas('users', [
        'name' => 'Budi Santoso',
        'username' => 'budi12345',
        'email' => 'budi@example.com',
        'phone' => '081234567890',
    ]);
    expect(Hash::check('rahasia123', User::sole()->password))->toBeTrue();
    $this->assertGuest();
});

it('requires every field', function () {
    $response = $this->post(route('register.store'), []);

    $response->assertSessionHasErrors([
        'name' => 'Nama lengkap wajib diisi.',
        'username' => 'Nama pengguna wajib diisi.',
        'email' => 'Email wajib diisi.',
        'password' => 'Kata sandi wajib diisi.',
        'phone' => 'Nomor telepon wajib diisi.',
    ]);
    $this->assertDatabaseCount('users', 0);
});

it('rejects invalid account data', function (array $overrides, string $field, string $message) {
    $response = $this->post(route('register.store'), webAccountPayload($overrides));

    $response->assertSessionHasErrors([$field => $message]);
    $this->assertDatabaseCount('users', 0);
})->with([
    'name with digits' => [['name' => 'Budi 123'], 'name', 'Nama lengkap hanya boleh berisi huruf dan spasi.'],
    'username shorter than 6 characters' => [['username' => 'budi1'], 'username', 'Nama pengguna harus terdiri dari 6-15 karakter.'],
    'username longer than 15 characters' => [['username' => 'budisantoso12345'], 'username', 'Nama pengguna harus terdiri dari 6-15 karakter.'],
    'username with a symbol' => [['username' => 'budi.santoso'], 'username', 'Nama pengguna hanya boleh berisi huruf dan angka.'],
    'malformed email' => [['email' => 'budi@example'], 'email', 'Email harus berupa alamat email yang valid.'],
    'password shorter than 8 characters' => [['password' => 'rahasia'], 'password', 'Kata sandi harus terdiri dari 8-16 karakter.'],
    'password longer than 16 characters' => [['password' => 'rahasiarahasia123'], 'password', 'Kata sandi harus terdiri dari 8-16 karakter.'],
    'phone with letters' => [['phone' => '0812abc4567'], 'phone', 'Nomor telepon harus berupa angka dengan panjang 7-14 digit.'],
    'phone shorter than 7 digits' => [['phone' => '081234'], 'phone', 'Nomor telepon harus berupa angka dengan panjang 7-14 digit.'],
    'phone longer than 14 digits' => [['phone' => '081234567890123'], 'phone', 'Nomor telepon harus berupa angka dengan panjang 7-14 digit.'],
]);

it('rejects a username that is already taken', function () {
    User::factory()->create(['username' => 'budi12345']);

    $response = $this->post(route('register.store'), webAccountPayload());

    $response->assertSessionHasErrors(['username' => 'Nama pengguna sudah digunakan.']);
    $this->assertDatabaseCount('users', 1);
});

it('rejects an email that is already registered', function () {
    User::factory()->create(['email' => 'budi@example.com']);

    $response = $this->post(route('register.store'), webAccountPayload());

    $response->assertSessionHasErrors(['email' => 'Email sudah terdaftar.']);
    $this->assertDatabaseCount('users', 1);
});
