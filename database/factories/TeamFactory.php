<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'team_name' => 'Team_'.fake()->unique()->numberBetween(1000, 9999),
        ];
    }

    /**
     * Indicate that the team has its captain (the registering user) and member filled in.
     */
    public function withMembers(): static
    {
        return $this->afterCreating(function (Team $team): void {
            TeamMember::factory()->captain()->for($team)->create([
                'name' => $team->user->name,
                'phone' => $team->user->phone,
            ]);

            TeamMember::factory()->for($team)->create();
        });
    }
}
