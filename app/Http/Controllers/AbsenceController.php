<?php

namespace App\Http\Controllers;

use App\Exports\AbsencesExport;
use App\Imports\AbsencesImport;
use App\Models\Absence;
use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Requests\UpdateAbsenceRequest;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class AbsenceController extends Controller
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
    public function store(StoreAbsenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Absence $absence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Absence $absence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAbsenceRequest $request, Absence $absence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absence $absence)
    {
        //
    }

    public function import()
    {
        try {
            //verifica se o ficheiro foi submetido no formulário
            if (request()->has('file')) {  //se sim apaga os dados da tabela
                DB::table('absences')->delete();
            }

            //importa os dados do ficheiro para a tabela
            Excel::import(new AbsencesImport(), request()->file('file'));
            return redirect('/import-export-data')->with('success', 'Faltas importadas com sucesso!');

        } catch (\Exception $e) {
            return redirect('/import-export-data')->with('error', 'Error during import: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new AbsencesExport(), 'absences.xlsx');
    }
}
