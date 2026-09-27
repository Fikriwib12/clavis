<?php

use App\Actions\RegisterTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Validation\ValidationException;

it('turns a unique constraint violation from a concurrent registration into a validation error', function () {
    Team::factory()->create(['team_name' => 'Garuda_01']);
    $captain = User::factory()->create();

    $register = fn () => app(RegisterTeam::class)->handle($captain, [
        'team_name' => 'Garuda_01',
        'captain' => ['gender' => 'Man'],
        'member' => ['name' => 'Siti Aminah', 'phone' => '081298765432', 'gender' => 'Woman'],
    ]);

    expect($register)->toThrow(ValidationException::class, 'Data tim sudah terdaftar. Silakan periksa kembali data Anda.');
    $this->assertDatabaseMissing('teams', ['user_id' => $captain->id]);
    $this->assertDatabaseCount('team_members', 0);
});
