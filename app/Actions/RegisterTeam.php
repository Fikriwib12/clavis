<?php

namespace App\Actions;

use App\Enums\TeamRoleName;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisterTeam
{
    /**
     * Register a tournament team captained by the given user.
     *
     * The captain's name and phone always come from the user's account.
     *
     * @param  array{team_name: string, captain: array{gender: string}, member: array{name: string, phone: string, gender: string}}  $data
     *
     * @throws ValidationException
     */
    public function handle(User $captain, array $data): Team
    {
        try {
            return DB::transaction(function () use ($captain, $data): Team {
                $team = $captain->team()->create(['team_name' => $data['team_name']]);

                $team->members()->createMany([
                    [
                        'team_role_id' => TeamRole::idFor(TeamRoleName::Captain),
                        'name' => $captain->name,
                        'phone' => $captain->phone,
                        'gender' => $data['captain']['gender'],
                    ],
                    [
                        'team_role_id' => TeamRole::idFor(TeamRoleName::Member),
                        'name' => $data['member']['name'],
                        'phone' => $data['member']['phone'],
                        'gender' => $data['member']['gender'],
                    ],
                ]);

                return $team;
            });
        } catch (UniqueConstraintViolationException) {
            // A concurrent request registered the same team name, player, or captain after validation passed.
            throw ValidationException::withMessages([
                'team_name' => 'Data tim sudah terdaftar. Silakan periksa kembali data Anda.',
            ]);
        }
    }
}
