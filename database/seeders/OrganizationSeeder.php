<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::create(
            [
                'account_id' => 1,
                'name' => 'Салон красоты',
                'full_name' => 'OOO Салон красоты',
                'phone' => '+7 (926) 944-24-35',
                'inn' => '123456789',
                'contact_phone' => '+7 (926) 944-24-35',
                'contact_name' => 'Иван Петров',
                'legal_address' => 'г. Москва, ул. Ленина, д. 1',
                'email' => 'johndoe@example.com',
                'registration_number' => 'г. Москва, ул. Ленина, д. 1',
                'acquiring_fee' => '17',
                'tips_1' => '10',
                'tips_2' => '15',
                'tips_3' => '20',
                'tips_4' => '25',
                'activity_type_id' => 1,
                'form_id' => 1,
            ]
        );
    }
}
