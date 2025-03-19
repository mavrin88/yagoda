<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            AcountSeeder::class,
            ActivityTypeSeeder::class,
            OrganizationSeeder::class,
            OrganizationUserSeeder::class,
//            OrderStatusSeeder::class,
            AddSettingsSidder::class,
            OrganizationFormsSeeder::class,
            CategoriesAndProductsSeeder::class,
        ]);
    }
}
