<?php

namespace Database\Seeders;


use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class AcountSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $account = Account::create(['name' => 'Yagoda Team']);

        User::factory()->create([
            'account_id' => $account->id,
            'first_name' => 'Алексей',
            'last_name' => 'Марченко',
            'encrypted_first_name' => Crypt::encryptString(''),
            'encrypted_card_number' => Crypt::encryptString(''),
            'encrypted_email' => Crypt::encryptString(''),
            'email' => 'mavrin_88@mail.ru',
            'password' => 'secret',
            'owner' => true,
            'phone' => '+7 (926) 944-24-35'
        ]);
    }
}
