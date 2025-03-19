<?php

namespace Database\Seeders;


use App\Models\OrganizationForm;
use Illuminate\Database\Seeder;

class OrganizationFormsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'ООО, АО, ЗАО, ПАО, НКО, ОП',
            ],
            [
                'name' => 'ИП',
            ],
            [
                'name' => 'Самозанятый',
            ]
        ];

        foreach ($roles as $role) {
            OrganizationForm::create($role);
        }

        $this->command->info('OrfanizationFormsSeeder created successfully.');
    }
}
