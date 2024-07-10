<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Event;
use App\Models\Presence;
use App\Models\User_Shift;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ButtonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $presence = Presence::where('user_id', $user->id)->first();
        $events = Event::where('user_id', $user->id)->get();
        $notifications = Notification::with(['event', 'absence', 'vacation'])->where('state', false)->get();
        return view('pages.menu.menu', ['user' => $user, 'presence' => $presence, 'events' => $events, 'notifications' => $notifications]);
    }


    // Metodo registerSchedule- serve para direcionar o utilizador para a pagina de criação de novo turno
    public function registerSchedule()
    {
        return view('pages.register-schedule.register-schedule');
    }

    // Metodo dashboardStatistics- serve para direcionar o utilizador para a pagina de criação de novo turno
    public function dashboardStatistics()
    {
        return view('pages.dashboard-stats.dashboard-stats');
    }

    // Metodo viewAbsences- serve para direcionar o utilizador para a pagina de criação de novo turno
    public function viewAbsences()
    {
        return view('pages.check-absence.check-absence');
    }

    // Metodo manageData- serve para direcionar o utilizador para a pagina de criação de novo turno
    public function manageData()
    {
        return view('pages.manage-data.manage-data');
    }

    // Metodo vacationPlans- serve para direcionar o utilizador para a pagina de criação de novo turno
    public function vacationPlans()
    {
        return view('pages.vacations.show');
    }

    // Metodo approveAbsences- serve para direcionar o utilizador para a pagina de listagem de faltas aprovadas
    public function approveAbsences()
    {
        $absences = Absence::all();

        return view('pages.approve-absence.approve-absence', ['absences' => $absences]);
    }

    // Metodo importExportData- serve para direcionar o utilizador para a pagina exportação de importação de dados
    public function importExportData()
    {
        return view('pages.import-export-data.import-export-data');
    }

    // Metodo dailyTasks- serve para direcionar o utilizador para a página de tarefas diárias
    public function dailyTasks()
    {
        return view('pages.daily-tasks.daily-tasks');
    }

    // Metodo requests- serve para direcionar o utilizador para a pagina de pedidos
    public function requests()
    {
        return view('pages.requests.requests');
    }

    // Metodo settings- serve para direcionar o utilizador para a pagina de definições
    public function settings()
    {
        return view('pages.settings.settings');
    }



    public function attendanceRecord()
    {
        $user = auth()->user();
        $presences = Presence::all()->where('user_id', $user->id);
        $user_shifts = User_Shift::all()->where('user_id', $user->id);

        return view('pages.attendance-record.attendance-record', ['user' => $user, 'presences' => $presences, 'user_shifts' => $user_shifts]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
