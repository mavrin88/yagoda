<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class ClearDuplicateCookies
{
    /**
     * На данный момент не используется
     *
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // 1. Удаляем host-only cookie (для домена "yagoda.team")
        // Для host-only cookie не указываем параметр domain.
        $response->headers->clearCookie('yagodateam_session', '/');
        $response->headers->clearCookie('yagoda_cookie_session', '/');
        $response->headers->clearCookie('XSRF-TOKEN', '/');

        // 2. Генерируем новый CSRF-токен (Laravel должен генерировать его и хранить в сессии)
        $newToken = csrf_token();

        // 3. Устанавливаем куку XSRF-TOKEN для домена ".yagoda.team"
        // Параметры cookie: имя, значение, время жизни (0 — сессионная), путь, домен, secure, httpOnly, raw, sameSite
        $cookie = new Cookie(
            'XSRF-TOKEN',         // имя куки
            $newToken,            // новое значение
            0,                    // время истечения (сессионная кука)
            '/',                  // путь
            '.yagoda.team',       // домен с ведущей точкой — для доступа с поддоменов
            config('session.secure'), // флаг secure (используйте true, если сайт работает по HTTPS)
            false,                // httpOnly (false, чтобы значение было доступно через JavaScript)
            false,                // raw
            null,                 // sameSite (при необходимости можно указать 'lax', 'strict' или 'none')
        );

        $response->headers->setCookie($cookie);

        return $response;
    }
}
