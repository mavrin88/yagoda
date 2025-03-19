<?php

namespace Database\Seeders;


use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            [
                'slug' => 'super_puper_admin', // СуперПуперАдмин - мы
                'name' => 'РутАдмин',
            ],
            [
                'slug' => 'super_admin', // СуперАдмин - владелец салона
                'name' => 'Суперадминистратор',
            ],
            [
                'slug' => 'admin', // Админ - человек набивающий чек для оплаты
                'name' => 'Администратор',
            ],
            [
                'slug' => 'master', // Мастер - тот кто получает чаевые
                'name' => 'Мастер',
            ],
            [
                'slug' => 'employee', // Кто будет в последствии Сополучателем чаевых, типа Уборщица... и т.д. Можно назвать эту роль "Сотрудник"
                'name' => 'Персонал',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        $this->command->info('Roles created successfully.');
    }
}
