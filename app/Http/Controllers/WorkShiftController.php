<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\User_Shift;
use App\Models\Work_shift;
use App\Http\Requests\StoreWork_shiftRequest;
use App\Http\Requests\UpdateWork_shiftRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class WorkShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        //
        $workShifts= Work_shift::orderBy('id','asc')->get();

        return view ('pages.work-shifts.index',['workShifts'=>$workShifts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view ('pages.work-shifts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    /*public function store(StoreWork_shiftRequest $request)
    {
        //
        $this->validate($request,[
          'start_hour'=>'required',
          'break_start'=>'required',
          'break_end'=>'required',
          'end_hour'=>'required'
            ]
        );

        Work_shift::create([
            'start_hour' => $request->start_hour,
            'break_start' => $request->break_start,
            'break_end' => $request->break_end,
            'end_hour' => $request->end_hour,

        ]);

        return redirect('work-shifts')->with('status', 'Turno criado com sucesso');
    }*/

    /**
     * Display the specified resource.
     */
    public function show(Work_shift $work_shift)
    {
        //
        return view ('pages.work-shifts.show', ['work_shift'=>$work_shift]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Work_shift $work_shift)
    {
        //
        return view ('pages.work-shifts.edit', ['workshift'=>$work_shift]);
    }

    /**
     * Update the specified resource in storage.
     */
    /*public function update(UpdateWork_shiftRequest $request, Work_shift $work_shift)
    {
        //
        $workShift = $work_shift;
        $workShift->start_hour = $request->input('start_hour');
        $workShift->break_start = $request->input('break_start');
        $workShift->break_end = $request->input('break_end');
        $workShift->end_hour = $request->input('end_hour');

        $workShift->save();

        return redirect('work-shifts')->with('status', 'Turno editado com sucesso');
    }*/

    public function store(Request $request)
    {
        //
        $this->validate($request,[
                'start_hour'=>'required',
                'break_start'=>'required',
                'break_end'=>'required',
                'end_hour'=>'required'
            ]
        );

        Work_shift::create([
            'start_hour' => $request->start_hour,
            'break_start' => $request->break_start,
            'break_end' => $request->break_end,
            'end_hour' => $request->end_hour,

        ]);

        return redirect('work-shifts')->with('success', 'Turno criado com sucesso');
    }

    public function update(Request $request, Work_shift $work_shift)
    {
        //
        $workShift = $work_shift;
        $workShift->start_hour = $request->input('start_hour');
        $workShift->break_start = $request->input('break_start');
        $workShift->break_end = $request->input('break_end');
        $workShift->end_hour = $request->input('end_hour');

        $workShift->save();

        return redirect('work-shifts')->with('success', 'Turno editado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Work_shift $work_shift)
    {
        //

        $user_shifts = User_Shift::where('work_shift_id', $work_shift->id)->get();
        foreach ($user_shifts as $user_shift) {
            $user_shift->end_date = Carbon::now();
            $user_shift->save();
        }

        $work_shift->delete();

        return redirect('work-shifts')->with('success', 'Turno apagado com sucesso');

    }

    public function export(){

        $work_shifts = Work_Shift::all();
        $csvFileName = 'work-shifts.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['HoraEntrada','HoraInicioIntervalo', 'HoraFimIntervalo','HoraSaida']); // Add more headers as needed

        foreach ($work_shifts as $work_shift) {
            fputcsv($handle, [$work_shift->start_hour,$work_shift->break_start, $work_shift->break_end,$work_shift->end_hour]); // Add more fields as needed
        }

        fclose($handle);

        return Response::make('', 200, $headers);
    }

    public function getUserWorkShift($userId){
        $users=User::all();
        $user_Shifts=User_Shift::all();

        //Percorre a lista de users e encontra o user que está logado
        foreach ($users as $user){
            if($user->id == $userId){
                $userFound = $user;
            }
        }

        //Vai buscar o horário atual deste utilizador
        $user_shift = User_Shift::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();

        return $user_shift;
    }

    public function exportUserWorkShift($userId){

        $user_Shift = User_Shift::where('user_id', $userId)->orderBy('created_at', 'desc')->whereNull('end_date')->first();

        if(!$user_Shift){
            return redirect()->back()->with('error', 'Este utilizador não tem um horário associado neste momento.');
        }

        $work_shift = Work_shift::find($user_Shift->work_shift_id);

        $csvFileName = 'user-work-shift.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['HoraEntrada','HoraInicioIntervalo', 'HoraFimIntervalo','HoraSaida']); // Add more headers as needed

        $weekDays = ['Segunda','Terça','Quarta','Quinta','Sábado','Domingo'];

        foreach ($weekDays as $day){
            fputcsv($handle, [$day,$work_shift->start_hour,$work_shift->break_start, $work_shift->break_end,$work_shift->end_hour]); // Add more fields as needed
        }

        fclose($handle);

        return Response::make('', 200, $headers);
    }

}
