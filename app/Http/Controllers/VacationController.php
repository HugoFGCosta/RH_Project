<?php

namespace App\Http\Controllers;

use App\Exports\VacationsExport;
use App\Imports\VacationsImport;
use App\Models\Vacation;
use App\Http\Requests\StoreVacationRequest;
use App\Http\Requests\UpdateVacationRequest;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class VacationController extends Controller
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
    public function store(StoreVacationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Vacation $vacation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacation $vacation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVacationRequest $request, Vacation $vacation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacation $vacation)
    {
        //
    }

    public function import()
    {
        try {

            //verifica se o ficheiro foi submetido no formulário
            if (request()->has('file')) {  //se sim apaga os dados da tabela
                DB::table('vacations')->delete();
            }

            //importa os dados do ficheiro
            Excel::import(new VacationsImport(), request()->file('file'));
            return redirect('/import-export-data')->with('success', 'Férias importadas com sucesso!');

        } catch (\Exception $e) {
            return redirect('/import-export-data')->with('error', 'Error during import: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new VacationsExport(), 'vacations.xlsx');
    }
}
