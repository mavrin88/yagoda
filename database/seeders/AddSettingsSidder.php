<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddSettingsSidder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'acquiring_fee',
                'value' => '0,69',
            ],
            [
                'key' => 'commission_for_using_the_service',
                'value' => '2,5',
            ],
            [
                'key' => 'order_link_life_time',
                'value' => '3',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
