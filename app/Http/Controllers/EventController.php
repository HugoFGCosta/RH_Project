<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = auth()->user()->id;

            $data = Event::where('user_id', $userId)
                ->whereDate('start', '>=', $request->start)
                ->whereDate('end', '<=', $request->end)
                ->get(['id', 'title', 'start', 'end']);

            return response()->json($data);
        }

        /* $users = User::orderBy('id', 'desc')->get();                 // exemplo para enviar Faltas, Presenças, Ferias
        return view('pages.users.index', ['users' => $users]); */
        $events = Event::all();
        return view('fullcalender', ['events' => $events]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }

    public function ajax(Request $request): JsonResponse
    {
        $userId = auth()->user()->id; // Obtém o ID do usuário logado

        switch ($request->type) {
            case 'add':
                // Verifica se já existe um evento para o usuário na data selecionada
                $eventExists = Event::where('user_id', $userId)
                    ->whereDate('start', $request->start)
                    ->whereDate('end', $request->end)
                    ->exists();

                if (!$eventExists) {
                    // Cria um novo evento para o usuário
                    $event = new Event([
                        'user_id' => $userId,
                        'title' => $request->title,
                        'start' => $request->start,
                        'end' => $request->end,
                    ]);

                    $event->save();

                    return response()->json($event);
                } else {
                    return response()->json(['message' => 'Já existe um evento para este usuário nesta data.']);
                }
                break;

            case 'update':
                $event = Event::where('user_id', $userId) // Verifica se o evento pertence ao usuário logado
                    ->find($request->id)
                    ->update([
                        'title' => $request->title,
                        'start' => $request->start,
                        'end' => $request->end,
                    ]);

                return response()->json($event);
                break;

            case 'delete':
                $event = Event::where('user_id', $userId) // Verifica se o evento pertence ao usuário logado
                    ->find($request->id)
                    ->delete();

                return response()->json($event);
                break;

            default:
                # code...
                break;
        }
    }



}
