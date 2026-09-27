<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('team_roles')->insertOrIgnore([
            ['role_name' => 'Captain', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'Member', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('team_roles')->whereIn('role_name', ['Captain', 'Member'])->delete();
    }
};
