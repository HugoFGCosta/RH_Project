<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Event;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::id();

            // Eventos normais
            $events = Event::where('user_id', $userId)
                ->where(function($query) use ($request) {
                    $query->whereBetween('start', [$request->start, $request->end])
                        ->orWhereBetween('end', [$request->start, $request->end])
                        ->orWhere(function($query) use ($request) {
                            $query->where('start', '<', $request->start)
                                ->where('end', '>', $request->end);
                        });
                })
                ->get(['id', 'title', 'start', 'end']);

            Log::info('Eventos normais recuperados', ['events' => $events]);

            // Eventos de férias
            $vacations = Vacation::where('user_id', $userId)
                ->where('vacation_approval_states_id', 1) // Apenas férias aprovadas
                ->where(function($query) use ($request) {
                    $query->whereBetween('date_start', [$request->start, $request->end])
                        ->orWhereBetween('date_end', [$request->start, $request->end])
                        ->orWhere(function($query) use ($request) {
                            $query->where('date_start', '<', $request->start)
                                ->where('date_end', '>', $request->end);
                        });
                })
                ->get(['id', 'date_start as start', 'date_end as end']);

            foreach ($vacations as $vacation) {
                $vacation->title = 'Férias';
                $vacation->is_vacation = true;
            }

            Log::info('Eventos de férias recuperados', ['vacations' => $vacations]);

            // Eventos de faltas
            $absences = Absence::where('user_id', $userId)
                ->where(function($query) use ($request) {
                    $query->whereBetween('absence_start_date', [$request->start, $request->end])
                        ->orWhereBetween('absence_end_date', [$request->start, $request->end])
                        ->orWhere(function($query) use ($request) {
                            $query->where('absence_start_date', '<', $request->start)
                                ->where('absence_end_date', '>', $request->end);
                        });
                })
                ->get(['id', 'absence_start_date as start', 'absence_end_date as end']);

            foreach ($absences as $absence) {
                $absence->title = 'Falta';
                $absence->is_absence = true;
            }

            Log::info('Eventos de faltas recuperados', ['absences' => $absences]);

            // Mesclar eventos
            $allEvents = $events->concat($vacations)->concat($absences);

            Log::info('Todos os eventos mesclados', ['allEvents' => $allEvents]);

            return response()->json($allEvents);
        }

        return view('fullcalender');
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

    public function ajax(Request $request)
    {
        $userId = Auth::id();

        switch ($request->type) {
            case 'add':
                $event = new Event([
                    'user_id' => $userId,
                    'title' => $request->title,
                    'start' => $request->start,
                    'end' => $request->end,
                ]);

                $event->save();

                return response()->json($event);

                break;

            case 'update':
                $event = Event::where('user_id', $userId)
                    ->find($request->id)
                    ->update([
                        'title' => $request->title,
                        'start' => $request->start,
                        'end' => $request->end,
                    ]);

                return response()->json($event);
                break;

            case 'delete':
                $event = Event::where('user_id', $userId)
                    ->find($request->id)
                    ->delete();

                return response()->json($event);
                break;

            default:
                return response()->json(['error' => 'Tipo de ação inválida'], 400);
        }
    }
}
