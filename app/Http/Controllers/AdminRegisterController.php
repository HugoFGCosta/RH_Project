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

    public function create(Request $request)
    {


        $messages = [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já se encontra registado.',
            'password.min' => 'A senha deve conter pelo menos 8 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos 1 letra maiuscula, 1 número e caracter especial.',
            'role_id.required' => 'O cargo é obrigatório.',
            'address.required' => 'O endereço é obrigatório.',
            'nif.required' => 'O NIF é obrigatório.',
            'nif.digits' => 'O NIF deve conter 9 dígitos.',
            'tel.required' => 'O telemóvel é obrigatório.',
            'tel.digits' => 'O telemóvel deve conter 9 digitos.',
            'tel.unique' => 'Este telemóvel já se encontra registado.',
            'birth_date.required' => 'A data de aniversário é obrigatório.',
            'birth_date.before' => 'Necessita de ter mais de 18anos para se registar.',
            'work_shift_id.required' => 'O turno é obrigatório.',
        ];



        // Se não existir nenhum registro, permita a criação do primeiro utilizador
        if (User::count() == 0) {
            // Validação dos dados para o primeiro utilizador
            Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    'min:8',
                    'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                    'confirmed'
                ],
                'role_id' => 'required',
                'address' => 'required',
                'nif' => 'required|digits:9',
                'tel' => 'required|digits:9|unique:users,tel',
                'birth_date' => ['required', 'date', 'before:-18 years'],
                'work_shift_id' => 'required',
            ], $messages)->validate();

            // Criação do primeiro utilizador
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'address' => $request->address,
                'nif' => $request->nif,
                'tel' => $request->tel,
                'birth_date' => $request->birth_date,
                // Defina o utilizador como administrador
                'is_admin' => true,
            ]);

            // Criação do turno do utilizador
            $user_shift = new User_Shift();
            $user_shift->work_shift_id = $request->work_shift_id;
            $user_shift->user_id = $user->id;
            $user_shift->start_date = now();
            $user_shift->save();

            return redirect('/menu');
        }

        // Se já existir pelo menos um utilizador, verifique se o utilizador atual é um administrador
        if (Auth::check() && Auth::user()->isAdmin()) {
            // Validação dos dados para um administrador criar um novo utilizador
            Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    'min:8',
                    'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                    'confirmed'
                ],
                'role_id' => 'required',
                'address' => 'required',
                'nif' => 'required|digits:9',
                'tel' => 'required|digits:9|unique:users,tel',
                'birth_date' => ['required', 'date', 'before:-18 years'],
                'work_shift_id' => 'required',
            ], $messages)->validate();

            // Criação de um novo utilizador por um administrador
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

            // Criação do turno do utilizador
            $user_shift = new User_Shift();
            $user_shift->work_shift_id = $request->work_shift_id;
            $user_shift->user_id = $user->id;
            $user_shift->start_date = now();
            $user_shift->save();

            return redirect('/menu');
        }

        // Se não for um administrador, aborte com um erro 403
        abort(403, 'Apenas administradores podem criar novos utilizadores.');
    }


}