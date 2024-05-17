<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\User_Shift;
use App\Models\Work_Shift;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Validator;

class AdminRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showRegisterForm()
    {
        $work_shifts = Work_Shift::all();
        $roles = Role::all();
        $users = User::all();
        return view('pages.users.create', ['users' => $users, 'work_shifts' => $work_shifts, 'roles' => $roles]);
    }

    /* public function create(Request $request)
    {

        // se nao existir registro o primeiro pode ser feito estar logado como admin
        if (User::count() > 0 && (!Auth::user() || !Auth::user()->isAdmin())) {
            abort(403, 'Apenas administradores podem criar novos usuários.');
        }


        // Validação dos dados -- formato data ANO(2004)-MES(02)-DIA(21)
        Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role_id' => 'required',
            'address' => 'required',
            'nif' => 'required|digits:9',
            'tel' => 'required|digits:9|unique:users,tel',
            'birth_date' => ['required', 'date', 'before:-18 years'],
            'work_shift_id' => 'required',
        ])->validate();

        // Criação do usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'address' => $request->address,
            'nif' => $request->nif,
            'tel' => $request->tel,
            'birth_date' => $request->birth_date
        ]);

        // Criação do turno do usuário
        $user_shift = new User_Shift();
        $user_shift->work_shift_id = $request->work_shift_id;
        $user_shift->user_id = $user->id;
        $user_shift->start_date = now();
        $user_shift->save();

        return redirect('/menu');
    } */


    public function create(Request $request)
    {
        // Se não existir nenhum registro, permita a criação do primeiro usuário
        if (User::count() == 0) {
            // Validação dos dados para o primeiro usuário
            Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'role_id' => 'required',
                'address' => 'required',
                'nif' => 'required|digits:9',
                'tel' => 'required|digits:9|unique:users,tel',
                'birth_date' => ['required', 'date', 'before:-18 years'],
                'work_shift_id' => 'required',
            ])->validate();

            // Criação do primeiro usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'address' => $request->address,
                'nif' => $request->nif,
                'tel' => $request->tel,
                'birth_date' => $request->birth_date,
                // Defina o usuário como administrador
                'is_admin' => true,
            ]);

            // Criação do turno do usuário
            $user_shift = new User_Shift();
            $user_shift->work_shift_id = $request->work_shift_id;
            $user_shift->user_id = $user->id;
            $user_shift->start_date = now();
            $user_shift->save();

            return redirect('/menu');
        }

        // Se já existir pelo menos um usuário, verifique se o usuário atual é um administrador
        if (Auth::check() && Auth::user()->isAdmin()) {
            // Validação dos dados para um administrador criar um novo usuário
            Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'role_id' => 'required',
                'address' => 'required',
                'nif' => 'required|digits:9',
                'tel' => 'required|digits:9|unique:users,tel',
                'birth_date' => ['required', 'date', 'before:-18 years'],
                'work_shift_id' => 'required',
            ])->validate();

            // Criação de um novo usuário por um administrador
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'address' => $request->address,
                'nif' => $request->nif,
                'tel' => $request->tel,
                'birth_date' => $request->birth_date,
            ]);

            // Criação do turno do usuário
            $user_shift = new User_Shift();
            $user_shift->work_shift_id = $request->work_shift_id;
            $user_shift->user_id = $user->id;
            $user_shift->start_date = now();
            $user_shift->save();

            return redirect('/menu');
        }

        // Se não for um administrador, aborte com um erro 403
        abort(403, 'Apenas administradores podem criar novos usuários.');
    }


}