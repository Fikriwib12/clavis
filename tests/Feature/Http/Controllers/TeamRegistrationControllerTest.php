<?php

use App\Enums\TeamRoleName;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamRole;
use App\Models\User;

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function webTeamPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'team_name' => 'Garuda_01',
        'captain' => ['gender' => 'Man'],
        'member' => ['name' => 'Siti Aminah', 'phone' => '081298765432', 'gender' => 'Woman'],
    ], $overrides);
}

it('redirects guests to the login form', function (string $method, string $routeName) {
    $response = $this->call($method, route($routeName));

    $response->assertRedirectToRoute('login');
})->with([
    'registration form' => ['GET', 'tournament.create'],
    'registration submission' => ['POST', 'tournament.store'],
    'finish page' => ['GET', 'tournament.finish'],
]);

it('fills the captain name and phone from the logged-in user', function () {
    $user = User::factory()->create(['name' => 'Budi Santoso', 'phone' => '081234567890']);

    $response = $this->actingAs($user)->get(route('tournament.create'));

    $response->assertSee('value="Budi Santoso"', false);
    $response->assertSee('value="081234567890"', false);
});

it('escapes the captain name in the form', function () {
    $user = User::factory()->create(['name' => '<script>alert("xss")</script>']);

    $response = $this->actingAs($user)->get(route('tournament.create'));

    $response->assertSee('&lt;script&gt;', false);
    $response->assertDontSee('<script>alert("xss")</script>', false);
});

it('registers the team with the logged-in user as captain', function () {
    $user = User::factory()->create(['name' => 'Budi Santoso', 'phone' => '081234567890']);

    $response = $this->actingAs($user)->post(route('tournament.store'), webTeamPayload());

    $response->assertRedirectToRoute('tournament.finish');
    $team = Team::sole();
    expect($team)->team_name->toBe('Garuda_01')->user_id->toBe($user->id);
    $this->assertDatabaseHas('team_members', [
        'team_id' => $team->id,
        'team_role_id' => TeamRole::idFor(TeamRoleName::Captain),
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
        'gender' => 'Man',
    ]);
    $this->assertDatabaseHas('team_members', [
        'team_id' => $team->id,
        'team_role_id' => TeamRole::idFor(TeamRoleName::Member),
        'name' => 'Siti Aminah',
        'phone' => '081298765432',
        'gender' => 'Woman',
    ]);
});

it('ignores a captain name and phone submitted by the client', function () {
    $user = User::factory()->create(['name' => 'Budi Santoso', 'phone' => '081234567890']);

    $this->actingAs($user)->post(route('tournament.store'), webTeamPayload([
        'captain' => ['name' => 'Orang Lain', 'phone' => '089999999999'],
    ]));

    $this->assertDatabaseHas('team_members', ['name' => 'Budi Santoso', 'phone' => '081234567890']);
    $this->assertDatabaseMissing('team_members', ['name' => 'Orang Lain']);
});

it('redirects a user whose team is registered to the finish page', function (string $method, string $routeName) {
    $user = User::factory()->create();
    Team::factory()->for($user)->create();

    $response = $this->actingAs($user)->call($method, route($routeName), webTeamPayload());

    $response->assertRedirectToRoute('tournament.finish');
    $this->assertDatabaseCount('teams', 1);
})->with([
    'registration form' => ['GET', 'tournament.create'],
    'registration submission' => ['POST', 'tournament.store'],
]);

it('requires every team field', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tournament.store'), []);

    $response->assertSessionHasErrors([
        'team_name' => 'Nama tim wajib diisi.',
        'captain.gender' => 'Jenis kelamin kapten wajib dipilih.',
        'member.name' => 'Nama anggota wajib diisi.',
        'member.phone' => 'Nomor telepon anggota wajib diisi.',
        'member.gender' => 'Jenis kelamin anggota wajib dipilih.',
    ]);
    $this->assertDatabaseCount('teams', 0);
});

it('rejects invalid team data', function (array $overrides, string $field, string $message) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tournament.store'), webTeamPayload($overrides));

    $response->assertSessionHasErrors([$field => $message]);
    $this->assertDatabaseCount('teams', 0);
})->with([
    'team name shorter than 4 characters' => [['team_name' => 'Gar'], 'team_name', 'Nama tim harus terdiri dari 4-15 karakter.'],
    'team name longer than 15 characters' => [['team_name' => 'Garuda_Nusantara'], 'team_name', 'Nama tim harus terdiri dari 4-15 karakter.'],
    'team name with a space' => [['team_name' => 'Garuda 01'], 'team_name', 'Nama tim hanya boleh berisi huruf, angka, dan garis bawah (_).'],
    'team name with a dash' => [['team_name' => 'Garuda-01'], 'team_name', 'Nama tim hanya boleh berisi huruf, angka, dan garis bawah (_).'],
    'member name with digits' => [['member' => ['name' => 'Siti 2']], 'member.name', 'Nama anggota hanya boleh berisi huruf dan spasi.'],
    'member phone with letters' => [['member' => ['phone' => '08123abc']], 'member.phone', 'Nomor telepon anggota harus berupa angka dengan panjang 7-14 digit.'],
    'member phone shorter than 7 digits' => [['member' => ['phone' => '081234']], 'member.phone', 'Nomor telepon anggota harus berupa angka dengan panjang 7-14 digit.'],
    'member phone longer than 14 digits' => [['member' => ['phone' => '081234567890123']], 'member.phone', 'Nomor telepon anggota harus berupa angka dengan panjang 7-14 digit.'],
    'unknown member gender' => [['member' => ['gender' => 'Other']], 'member.gender', 'Jenis kelamin anggota yang dipilih tidak valid.'],
    'unknown captain gender' => [['captain' => ['gender' => 'Other']], 'captain.gender', 'Jenis kelamin kapten yang dipilih tidak valid.'],
]);

it('rejects a team name that is already taken', function () {
    Team::factory()->create(['team_name' => 'Garuda_01']);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tournament.store'), webTeamPayload());

    $response->assertSessionHasErrors(['team_name' => 'Nama tim sudah digunakan oleh tim lain.']);
    $this->assertDatabaseCount('teams', 1);
});

it('rejects a member who already plays in another team', function () {
    TeamMember::factory()->create(['name' => 'Siti Aminah']);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tournament.store'), webTeamPayload());

    $response->assertSessionHasErrors(['member.name' => 'Nama anggota sudah terdaftar di tim lain.']);
    $this->assertDatabaseMissing('teams', ['user_id' => $user->id]);
});

it('rejects a captain who already plays in another team', function () {
    TeamMember::factory()->create(['name' => 'Budi Santoso']);
    $user = User::factory()->create(['name' => 'Budi Santoso']);

    $response = $this->actingAs($user)->post(route('tournament.store'), webTeamPayload());

    $response->assertSessionHasErrors(['captain.name' => 'Nama kapten sudah terdaftar di tim lain.']);
    $this->assertDatabaseMissing('teams', ['user_id' => $user->id]);
});

it('rejects a member with the same name as the captain regardless of letter case', function () {
    $user = User::factory()->create(['name' => 'Budi Santoso']);

    $response = $this->actingAs($user)->post(route('tournament.store'), webTeamPayload([
        'member' => ['name' => 'budi  SANTOSO'],
    ]));

    $response->assertSessionHasErrors(['member.name' => 'Nama anggota tidak boleh sama dengan nama kapten.']);
    $this->assertDatabaseCount('teams', 0);
});

it('shows the congratulation message on the finish page', function () {
    $user = User::factory()->create();
    Team::factory()->for($user)->create(['team_name' => 'Garuda_01']);

    $response = $this->actingAs($user)->get(route('tournament.finish'));

    $response->assertSee('Pendaftaran Berhasil!');
    $response->assertSee('Garuda_01');
});

it('redirects a user without a team from the finish page to the registration form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('tournament.finish'));

    $response->assertRedirectToRoute('tournament.create');
});
