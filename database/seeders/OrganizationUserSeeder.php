<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('organization_user')->insert([
            'user_id' => 1,
            'organization_id' => 1,
            'role_id' => 2
        ]);

        DB::table('organization_user')->insert([
            'user_id' => 1,
            'organization_id' => 1,
            'role_id' => 3
        ]);

        DB::table('organization_user')->insert([
            'user_id' => 1,
            'organization_id' => 1,
            'role_id' => 4
        ]);
    }
}
