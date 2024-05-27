<?php

namespace App\Http\Controllers;

use App\Exports\PresencesExport;
use App\Imports\PresencesImport;
use App\Models\Presence;
use App\Http\Requests\StorePresenceRequest;
use App\Http\Requests\UpdatePresenceRequest;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StorePresenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Presence $presence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presence $presence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePresenceRequest $request, Presence $presence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presence $presence)
    {
        //
    }

    public function import()
    {

        try {
            //verifica se o ficheiro foi submetido no formulário
            if (request()->has('file')) {  //se sim apaga os dados da tabela
                DB::table('presences')->delete();
            }

            //importa os dados do ficheiro para a tabela
            Excel::import(new PresencesImport(), request()->file('file'));
            return redirect('/import-export-data')->with('success', 'Presenças importadas com sucesso!');

        } catch (\Exception $e) {
            return redirect('/import-export-data')->with('error', 'Error during import: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new PresencesExport(), 'presences.xlsx');
    }
}
