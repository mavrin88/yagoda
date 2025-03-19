<?php

namespace Database\Seeders;


use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Черновик',
            ],
            [
                'name' => 'Новый',
            ],
            [
                'name' => 'admin',
            ],
            [
                'name' => 'master',
            ],
            [
                'name' => 'employee',
            ],
        ];

        foreach ($roles as $role) {
            OrderStatus::create($role);
        }

        $this->command->info('OrderStatusSeeder created successfully.');
    }
}
