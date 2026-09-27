<?php

namespace App\Models;

use App\Enums\TeamRoleName;
use Illuminate\Database\Eloquent\Model;

class TeamRole extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role_name' => TeamRoleName::class,
        ];
    }

    /**
     * Get the primary key of the role with the given name.
     */
    public static function idFor(TeamRoleName $roleName): int
    {
        return static::query()->where('role_name', $roleName)->valueOrFail('id');
    }
}
