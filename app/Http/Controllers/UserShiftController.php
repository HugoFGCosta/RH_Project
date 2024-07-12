<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Work_Shift;
use Illuminate\Http\Request;
use App\Models\User_Shift;
use App\Http\Requests\StoreUser_ShiftRequest;
use App\Http\Requests\UpdateUser_ShiftRequest;




class UserShiftController extends Controller
{
    public function index()
    {
        //
    }


    public function create()
    {
        //
    }

    public function store(StoreUser_shiftRequest $request)
    {
        //
    }


    // Metodo show - mostra todos os turnos disponiveis
    public function show(User_Shift $user_shift)
    {
        $users_shifts = User_Shift::all();

        return view('pages.user-shifts.show-all', ['users_shifts' => $users_shifts]);
    }

    // Metodo show_spec - mostra um turno especifico de um user
    public function show_spec(User_Shift $user_shift)
    {
        $user = auth()->user();
        $user_shifts = User_Shift::all()->where('user_id', $user->id);

        return view('pages.user-shifts.show-spec', ['user_shifts' => $user_shifts]);
    }


    // Metodo edit - edita o turno do user com as informaçoes recebida pelo work_shift.
    public function edit($id)
    {
        $work_shifts = Work_Shift::all();
        $user_shifts = User_Shift::findOrFail($id);
        return view('pages.user-shifts.edit-spec', ['user_shifts' => $user_shifts, 'work_shifts' => $work_shifts]);
    }

    // Metodo update - Atualiza as informaçoes do turno que do user.
    public function update(Request $request, User_Shift $user_shift)
    {
        // Verifique se o turno de trabalho existe no request
        if ($request->has('work_shift_id')) {
            // Busque o último UserShift do utilizador
            $last_user_shift = User_Shift::where('user_id', $user_shift->user_id)->latest()->first();

            // Se existir um último UserShift, finalize-o
            if ($last_user_shift) {
                $last_user_shift->end_date = now();
                $last_user_shift->save();
            }

            // Inicie um novo turno de trabalho
            User_Shift::create([
                'user_id' => $user_shift->user_id,
                'work_shift_id' => $request->input('work_shift_id'),
                'start_date' => now(),
                'end_date' => null,
            ]);
        }

        return redirect('/menu');
    }


    // Metodo destroy - Apaga as informaçoes do turno do user
    public function destroy(User_Shift $user_shift)
    {
        $user_shift->delete();

        return redirect('/menu');
    }
}
