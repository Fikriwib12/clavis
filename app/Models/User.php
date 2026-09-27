<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'username', 'email', 'password', 'phone'])]
#[Hidden(['password', 'remember_token', 'api_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the tournament team registered by the user as its captain.
     *
     * @return HasOne<Team, $this>
     */
    public function team(): HasOne
    {
        return $this->hasOne(Team::class);
    }

    /**
     * Determine if the user has already registered a team for the tournament.
     */
    public function hasRegisteredTeam(): bool
    {
        return $this->team()->exists();
    }

    /**
     * Issue a new API token, replacing any token issued previously.
     *
     * Only the SHA-256 hash is stored, so the plain-text token is returned once to the client.
     */
    public function issueApiToken(): string
    {
        $plainTextToken = Str::random(60);

        $this->forceFill(['api_token' => hash('sha256', $plainTextToken)])->save();

        return $plainTextToken;
    }

    /**
     * Revoke the user's current API token.
     */
    public function revokeApiToken(): void
    {
        $this->forceFill(['api_token' => null])->save();
    }
}
