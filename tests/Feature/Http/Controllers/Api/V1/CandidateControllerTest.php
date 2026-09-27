<?php

use App\Enums\Gender;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns 401 when no token is provided', function () {
    $response = $this->getJson(route('api.v1.candidate.show'));

    $response->assertUnauthorized();
    $response->assertJsonPath('message', 'Unauthenticated.');
});

it('returns 401 when the token is invalid', function () {
    User::factory()->create()->issueApiToken();

    $response = $this->withToken('invalid-token')->getJson(route('api.v1.candidate.show'));

    $response->assertUnauthorized();
});

it('returns the candidate with a null team before the team is registered', function () {
    $user = User::factory()->create([
        'name' => 'Budi Santoso',
        'username' => 'budi12345',
        'email' => 'budi@example.com',
        'phone' => '081234567890',
    ]);
    $token = $user->issueApiToken();

    $response = $this->withToken($token)->getJson(route('api.v1.candidate.show'));

    $response->assertOk();
    $response->assertJson(fn (AssertableJson $json) => $json
        ->has('data', fn (AssertableJson $json) => $json
            ->where('id', $user->id)
            ->where('name', 'Budi Santoso')
            ->where('username', 'budi12345')
            ->where('email', 'budi@example.com')
            ->where('phone', '081234567890')
            ->whereType('created_at', 'string')
            ->where('team', null)
        )
    );
});

it('returns the candidate with the registered team and its players', function () {
    $user = User::factory()->create(['name' => 'Budi Santoso', 'phone' => '081234567890']);
    $team = Team::factory()->for($user)->create(['team_name' => 'Garuda_01']);
    TeamMember::factory()->captain()->for($team)->create(['name' => 'Budi Santoso', 'phone' => '081234567890', 'gender' => Gender::Man]);
    TeamMember::factory()->for($team)->create(['name' => 'Siti Aminah', 'phone' => '081298765432', 'gender' => Gender::Woman]);
    $token = $user->issueApiToken();

    $response = $this->withToken($token)->getJson(route('api.v1.candidate.show'));

    $response->assertOk();
    $response->assertJsonPath('data.team.team_name', 'Garuda_01');
    $response->assertJsonPath('data.team.captain', ['name' => 'Budi Santoso', 'phone' => '081234567890', 'gender' => 'Man']);
    $response->assertJsonPath('data.team.member', ['name' => 'Siti Aminah', 'phone' => '081298765432', 'gender' => 'Woman']);
});
