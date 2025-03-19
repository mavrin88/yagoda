<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DashboardRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'slug' => 'root_admin',
                'name' => 'Рутадминистратор',
                'in_adminka' => true,
            ],
            [
                'slug' => 'documented',
                'name' => 'Документатор',
                'in_adminka' => true,
            ],
            [
                'slug' => 'care_department',
                'name' => 'Отдел заботы',
                'in_adminka' => true,
            ],
            [
                'slug' => 'bookkeeper',
                'name' => 'Бухгалтер',
                'in_adminka' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        $this->command->info('Roles created successfully.');
    }
}
