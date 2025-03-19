<?php
    namespace App\Helpers;

    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Cache;

    // Данные пользователя через login.yagoda.team
    class AuthHelper
    {
        public static function getUser()
        {
            if (Cache::has('current_user')) {
                return Cache::get('current_user');
            }

            $response = Http::withCookies(request()->cookies->all(), '.yagoda.team')
                ->get('https://login.yagoda.team/api/user');

            if ($response->successful()) {
                $user = $response->json();
                // Кешируем на 5 минут
                Cache::put('current_user', $user, 300);
                return $user;
            }

            return null;
        }
    }
