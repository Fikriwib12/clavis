<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

it('renders the login form', function () {
    $response = $this->get(route('login'));

    $response->assertSee('Selamat Datang Kembali');
});

it('redirects an authenticated user away from the login form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('login'));

    $response->assertRedirectToRoute('tournament.create');
});

it('logs in and redirects to the team registration form', function () {
    $user = User::factory()->create(['username' => 'budi12345']);

    $response = $this->post(route('login.store'), [
        'username' => 'budi12345',
        'password' => 'password',
    ]);

    $response->assertRedirectToRoute('tournament.create');
    $this->assertAuthenticatedAs($user);
});

it('redirects a user whose team is registered to the finish page after login', function () {
    $user = User::factory()->create(['username' => 'budi12345']);
    Team::factory()->for($user)->create();

    $response = $this->post(route('login.store'), [
        'username' => 'budi12345',
        'password' => 'password',
    ]);

    $response->assertRedirectToRoute('tournament.finish');
    $this->assertAuthenticatedAs($user);
});

it('sets the remember cookie when "Ingat saya" is checked', function () {
    User::factory()->create(['username' => 'budi12345']);

    $response = $this->post(route('login.store'), [
        'username' => 'budi12345',
        'password' => 'password',
        'remember' => '1',
    ]);

    $response->assertCookie(Auth::guard('web')->getRecallerName());
});

it('rejects a username that is not registered', function () {
    $response = $this->from(route('login'))->post(route('login.store'), [
        'username' => 'tidakada123',
        'password' => 'password',
    ]);

    $response->assertRedirectToRoute('login');
    $response->assertSessionHasErrors(['username' => 'Username tidak terdaftar.']);
    $this->assertGuest();
});

it('rejects a wrong password', function () {
    User::factory()->create(['username' => 'budi12345']);

    $response = $this->post(route('login.store'), [
        'username' => 'budi12345',
        'password' => 'salahsalah',
    ]);

    $response->assertSessionHasErrors(['password' => 'Kata sandi yang Anda masukkan salah.']);
    $this->assertGuest();
});

it('rejects credentials that break the format rules', function (array $credentials, string $field, string $message) {
    $response = $this->post(route('login.store'), $credentials);

    $response->assertSessionHasErrors([$field => $message]);
    $this->assertGuest();
})->with([
    'missing username' => [['username' => '', 'password' => 'password'], 'username', 'Username wajib diisi.'],
    'username shorter than 6 characters' => [['username' => 'budi1', 'password' => 'password'], 'username', 'Username harus terdiri dari 6-15 karakter.'],
    'username longer than 15 characters' => [['username' => 'budisantoso12345', 'password' => 'password'], 'username', 'Username harus terdiri dari 6-15 karakter.'],
    'username with a symbol' => [['username' => 'budi_123', 'password' => 'password'], 'username', 'Username hanya boleh berisi huruf dan angka.'],
    'missing password' => [['username' => 'budi12345', 'password' => ''], 'password', 'Kata sandi wajib diisi.'],
    'password shorter than 8 characters' => [['username' => 'budi12345', 'password' => 'rahasia'], 'password', 'Kata sandi harus terdiri dari 8-16 karakter.'],
    'password longer than 16 characters' => [['username' => 'budi12345', 'password' => 'rahasiarahasia123'], 'password', 'Kata sandi harus terdiri dari 8-16 karakter.'],
]);

it('locks the login after five failed attempts', function () {
    $this->freezeTime();
    User::factory()->create(['username' => 'budi12345']);

    foreach (range(1, 5) as $attempt) {
        $this->post(route('login.store'), ['username' => 'budi12345', 'password' => 'salahsalah']);
    }

    $response = $this->post(route('login.store'), ['username' => 'budi12345', 'password' => 'password']);

    $response->assertSessionHasErrors(['username' => 'Terlalu banyak percobaan masuk. Silakan coba lagi dalam 60 detik.']);
    $this->assertGuest();
});

it('logs the user out and clears the session', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->withSession(['captain_draft' => 'Garuda'])->post(route('logout'));

    $response->assertRedirectToRoute('login');
    $response->assertSessionMissing('captain_draft');
    $this->assertGuest();
});
