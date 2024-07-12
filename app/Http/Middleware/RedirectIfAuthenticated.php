<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /* public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    } */

    /* public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // Verifique se a rota atual é 'admin-register', se for, continue
            if ($request->route()->getName() == 'admin-register') {
                return $next($request);
            }

            // Caso contrário, redirecione para a página inicial
            return redirect('/home');
        }

        return $next($request);
    } */


    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // Se já existir pelo menos um utilizador, redirecione para a página inicial
            if (User::count() > 0) {
                return redirect('/home');
            }

            // Se não houver utilizadores, permita o acesso à rota 'admin-register'
            if ($request->route()->getName() == 'admin-register') {
                return $next($request);
            }
        }

        return $next($request);
    }

}
