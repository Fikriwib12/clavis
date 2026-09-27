<?php

use App\Models\Team;
use App\Models\User;

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function apiTeamPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'team_name' => 'Garuda_01',
        'captain' => ['gender' => 'Man'],
        'member' => ['name' => 'Siti Aminah', 'phone' => '081298765432', 'gender' => 'Woman'],
    ], $overrides);
}

it('returns 401 when no token is provided', function () {
    $response = $this->postJson(route('api.v1.candidate.team.store'), apiTeamPayload());

    $response->assertUnauthorized();
    $this->assertDatabaseCount('teams', 0);
});

it('registers the team and returns 201', function () {
    $user = User::factory()->create(['name' => 'Budi Santoso', 'phone' => '081234567890']);
    $token = $user->issueApiToken();

    $response = $this->withToken($token)->postJson(route('api.v1.candidate.team.store'), apiTeamPayload());

    $response->assertCreated();
    $response->assertJsonPath('message', 'Tim berhasil didaftarkan.');
    $response->assertJsonPath('data.team_name', 'Garuda_01');
    $response->assertJsonPath('data.captain', ['name' => 'Budi Santoso', 'phone' => '081234567890', 'gender' => 'Man']);
    $response->assertJsonPath('data.member', ['name' => 'Siti Aminah', 'phone' => '081298765432', 'gender' => 'Woman']);
    $this->assertDatabaseHas('teams', ['team_name' => 'Garuda_01', 'user_id' => $user->id]);
    $this->assertDatabaseCount('team_members', 2);
});

it('returns 409 when the candidate already registered a team', function () {
    $user = User::factory()->create();
    Team::factory()->for($user)->create();
    $token = $user->issueApiToken();

    $response = $this->withToken($token)->postJson(route('api.v1.candidate.team.store'), apiTeamPayload());

    $response->assertConflict();
    $response->assertJsonPath('message', 'Tim Anda sudah terdaftar pada turnamen ini.');
    $this->assertDatabaseCount('teams', 1);
});

it('returns 422 with the validation message for invalid team data', function () {
    $user = User::factory()->create();
    $token = $user->issueApiToken();

    $response = $this->withToken($token)->postJson(route('api.v1.candidate.team.store'), apiTeamPayload(['team_name' => 'Gar']));

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['team_name' => 'Nama tim harus terdiri dari 4-15 karakter.']);
    $this->assertDatabaseCount('teams', 0);
});
