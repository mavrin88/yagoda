<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activityTypes = [
            'Салон красоты',
            'Барбершоп',
            'Автомойка',
            'Баня',
            'Автосервис',
            'Ресторан',
            'Цветочный салон',
        ];

        foreach ($activityTypes as $type) {
            ActivityType::create(['name' => $type]);
        }
    }
}
