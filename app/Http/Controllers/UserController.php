<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\User_Shift;
use App\Models\Work_Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*  $players = User::orderBy('id', 'asc')->paginate(10);
         return view('pages.players.index', ['players' => $players]); */

        $users = User::orderBy('id', 'desc')->get();
        return view('pages.users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $work_shifts = Work_Shift::all();
        $roles = Role::all();
        $users = User::all();
        return view('pages.users.create', ['users' => $users, 'work_shifts' => $work_shifts, 'roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role_id' => 'required',
            'address' => 'required',
            'nif' => 'required',
            'tel' => 'required',
            'birth_date' => 'required',
            'work_shift_id' => 'required',

        ]);



        $user = new User();
        $user->name = $request->name;
        $user->address = $request->address;
        $user->nif = $request->nif;
        $user->tel = $request->tel;
        $user->role_id = $request->role_id;
        $user->birth_date = $request->birth_date;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        $user_shift = new User_Shift();
        $user_shift->work_shift_id = $request->work_shift_id;
        $user_shift->user_id = $user->id;
        $user_shift->start_date = now();
        $user_shift->save();
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = auth()->user();
        return view('pages.users.show', ['user' => $user]);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {

        $work_shifts = Work_Shift::all();
        $roles = Role::all();

        $user = auth()->user();
        $user_shift = User_Shift::where('user_id', $user->id)->first();
        return view('pages.users.edit', ['user' => $user, 'user_shift' => $user_shift, 'work_shifts' => $work_shifts, 'roles' => $roles]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $user = auth()->user();
        $user->name = $request->input('name');
        $user->save();
        /*  $user->address = $request->input('address');
         $user->nif = $request->input('nif');
         $user->tel = $request->input('tel');
         $user->role_id = $request->input('role_id');
         $user->birth_date = $request->input('birth_date');
         $user->email = $request->input('email');
         $user->password = $request->input('password');

         $user_shift = User_Shift::where('user_id', $user->id)->first();
         $user_shift->work_shift_id = $request->input('work_shift_id');
         $user_shift->save(); */

        return redirect('/menu');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $user)
    {
        $user->softDelete();
        return redirect('users')->with('status', 'User deleted successfully!');
    }

    public function exportCSVAbsences() //exporta os dados dos utilizadores para um ficheiro CSV
    {
        $filename = 'user-data.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, [
                'id',
                'role_id',
                'name',
                'address',
                'nif',
                'tel',
                'birth_date',
                'email',
                'email_verified_at',
                'password',
                'rememberToken',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);

            // Fetch and process data in chunks
            User::chunk(25, function ($users) use ($handle) {
                foreach ($users as $user) {
                    // Extract data from each employee.
                    $data = [
                        isset($user->id)? $user->id : '',
                        isset($user->role_id)? $user->role_id : '',
                        isset($user->name)? $user->name : '',
                        isset($user->address)? $user->address : '',
                        isset($user->nif)? $user->nif : '',
                        isset($user->tel)? $user->tel : '',
                        isset($user->birth_date)? $user->birth_date : '',
                        isset($user->email)? $user->email : '',
                        isset($user->email_verified_at)? $user->email_verified_at : '',
                        isset($user->password)? $user->password : '',
                        isset($user->rememberToken)? $user->rememberToken : '',
                        isset($user->created_at)? $user->created_at : '',
                        isset($user->updated_at)? $user->updated_at : '',
                        isset($user->deleted_at)? $user->deleted_at : '',
                    ];

                    // Write data to a CSV file.
                    fputcsv($handle, $data);
                }
            });

            // Close CSV file handle
            fclose($handle);
        }, 200, $headers);
    }



}

