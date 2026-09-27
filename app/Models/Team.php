<?php

namespace App\Models;

use App\Enums\TeamRoleName;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['team_name'])]
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    /**
     * Get the user account that registered the team.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get every player of the team, regardless of role.
     *
     * @return HasMany<TeamMember, $this>
     */
    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get the player registered as the team captain.
     *
     * @return HasOne<TeamMember, $this>
     */
    public function captain(): HasOne
    {
        return $this->hasOne(TeamMember::class)->whereRelation('role', 'role_name', TeamRoleName::Captain);
    }

    /**
     * Get the player registered as the captain's team member.
     *
     * @return HasOne<TeamMember, $this>
     */
    public function member(): HasOne
    {
        return $this->hasOne(TeamMember::class)->whereRelation('role', 'role_name', TeamRoleName::Member);
    }
}
