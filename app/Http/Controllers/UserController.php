<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Presence;
use App\Models\User;
use App\Models\Role;
use App\Models\User_Shift;
use App\Models\Vacation;
use App\Models\Work_Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

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


    // TENTATIVA METODO STORE 100 % ao clickar
    /* public function store(Request $request)
    {
        // GUARDA REGISTRO POR REGISTRO AO CLICKAR NO BOTAO - 100% OQUE FOR MAIS DE 8 HORAS EFETIVAS PASSA A HORA EXTRA

        $user = auth()->user();
        $userShift = User_Shift::where('user_id', $user->id)->first();
        $workShift = Work_Shift::find($userShift->work_shift_id);
        $presence = Presence::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->first();

        if (!$presence) {
            $presence = new Presence;
            $presence->user_id = $user->id;
            $presence->first_start = now();
        } elseif (!$presence->first_end) {
            $presence->first_end = now();
        } elseif (!$presence->second_start) {
            $presence->second_start = now();
        } else {
            $presence->second_end = now();

            $first_start = Carbon::parse($presence->first_start);
            $first_end = Carbon::parse($presence->first_end);
            $second_start = Carbon::parse($presence->second_start);
            $second_end = Carbon::parse($presence->second_end);

            $totalMinutes = $first_end->diffInMinutes($first_start) + $second_end->diffInMinutes($second_start);

            $workShiftStart = Carbon::parse($workShift->start_hour);
            $workShiftEnd = Carbon::parse($workShift->end_hour);
            $workShiftMinutes = $workShiftEnd->diffInMinutes($workShiftStart);

            // Defina um limite para a hora efetiva (8 horas convertidas em minutos)
            $effectiveHourLimit = 8 * 60;

            // Se o total de minutos for maior que o total de minutos do turno de trabalho ou o limite da hora efetiva,
            // registre os minutos do turno de trabalho ou o limite da hora efetiva como minutos efetivos e o restante como minutos extras
            if ($totalMinutes > $workShiftMinutes || $totalMinutes > $effectiveHourLimit) {
                $presence->effective_hour = min($workShiftMinutes, $effectiveHourLimit) / 60; // Converta para horas
                $presence->extra_hour = ($totalMinutes - min($workShiftMinutes, $effectiveHourLimit)) / 60; // Converter para horas
            } else {
                // Se o total de minutos for menor ou igual ao total de minutos do turno de trabalho e ao limite da hora efetiva,
                // registre todos os minutos como minutos efetivos
                $presence->effective_hour = $totalMinutes / 60; // Converter para horas
                $presence->extra_hour = 0;
            }
        }

        $presence->save();

        return redirect()->to(url('user/presence'));
    } */

    // GUARDA REGISTRO POR REGISTRO AO CLICKAR NO BOTAO - 100% , nao importa as horas efetivas compara com a saida do turno para definir horas extras


    // TENTATIVA METODO STORE2 90%
    /*  public function store(Request $request)
     {
        // GUARDA REGISTRO POR REGISTRO AO CLICKAR NO BOTAO - 90% OQUE FOR MAIS DE 8 HORAS EFETIVAS PASSA A HORA EXTRA
         $user = auth()->user();
         $userShift = User_Shift::where('user_id', $user->id)->first();
         $workShift = Work_Shift::find($userShift->work_shift_id);
         $presence = Presence::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->first();

         if (!$presence) {
             $presence = new Presence;
             $presence->user_id = $user->id;
             $presence->first_start = now();
         } elseif (!$presence->first_end) {
             $presence->first_end = now();
         } elseif (!$presence->second_start) {
             $presence->second_start = now();
         } else {
             $existingPresence = Presence::where('user_id', $user->id)->where('second_end', '!=', null)->whereDate('created_at', Carbon::today())->first();

             if ($existingPresence) {
                 // Se existe um registro completo (2 entradas, 2 saidas), redirecione o user de volta com uma mensagem de erro
                 return redirect()->to(url('user/presence'))->with('error', 'Já existe um registro de presença completo para hoje.');
             }

             $presence->second_end = now();

             $first_start = Carbon::parse($presence->first_start);
             $first_end = Carbon::parse($presence->first_end);
             $second_start = Carbon::parse($presence->second_start);
             $second_end = Carbon::parse($presence->second_end);

             $totalMinutes = $first_end->diffInMinutes($first_start) + $second_end->diffInMinutes($second_start);

             $workShiftStart = Carbon::parse($workShift->start_hour);
             $workShiftEnd = Carbon::parse($workShift->end_hour);
             $workShiftMinutes = $workShiftEnd->diffInMinutes($workShiftStart);

             // DEFINE limite as horas efetiva
             $effectiveHourLimit = 8 * 60;

             // Se o total de minutos for maior que o total de minutos do turno de trabalho ou o limite da hora efetiva,
             // registre os minutos do turno de trabalho ou o limite da hora efetiva como minutos efetivos e o restante como minutos extras
             if ($totalMinutes > $workShiftMinutes || $totalMinutes > $effectiveHourLimit) {
                 $presence->effective_hour = min($workShiftMinutes, $effectiveHourLimit) / 60; // Converter para horas
                 $presence->extra_hour = ($totalMinutes - min($workShiftMinutes, $effectiveHourLimit)) / 60; // Converter para horas
             } else {
                 // Se o total de minutos for menor ou igual ao total de minutos do turno de trabalho e ao limite da hora efetiva,
                 // registre todos os minutos como minutos efetivos
                 $presence->effective_hour = $totalMinutes / 60; // Converter para horas
                 $presence->extra_hour = 0;
             }

             // Se o user começou a trabalhar depois do inicio do turno, ajuste as horas efetivas e extras
             if ($first_start->greaterThan($workShiftStart)) {
                 $lateStartMinutes = $first_start->diffInMinutes($workShiftStart);
                 $lateStartHours = $lateStartMinutes / 60;
                 if ($presence->effective_hour > $lateStartHours) {
                     $presence->effective_hour -= $lateStartHours; // SUBTRAIR as horas que o funcionário chegou tarde
                     $presence->extra_hour += $lateStartHours; // ADICIONAR essas horas às horas extras
                 } else {
                     $presence->extra_hour += $presence->effective_hour;
                     $presence->effective_hour = max(0, $presence->effective_hour - $lateStartHours); // GARANTIR que as horas efetivas não sejam negativas
                 }
             }
         }

         $presence->save();

         return redirect()->to(url('user/presence'));
     } */

    public function store(Request $request)
    {

        // GUARDA REGISTRO POR REGISTRO AO CLICKAR NO BOTAO + aviso - 95% hora extra -> CONFORME o horario do turno
        $user = auth()->user();
        $userShift = User_Shift::where('user_id', $user->id)->first();
        $workShift = Work_Shift::find($userShift->work_shift_id);
        $presence = Presence::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->first();

        if (!$presence) {
            $presence = new Presence;
            $presence->user_id = $user->id;
            $presence->first_start = now();
        } elseif (!$presence->first_end) {
            $presence->first_end = now();
        } elseif (!$presence->second_start) {
            $presence->second_start = now();
        } else {
            $existingPresence = Presence::where('user_id', $user->id)->where('second_end', '!=', null)->whereDate('created_at', Carbon::today())->first();

            if ($existingPresence) {
                return redirect()->to(url('user/presence'))->with('error', 'Já existe um registro de presença completo para hoje.');
            }

            $presence->second_end = now();

            $first_start = Carbon::parse($presence->first_start);
            $first_end = Carbon::parse($presence->first_end);
            $second_start = Carbon::parse($presence->second_start);
            $second_end = Carbon::parse($presence->second_end);

            $totalMinutes = $first_end->diffInMinutes($first_start) + $second_end->diffInMinutes($second_start);

            $workShiftStart = Carbon::parse($workShift->start_hour);
            $workShiftEnd = Carbon::parse($workShift->end_hour);
            $workShiftMinutes = $workShiftEnd->diffInMinutes($workShiftStart);

            $effectiveHourLimit = 8 * 60;

            if ($totalMinutes > $workShiftMinutes || $totalMinutes > $effectiveHourLimit) {
                $presence->effective_hour = min($workShiftMinutes, $effectiveHourLimit) / 60;
                $presence->extra_hour = ($totalMinutes - min($workShiftMinutes, $effectiveHourLimit)) / 60;
            } else {
                $presence->effective_hour = $totalMinutes / 60;
                $presence->extra_hour = 0;
            }

            if ($first_start->greaterThan($workShiftStart)) {
                $lateStartMinutes = $first_start->diffInMinutes($workShiftStart);
                $lateStartHours = $lateStartMinutes / 60;
                if ($presence->effective_hour > $lateStartHours) {
                    $presence->effective_hour -= $lateStartHours;
                    $presence->extra_hour += $lateStartHours;
                } else {
                    $presence->extra_hour += $presence->effective_hour;
                    $presence->effective_hour = max(0, $presence->effective_hour - $lateStartHours);
                }
            } else if ($first_start->lessThan($workShiftEnd)) {
                $earlyStartMinutes = $workShiftEnd->diffInMinutes($first_start);
                $earlyStartHours = $earlyStartMinutes / 60;
                if ($presence->effective_hour > $earlyStartHours) {
                    $presence->effective_hour += $earlyStartHours;
                    $presence->extra_hour -= $earlyStartHours;
                } else {
                    $presence->extra_hour -= $presence->effective_hour;
                    $presence->effective_hour = min($effectiveHourLimit, $presence->effective_hour + $earlyStartHours);
                }
            }
        }

        $presence->save();

        return redirect()->to(url('user/presence'));
    }




    public function storeSimulated(Request $request)
    {
        // SIMULA HORA FICTICIA - 100% , nao importa as horas efetivas compara com a saida do turno para definir horas extras

        $user = auth()->user();
        $userShift = User_Shift::where('user_id', $user->id)->first();
        $workShift = Work_Shift::find($userShift->work_shift_id);

        // VERIFICA se existe um registro de presença para user hoje
        $existingPresence = Presence::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if ($existingPresence) {
            // SE EXISTIR um registro redirecione o user de volta com uma mensagem de erro
            return redirect()->to(url('user/presence'))->with('error', 'Já existe um registro de presença para hoje.');
        }

        // CRIA um objeto Presence
        $presence = new Presence;
        $presence->user_id = $user->id;

        // SIMULA hora com pre-definida no formulario
        $presence->first_start = Carbon::parse($request->first_start);
        $presence->first_end = Carbon::parse($request->first_end);
        $presence->second_start = Carbon::parse($request->second_start);
        $presence->second_end = Carbon::parse($request->second_end);

        // CONVERTE as strings para objetos de data/hora
        $first_start = Carbon::parse($presence->first_start);
        $first_end = Carbon::parse($presence->first_end);
        $second_start = Carbon::parse($presence->second_start);
        $second_end = Carbon::parse($presence->second_end);

        // CALCULA total de minutos trabalhados
        $totalMinutes = $first_end->diffInMinutes($first_start) + $second_end->diffInMinutes($second_start);

        // TOTAL de minutos do turno de trabalho
        $workShiftStart = Carbon::parse($workShift->start_hour);
        $workShiftEnd = Carbon::parse($workShift->end_hour);
        $workShiftMinutes = $workShiftEnd->diffInMinutes($workShiftStart);

        // LIMITE para a hora efetiva em minutos
        $effectiveHourLimit = 8 * 60;

        // Se o total de minutos for maior que o total de minutos do turno de trabalho ou o limite da hora efetiva,
        // registre os minutos do turno de trabalho ou o limite da hora efetiva como minutos efetivos e o restante como minutos extras
        if ($totalMinutes > $workShiftMinutes || $totalMinutes > $effectiveHourLimit) {
            $presence->effective_hour = min($workShiftMinutes, $effectiveHourLimit) / 60; // Converter para horas
            $presence->extra_hour = ($totalMinutes - min($workShiftMinutes, $effectiveHourLimit)) / 60; // Converter para horas

        } else {
            // Se o total de minutos for menor ou igual ao total de minutos do turno de trabalho e ao limite da hora efetiva,
            // registra todos os minutos como minutos efetivos
            $presence->effective_hour = $totalMinutes / 60; // Converter para horas
            $presence->extra_hour = 0;
        }

        // Se o user começou a trabalhar depois do inicio do turno, ajusta as horas efetivas e extras
        if ($first_start->greaterThan($workShiftStart)) {
            $lateStartMinutes = $first_start->diffInMinutes($workShiftStart);
            $presence->effective_hour -= $lateStartMinutes / 60; // SUBTRAIR as horas que o user chegou tarde
            $presence->extra_hour += $lateStartMinutes / 60; // ADICIONA essas horas extras
        }

        $presence->save();

        return redirect()->to(url('user/presence'));
    }


    /* public function storeSimulated(Request $request)
    {
        // METODO PARA SIMULAR ENTRA - SAIDA --> 100%, conta oque passar das 8 horas efetivas = hora extra

        $user = auth()->user();
        $userShift = User_Shift::where('user_id', $user->id)->first();
        $workShift = Work_Shift::find($userShift->work_shift_id);

        $existingPresence = Presence::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if ($existingPresence) {
            return redirect()->to(url('user/presence'))->with('error', 'Já existe um registro de presença para hoje.');
        }


        $presence = new Presence;
        $presence->user_id = $user->id;

        $presence->first_start = Carbon::parse($request->first_start);
        $presence->first_end = Carbon::parse($request->first_end);
        $presence->second_start = Carbon::parse($request->second_start);
        $presence->second_end = Carbon::parse($request->second_end);

        $first_start = Carbon::parse($presence->first_start);
        $first_end = Carbon::parse($presence->first_end);
        $second_start = Carbon::parse($presence->second_start);
        $second_end = Carbon::parse($presence->second_end);

        $totalMinutes = $first_end->diffInMinutes($first_start) + $second_end->diffInMinutes($second_start);

        $workShiftStart = Carbon::parse($workShift->start_hour);
        $workShiftEnd = Carbon::parse($workShift->end_hour);
        $workShiftMinutes = $workShiftEnd->diffInMinutes($workShiftStart);

        $effectiveHourLimit = 8 * 60;

        if ($totalMinutes > $workShiftMinutes || $totalMinutes > $effectiveHourLimit) {
            $presence->effective_hour = min($workShiftMinutes, $effectiveHourLimit) / 60;
            $presence->extra_hour = ($totalMinutes - min($workShiftMinutes, $effectiveHourLimit)) / 60;
        } else {

            $presence->effective_hour = $totalMinutes / 60;
            $presence->extra_hour = 0;
        }

        $presence->save();

        return redirect()->to(url('user/presence'));
    } */








    /*  public function store(Request $request)
    {
         // METODO PARA HORA EXTRA E HORA EFETIVA +8 HORAS POR DIA -- 100% FUNCIONAL
         $user = auth()->user();
         $presence = Presence::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->first();

         if (!$presence) {
             $presence = new Presence;
             $presence->user_id = $user->id;
             $presence->first_start = now();
         } elseif (!$presence->first_end) {
             $presence->first_end = now();
         } elseif (!$presence->second_start) {
             $presence->second_start = now();
         } else {
             $presence->second_end = now();

             // Converta as strings para objetos de data/hora
             $first_start = Carbon::parse($presence->first_start);
             $first_end = Carbon::parse($presence->first_end);
             $second_start = Carbon::parse($presence->second_start);
             $second_end = Carbon::parse($presence->second_end);

             // Calcule o total de minutos trabalhados
             $totalMinutes = $first_end->diffInMinutes($first_start) + $second_end->diffInMinutes($second_start);

             // Se o total de minutos for maior que 480 (8 horas), registre 480 minutos como minutos efetivos e o restante como minutos extras
             if ($totalMinutes > 480) {
                 $presence->effective_hour = 480 / 60; // Converta para horas
                 $presence->extra_hour = ($totalMinutes - 480) / 60; // Converter para horas
             } else {
                 // Se o total de minutos for menor ou igual a 480, registre todos os minutos como minutos efetivos
                 $presence->effective_hour = $totalMinutes / 60; // Converter para horas
                 $presence->extra_hour = 0;
             }
         }

         $presence->save();

         return redirect()->to(url('user/presence'));
     } */








    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = auth()->user();
        $user_shift = User_Shift::where('user_id', $user->id)->first();
        return view('pages.users.show', ['user' => $user, 'user_shift' => $user_shift]);
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
        $user_shift->work_shift_id = $request->input('work_shift_id');
        $user_shift->save();

        return redirect('/user/show');
    }

    public function presence(Request $request)
    {
        $user = auth()->user();
        $presence = Presence::where('user_id', $user->id)->first();

        return view('pages.users.presence', ['user' => $user, 'presence' => $presence]);
    }

    public function getPresence()
    {
        $user = auth()->user();
        $presence = Presence::where('user_id', $user->id)->first();

        return view('pages.users.presence', ['user' => $user, 'presence' => $presence]);
    }






    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $user)
    {
        $user->softDelete();
        return redirect('users')->with('status', 'User deleted successfully!');
    }


    public function import(Request $request)
    {
    $file = $request->file('file');

    if(!$file) {
        return redirect()->back()->with('error', 'Please choose a file before importing.');
    }

    $handle = fopen($file->getPathname(), 'r');

    
    // Ignorar a primeira linha (cabeçalhos)
    fgets($handle);

    Schema::disableForeignKeyConstraints();

    // Trunca as tabelas
    DB::table('users')->truncate();
    DB::table('presences')->truncate();
    DB::table('absences')->truncate();
    DB::table('vacations')->truncate();

    // Reabilita as verificações de chave estrangeira
    Schema::enableForeignKeyConstraints();



    while (($line = fgets($handle)) !== false) {
        $data = str_getcsv($line);

        User::create([
            'role_id' => $data[0],
            'name' => $data[1],
            'address' => $data[2],
            'nif' => $data[3],
            'tel' => $data[4],
            'birth_date' => $data[5],
            'email' => $data[6],
            'password' => $data[7],  
        ]);
    }

    fclose($handle);

    return redirect()->back()->with('success', 'CSV file imported successfully.');
    }

    public function export(){
        $users = User::all();
        $csvFileName = 'users.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Role_id','Name', 'Address','Nif','Tel','Birth_date','Email','Password']); // Add more headers as needed

        foreach ($users as $user) {
            fputcsv($handle, [$user->role_id,$user->name, $user->address,$user->nif,$user->tel,$user->birth_date,$user->email,$user->password]); // Add more fields as needed
        }

        fclose($handle);

        return Response::make('', 200, $headers);
    }


    public function insertUser($data)
    {
        $user = new User();
        $user->role_id = $data['role_id'];
        $user->name = $data['name'];
        $user->address = $data['address'];
        $user->nif = $data['nif'];
        $user->tel = $data['tel'];
        $user->birth_date = $data['birth_date'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->created_at = $data['created_at'];
        $user->updated_at = $data['updated_at'];
        $user->deleted_at = $data['deleted_at'];

        $user->save();
    }
}
