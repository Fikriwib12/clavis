<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '081234567890',
        ]);

        $captain = User::factory()->create([
            'name' => 'Registered Captain',
            'username' => 'registered1',
            'email' => 'captain@example.com',
            'phone' => '081298765432',
        ]);

        Team::factory()->for($captain)->withMembers()->create(['team_name' => 'Demo_Team']);
    }
}
