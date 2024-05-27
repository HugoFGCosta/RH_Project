<?php

namespace App\Http\Controllers;

use App\Exports\VacationsExport;
use App\Imports\VacationsImport;
use App\Models\Vacation;
use App\Http\Requests\StoreVacationRequest;
use App\Http\Requests\UpdateVacationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades;
class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {//dont 4get to change to user info only and remeb to make admin see all

        $vacation = vacation::with('user')->orderBy('id', 'asc')->paginate(3);
        return view('pages.vacations.show', ['vacations' => $vacation]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view ('pages.vacations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVacationRequest $request)
    {//2 role can also do this but shouldn't be able to confirm his own vacation

        $request->validate([
            'date_start' => 'required|after:tomorrow' ,
            'date_end' => 'required|after:tomorrow'
        ]);
        $vacation = new Vacation();
        $vacation->user_id = Auth::id();
        $vacation->vacation_approval_states_id = 3;
        $vacation->approved_by = null;
        $vacation->date_start =$request->date_start ;
        $vacation->date_end = $request->date_end ;
        $vacation->save();
        return redirect(url('/vacation'))->with('status','Item created successfully!');


    }

    /**
     * Display the specified resource.
     */
    public function show(Vacation $vacation)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacation $vacation)
    {
        return view('pages.vacations.edit', ['vacations' => $vacation]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVacationRequest $request, Vacation $vacation)
    {
        $vacation = vacation::find($vacation->id);
      //  if(find($vacation->approved_by))
        $vacation->approved_by = null;
        $vacation->vacation_approval_states_id = '3';
        $vacation->date_start = $request->date_start;
        $vacation->date_end = $request->date_end;


        $vacation->save();
        return redirect(url('/vacation'))->with('status','Item edited successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacation $vacation)
    {
        $vacation = vacation::find($vacation->id);
        $vacation->delete();
        return redirect('vacations')->with('status','Item deleted successfully!');
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
