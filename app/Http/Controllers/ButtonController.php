<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Presence;
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
        return view('pages.menu.menu', ['user' => $user,'presence' => $presence]);
    }


    public function registerSchedule()
    {
        return view('pages.register-schedule.register-schedule');
    }

    public function dashboardStatistics()
    {
        return view('pages.dashboard-stats.dashboard-stats');
    }

    public function viewAbsences()
    {
        return view('pages.check-absence.check-absence');
    }

    public function manageData()
    {
        return view('pages.manage-data.manage-data');
    }

    public function vacationPlans()
    {
        return view('pages.schedule-vacations.schedule-vacations');
    }

    public function approveAbsences()
    {
        $absences = Absence::all();

        return view('pages.approve-absence.approve-absence', ['absences' => $absences]);
    }

    public function importExportData(){
        return view('pages.import-export-data.import-export-data');
    }

    public function dailyTasks(){
        return view('pages.daily-tasks.daily-tasks');
    }

    public function requests(){
        return view('pages.requests.requests');
    }

    public function settings(){
        return view('pages.settings.settings');
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
