<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\TeamRoleName;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamMember>
 */
class TeamMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'team_role_id' => fn (): int => TeamRole::idFor(TeamRoleName::Member),
            'name' => preg_replace('/[^A-Za-z ]/', '', fake()->unique()->name()),
            'phone' => fake()->numerify('08##########'),
            'gender' => fake()->randomElement(Gender::cases()),
        ];
    }

    /**
     * Indicate that the player is the captain of the team.
     */
    public function captain(): static
    {
        return $this->state(fn (array $attributes) => [
            'team_role_id' => TeamRole::idFor(TeamRoleName::Captain),
        ]);
    }
}
