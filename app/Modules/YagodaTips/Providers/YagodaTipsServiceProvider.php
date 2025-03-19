<?php

namespace App\Modules\YagodaTips\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class YagodaTipsServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Регистрация любых сервисов модуля
    }

    public function boot()
    {

        Route::middleware('web')
            ->group(base_path('app/Modules/YagodaTips/Routes/web.php'));

        $this->loadMigrationsFrom(base_path('app/Modules/YagodaTips/Database/Migrations'));
    }
}
