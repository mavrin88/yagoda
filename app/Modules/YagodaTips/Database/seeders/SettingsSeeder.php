<?php

namespace app\Modules\YagodaTips\Database\seeders;

use app\Modules\YagodaTips\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'maximum_tip_amount',
                'value' => '5000',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
