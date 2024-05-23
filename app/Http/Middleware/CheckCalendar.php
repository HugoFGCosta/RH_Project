<?php

namespace App\Http\Middleware;

use Closure;
use Event;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCalendar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $userId = auth()->user()->id; // BUSCA o ID do user logado

        // VERIFICA se ja existe um evento para o usuer
        $eventExists = Event::where('user_id', $userId)->exists();

        if (!$eventExists) {
            // CRIA um novo evento para o user
            Event::create([
                'user_id' => $userId,
                'title' => 'Novo Evento',
                'start' => now(),
                'end' => now()->addHour(),
            ]);
        }

        return $next($request);
    }

}