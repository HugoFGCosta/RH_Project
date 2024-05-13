<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User_Shift;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Validação dos dados -- formato data ANO(2004)-MES(02)-DIA(21)
        Validator::make($data, [
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
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'address' => $data['address'],
            'nif' => $data['nif'],
            'tel' => $data['tel'],
            'birth_date' => $data['birth_date']
        ]);

        // Criação do turno do usuário
        $user_shift = new User_Shift();
        $user_shift->work_shift_id = $data['work_shift_id'];
        $user_shift->user_id = $user->id;
        $user_shift->start_date = now();
        $user_shift->save();

        return $user;
    }


}