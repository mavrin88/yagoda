<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

//Artisan::command('inspire', function () {
//    $this->comment(Inspiring::quote());
//})->purpose('Display an inspiring quote')->everyFifteenSeconds();

// Запускаем команду t:t каждые 5 минут
Schedule::command('tochka:run')->everyFiveMinutes();
Schedule::command('tochka:new')->everyFiveMinutes();
