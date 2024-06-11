<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\User_Shift;
use App\Http\Requests\StoreUser_ShiftRequest;
use App\Http\Requests\UpdateUser_ShiftRequest;
use App\Models\Work_Shift;
use Illuminate\Http\Request;




class UserShiftController extends Controller
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
    public function store(StoreUser_shiftRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User_shift $user_shift)
    {
        $users_shifts = User_shift::all();

        return view('pages.user-shifts.show-all', ['users_shifts' => $users_shifts]);
    }
    public function show_spec(User_shift $user_shift)
    {

        $user = auth()->user();
        $user_shifts = User_Shift::all()->where('user_id', $user->id);

        return view('pages.user-shifts.show-spec', ['user_shifts' => $user_shifts]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $work_shifts = Work_Shift::all();
        $user_shifts = User_Shift::findOrFail($id);
        return view('pages.user-shifts.edit-spec', ['user_shifts' => $user_shifts, 'work_shifts' => $work_shifts]);
    }



    public function editSpec($id)
    {
        $work_shifts = Work_Shift::all();
        $user = auth()->user();
        return view('pages.user-shifts.edit', ['user' => $user, 'work_shifts' => $work_shifts]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User_Shift $user_shift)
    {
        // Verifique se o turno de trabalho existe no request
        if ($request->has('work_shift_id')) {
            // Busque o último UserShift do usuário
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






    public function updateSpec(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('/user/show')->with('error', 'Usuário não encontrado!');
        }

        $user->name = $request->input('name');




        $user->email = $request->input('email');
        if ($request->has('role')) {
            $role = Role::where('role', $request->input('role'))->first();
            if ($role) {
                $user->role_id = $role->id;
            }
        }
        $user->address = $request->input('address');
        $user->nif = $request->input('nif');
        $user->tel = $request->input('tel');
        $user->birth_date = $request->input('birth_date');
        $user->save();

        $user_shift = User_Shift::where('user_id', $user->id)->first();
        if ($user_shift) {
            $user_shift->work_shift_id = $request->input('work_shift_id');
            $user_shift->save();
        } else {
            // Handle the case where there is no corresponding User_Shift
        }

        return redirect('/users/show-all')->with('success', 'Especificações do usuário atualizadas com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User_Shift $user_shift)
    {
        $user_shift->delete();

        return redirect('/menu');
    }
}