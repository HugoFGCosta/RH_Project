<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Presence;
use App\Http\Requests\StorePresenceRequest;
use App\Http\Requests\UpdatePresenceRequest;
use App\Models\User_Shift;
use App\Models\Work_Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class PresenceController extends Controller
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


    /*  public function store(Request $request)
     {
        // metodo antigo

         $user = auth()->user();
         $userShift = User_Shift::where('user_id', $user->id)->whereNull('end_date')->first();
         $workShift = Work_Shift::find($userShift->work_shift_id);

         // VERIFICA se existe um registro de presença para user hoje
         $presence = Presence::where('user_id', $user->id)
             ->whereDate('created_at', Carbon::today())
             ->first();

         // VERIFICA se todos os 4 registros já foram preenchidos
         if ($presence && $presence->first_start && $presence->first_end && $presence->second_start && $presence->second_end) {
             return redirect()->to(url('user/presence'))->with('error', 'Já existe um registro de presença completo para hoje.');
         }

         if (!$presence) {
             $presence = new Presence;
             $presence->user_id = $user->id;
             $presence->first_start = Carbon::parse($request->first_start);
         } elseif (!$presence->first_end) {
             $presence->first_end = Carbon::parse($request->first_end);
         } elseif (!$presence->second_start) {
             $presence->second_start = Carbon::parse($request->second_start);
         } else {
             $presence->second_end = Carbon::parse($request->second_end);
         }

         // CALCULA a diferença em minutos e guarda em $presence->effective_hour



         $effective_hour = 0;



         if ($presence->first_start && $presence->first_end) {
             $first_start = Carbon::parse($presence->first_start);
             $first_end = Carbon::parse($presence->first_end);
             $effective_hour += $first_start->diffInMinutes($first_end);
         }
         if ($presence->second_start && $presence->second_end) {
             $second_start = Carbon::parse($presence->second_start);
             $second_end = Carbon::parse($presence->second_end);
             $effective_hour += $second_start->diffInMinutes($second_end);
         }


         $effective_hour /= 60;

         // PEGA a hora de término do turno
         $start_hour = Carbon::parse($workShift->start_hour);
         $end_hour = Carbon::parse($workShift->end_hour);

         // CALCULA as horas extras
         $extra_hours = 0;

         // CALCULA hora extra para quem entra antes da hora do turno
         $first_start = Carbon::parse($presence->first_start);
         if ($first_start->lt($start_hour)) {
             $extra_hours_early = $first_start->diffInMinutes($start_hour) / 60;
             if ($extra_hours_early >= 1) {
                 $extra_hours += $extra_hours_early;
             }
         }

         if ($presence->second_end && $presence->second_end->gt($end_hour)) {
             $extra_hours += $presence->second_end->diffInMinutes($end_hour) / 60;
         }

         $presence->extra_hour = $extra_hours;
         $presence->effective_hour = $effective_hour - $extra_hours;


         $verify_effective_hour = $effective_hour - $extra_hours;

         if ($verify_effective_hour < 0) {
             $effective_hour == 0;
             $presence->effective_hour = $effective_hour;
         } else {
             $presence->effective_hour = $effective_hour - $extra_hours;
         }

         $presence->save();

         return redirect()->to(url('/menu'));
     }  */




    public function verifyPresence()
    {
        // METODO das 15 horas auto-picagem

        /*  $presences = Presence::all()->where('first_start', '>=', Carbon::now()->subHours(15))
             ->whereNull('first_end'); */

        /* $presences = Presence::where('first_start', '>=', Carbon::now()->subHours(15))
            ->whereNull('first_end')
            ->get(); */
        //$presences = Presence::all();

        $presences = Presence::whereNull('first_end')
            ->orWhereNull('second_start')
            ->orWhereNull('second_end')
            ->get();


        //dd($presences);


        foreach ($presences as $presence) {
            //dd($presence);
            $first_start = Carbon::parse($presence->first_start);
            if ($first_start->addHours(15) <= Carbon::now()->addHour()) {
                //dd($presence);
                if ($presence->first_end == null) {
                    //dd($presence);
                    $presence->first_end = $first_start;
                    $presence->second_start = $first_start;
                    $presence->second_end = $first_start;
                    $presence->extra_hour = 0;
                    $presence->effective_hour = 0;
                    $presence->save();
                    //dd($presence);

                } elseif ($presence->second_end == null) {
                    $presence->second_end = $first_start;
                    $presence->extra_hour = 0;
                    $presence->effective_hour = 0;
                    $presence->save();
                }
            }
        }

        /*
                $presences_second = Presence::WhereNull('second_end')->get();

                foreach ($presences_second as $presence) {

                    $first_start = Carbon::parse($presence->first_start);

                    if ($first_start->addHours(15) <= Carbon::now()) {

                        if ($presence->second_end === null) {
                            $presence->second_end = $first_start;
                            $presence->extra_hour = 0;
                            $presence->effective_hour = 0;
                            $presence->save();
                        }
                    }
                } */
    }






    public function store(Request $request)
    {
        $user = auth()->user();
        $userShift = User_Shift::where('user_id', $user->id)->whereNull('end_date')->first();
        $workShift = Work_Shift::find($userShift->work_shift_id);

        // VERIFICA se existe um registro de presença para user hoje
        $presence = Presence::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        // VERIFICA se todos os 4 registros já foram preenchidos
        if ($presence && $presence->first_start && $presence->first_end && $presence->second_start && $presence->second_end) {
            return redirect()->to(url('menu'))->with('error', 'Já existe um registro de presença completo para hoje.');
        }

        // Verificar se o usuário tem uma falta no primeiro turno
        $absenceFirstShift = Absence::where('user_id', $user->id)
            ->where('absence_types_id', 1)
            ->whereDate('absence_start_date', Carbon::today())
            ->first();

        if ($absenceFirstShift && !$presence) {
            $presence = new Presence;
            $presence->user_id = $user->id;
            $presence->second_start = Carbon::now()->addHour();
        } elseif ($absenceFirstShift && $presence && is_null($presence->second_start)) {
            $presence->second_start = Carbon::now()->addHour();
        } elseif ($absenceFirstShift && $presence && is_null($presence->second_end)) {
            $presence->second_end = Carbon::now()->addHour();
        } else {
            if (!$presence) {
                $presence = new Presence;
                $presence->user_id = $user->id;
                $presence->first_start = Carbon::now()->addHour();
            } elseif (is_null($presence->first_end)) {
                $presence->first_end = Carbon::now()->addHour();
            } elseif (is_null($presence->second_start)) {
                $presence->second_start = Carbon::now()->addHour();
            } else {
                $presence->second_end = Carbon::now()->addHour();
            }
        }

        // CALCULA a diferença em minutos e guarda em $presence->effective_hour
        $effective_hour = 0;

        if ($presence->first_start && $presence->first_end) {
            $first_start = Carbon::parse($presence->first_start);
            $first_end = Carbon::parse($presence->first_end);
            $effective_hour += $first_start->diffInMinutes($first_end);
        }
        if ($presence->second_start && $presence->second_end) {
            $second_start = Carbon::parse($presence->second_start);
            $second_end = Carbon::parse($presence->second_end);
            $effective_hour += $second_start->diffInMinutes($second_end);
        }

        $effective_hour /= 60;

        // PEGA a hora começo/fim do turno
        $start_hour = Carbon::parse($workShift->start_hour);
        $end_hour = Carbon::parse($workShift->end_hour);

        // CALCULA as horas extras
        $extra_hours = 0;

        // CALCULA hora extra para quem entra antes da hora do turno
        $first_start = Carbon::parse($presence->first_start);
        if ($first_start->lt($start_hour)) {
            $extra_hours_early = $first_start->diffInMinutes($start_hour) / 60;
            if ($extra_hours_early >= 1) {
                $extra_hours += $extra_hours_early;
            }
        }

        if ($presence->second_end && Carbon::parse($presence->second_end)->gt($end_hour)) {
            $extra_hours += Carbon::parse($presence->second_end)->diffInMinutes($end_hour) / 60;
        }

        $presence->extra_hour = $extra_hours;
        $presence->effective_hour = $effective_hour - $extra_hours;

        $presence->save();

        return redirect()->to(url('/menu'));
    }



    /* public function store(Request $request)
    {
        $user = auth()->user();
        $userShift = User_Shift::where('user_id', $user->id)->first();  // FUNCIONA - MAS ESTA FALTANDO ADICIONAR O END DATE NO USER_SHIFT AO TROCAR DE HORARIO
        $workShift = Work_Shift::find($userShift->work_shift_id);

        // VERIFICA se existe um registro de presença para user hoje
        $presence = Presence::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->first();


        // VERIFICA se todos os 4 registros já foram preenchidos
        if ($presence && $presence->first_start && $presence->first_end && $presence->second_start && $presence->second_end) {
            return redirect()->to(url('user/presence'))->with('error', 'Já existe um registro de presença completo para hoje.');
        }

        if (!$presence) {
            $presence = new Presence;
            $presence->user_id = $user->id;
            $presence->first_start = Carbon::parse($request->first_start);
        } elseif (!$presence->first_end) {
            $presence->first_end = Carbon::parse($request->first_end);
        } elseif (!$presence->second_start) {
            $presence->second_start = Carbon::parse($request->second_start);
        } else {
            $presence->second_end = Carbon::parse($request->second_end);
        }

        // CALCULA a diferença em minutos e guarda em $presence->effective_hour
        $effective_hour = 0;

        if ($presence->first_start && $presence->first_end) {
            $first_start = Carbon::parse($presence->first_start);
            $first_end = Carbon::parse($presence->first_end);
            $effective_hour += $first_start->diffInMinutes($first_end);
        }
        if ($presence->second_start && $presence->second_end) {
            $second_start = Carbon::parse($presence->second_start);
            $second_end = Carbon::parse($presence->second_end);
            $effective_hour += $second_start->diffInMinutes($second_end);
        }
        $effective_hour /= 60;

        // PEGA a hora de término do turno
        $start_hour = Carbon::parse($workShift->start_hour);
        $end_hour = Carbon::parse($workShift->end_hour);


        // PEGA o horario de entradaa do funcionario
        $first_start = Carbon::parse($presence->first_start);




        // CALCULA as horas extras
        $extra_hours = 0;



        // CALCULA hora extra acima de 1 hora antes da hora do turno
        $extra_hours_early = 0;

        $extra_hours_early = $first_start->diffInMinutes($start_hour);
        if ($presence->first_start < $workShift->start_hour && $extra_hours_early >= 60) {
            $extra_hours += $extra_hours_early;
        }





        if ($presence->second_end && $presence->second_end->gt($end_hour)) {
            $extra_hours = $presence->second_end->diffInMinutes($end_hour) / 60;
        }

        $presence->extra_hour = $extra_hours;

        $verify_effective_hour = $effective_hour - $extra_hours;

        if ($verify_effective_hour < 0) {
            $effective_hour == 0;
            $presence->effective_hour = $effective_hour;
        } else {
            $presence->effective_hour = $effective_hour - $extra_hours;
        }


        $presence->save();

        return redirect()->to(url('/menu'));
    } */



    public function getStatus()
    {
        $user = auth()->user();
        $user_id = $user->id;
        $presence = Presence::where('user_id', $user_id)->whereDate('created_at', Carbon::today())->first();

        // Se não há registro de presença, o status é 'out'
        if (!$presence) {
            return response()->json(['status' => 'out']);
        }

        // Verifica se há uma ausência no primeiro turno
        $absenceFirstShift = Absence::where('user_id', $user_id)
            ->where('absence_types_id', 1)
            ->whereDate('absence_start_date', Carbon::today())
            ->first();

        // Se houve falta no primeiro turno, a próxima ação deve ser no segundo turno
        if ($absenceFirstShift) {
            if (is_null($presence->second_start)) {
                return response()->json(['status' => 'out', 'shift' => 'second']);
            } elseif (is_null($presence->second_end)) {
                return response()->json(['status' => 'in', 'shift' => 'second']);
            } else {
                return response()->json(['status' => 'completed']);
            }
        }

        // Verifica o estado dos registros de presença para determinar o status
        if (is_null($presence->first_start)) {
            return response()->json(['status' => 'out', 'shift' => 'first']);
        } elseif (is_null($presence->first_end)) {
            return response()->json(['status' => 'in', 'shift' => 'first']);
        } elseif (is_null($presence->second_start)) {
            return response()->json(['status' => 'out', 'shift' => 'second']);
        } elseif (is_null($presence->second_end)) {
            return response()->json(['status' => 'in', 'shift' => 'second']);
        } else {
            return response()->json(['status' => 'completed']);
        }
    }


    /* public function storeSimulated(Request $request)
    {
        // CODIGO FUNCIONANDO 100%  até a hora efetiva
        // SIMULA HORA FICTICIA - % , nao importa as horas efetivas compara com a saida do turno para definir horas extras

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

        // CALCULA a diferença em minutos e guarda em $presence->effective_hour
        $presence->effective_hour = ($first_start->diffInMinutes($first_end) + $second_start->diffInMinutes($second_end)) / 60;

        $presence->save();

        return redirect()->to(url('/menu'));
    } */

    /* public function storeSimulated(Request $request)
    {
        // SIMULA HORA FICTICIA - 80% , nao importa as horas efetivas compara com a saida do turno para definir horas extras
        // TUDO FUNCIONA, MAS AINDA NAO TEM TOLERANCIA DE ATRASO

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
        $first_start = Carbon::parse($request->first_start);
        $first_end = Carbon::parse($request->first_end);
        $second_start = Carbon::parse($request->second_start);
        $second_end = Carbon::parse($request->second_end);

        // CALCULA a diferença em minutos e guarda em $presence->effective_hour

        $effective_hour = ($first_start->diffInMinutes($first_end) + $second_start->diffInMinutes($second_end)) / 60; // HORA efetiva em HORAS*

        $presence->first_start = $first_start;
        $presence->first_end = $first_end;
        $presence->second_start = $second_start;
        $presence->second_end = $second_end;

        // SIMULA hora com pre-definida no formulario
        $second_end = Carbon::parse($request->second_end);

        // PEGA a hora de término do turno
        $end_hour = Carbon::parse($workShift->end_hour);

        // CALCULA as horas extras

        $extra_hours = 0;

        $first_start = Carbon::parse($presence->first_start);
        $start_hour = Carbon::parse($workShift->start_hour);


        $extra_hours_early = $first_start->diffInMinutes($start_hour);

        if ($presence->first_start < $workShift->start_hour && $extra_hours_early >= 60) {
            $extra_hours += $extra_hours_early;

            $extra_hours_early = $first_start->diffInMinutes($start_hour) / 60;
            $effective_hour -= $extra_hours_early;
        }


        if ($second_end->gt($end_hour)) {
            $extra_hours = $second_end->diffInMinutes($end_hour) / 60;
        }

        $presence->extra_hour = $extra_hours;


        // ADICIONA as horas extras ao objeto Presence
        $presence->extra_hour = $extra_hours;
        $presence->effective_hour = $effective_hour - $extra_hours;

        $presence->save();

        return redirect()->to(url('/menu'));
    } */


    public function storeSimulated(Request $request)
    {
        $user = auth()->user();
        $userShift = User_Shift::where('user_id', $user->id)->whereNull('end_date')->first();
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
        $first_start = Carbon::parse($request->first_start);
        $first_end = Carbon::parse($request->first_end);
        $second_start = Carbon::parse($request->second_start);
        $second_end = Carbon::parse($request->second_end);

        // CALCULA a diferença em minutos para cada turno e guarda em $presence->work_hour
        $first_shift_hours = $first_start->diffInMinutes($first_end) / 60; // HORA de trabalho em HORAS* para o primeiro turno
        $second_shift_hours = $second_start->diffInMinutes($second_end) / 60; // HORA de trabalho em HORAS* para o segundo turno

        $work_hour = $first_shift_hours + $second_shift_hours; // soma das horas de trabalho dos dois turnos

        $presence->first_start = $first_start;
        $presence->first_end = $first_end;
        $presence->second_start = $second_start;
        $presence->second_end = $second_end;

        // PEGA a hora de término do turno
        $end_hour = Carbon::parse($workShift->end_hour);

        // CALCULA as horas extras
        $extra_hours = 0;

        $first_start = Carbon::parse($presence->first_start);
        $start_hour = Carbon::parse($workShift->start_hour);

        if ($first_start->lt($start_hour)) {
            $extra_hours_early = $first_start->diffInMinutes($start_hour) / 60;
            if ($extra_hours_early >= 1) {
                $extra_hours += $extra_hours_early;
            }
        }

        if ($second_end->gt($end_hour)) {
            $extra_hours += $second_end->diffInMinutes($end_hour) / 60;
        }

        $presence->extra_hour = $extra_hours;
        $presence->effective_hour = $work_hour - $extra_hours;

        $presence->save();

        return redirect()->to(url('/menu'));
    }






    /**
     * Display the specified resource.
     */
    public function show(Presence $presence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presence $presence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePresenceRequest $request, Presence $presence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presence $presence)
    {
        //
    }

    public function presence(Request $request)
    {
        $user = auth()->user();
        $presence = Presence::where('user_id', $user->id)->first();

        return view('pages.menu.menu', ['user' => $user, 'presence' => $presence]);
    }

    public function getPresence()
    {
        $user = auth()->user();
        $presence = Presence::where('user_id', $user->id)->first();

        return view('pages.menu.menu', ['user' => $user, 'presence' => $presence]);
    }

    public function import(Request $request)
    {
        // Verifica se foi escolhido um arquivo
        $file = $request->file('file');

        if (!$file) {
            return redirect()->back()->with('error', 'Escolha um ficheiro antes de importar.');
        }

        $handle = fopen($file->getPathname(), 'r');

        if (!$handle) {
            return redirect()->back()->with('error', 'Erro ao abrir o ficheiro.');
        }

        // Ignora a primeira linha (cabeçalhos)
        fgets($handle);

        // Armazena mensagens de erro
        $errors = [];

        // Verifica os dados do arquivo antes de truncar a tabela
        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);

            if (count($data) != 7) {
                return redirect()->back()->with('error', 'Certifique-se que este ficheiro contem informações de presenças.');
            }

            // Verifica se os IDs são inteiros
            if (!is_integer($data[0])) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de utilizador são números válidos.');
            }


            if (!is_numeric($data[5]) || !is_numeric($data[6])) {
                return redirect()->back()->with('error', 'Certifique-se que os campos de horas extra e efetivas são válidos.');
            }

            if (!strtotime($data[1]) || !strtotime($data[2]) || !strtotime($data[3]) || !strtotime($data[4])) {
                return redirect()->back()->with('error', 'Certifique-se que as datas estão no formato correto.');
            }

        }

        fclose($handle);

        // Se houver erros, redireciona de volta com as mensagens de erro
        if (!empty($errors)) {
            return redirect()->back()->with('error', $errors);
        }

        // Desativa as verificações de chave estrangeira
        Schema::disableForeignKeyConstraints();

        // Trunca a tabela
        DB::table('presences')->truncate();

        // Reabilita as verificações de chave estrangeira
        Schema::enableForeignKeyConstraints();

        // Abre novamente o arquivo para importar os dados
        $handle = fopen($file->getPathname(), 'r');

        // Ignora a primeira linha (cabeçalhos)
        fgets($handle);

        // Percorre o ficheiro e insere os dados na base de dados
        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);

            Presence::create([
                'user_id' => $data[0],
                'first_start' => $data[1],
                'first_end' => $data[2],
                'second_start' => $data[3],
                'second_end' => $data[4],
                'extra_hour' => $data[5],
                'effective_hour' => $data[6],
            ]);
        }

        fclose($handle);

        // Retorna para a página anterior com uma mensagem de sucesso
        return redirect()->back()->with('success', 'Presenças importadas com sucesso.');
    }


    public function export()
    {
        // Define o nome do ficheiro e os cabeçalhos
        $presences = Presence::all();
        $csvFileName = 'presences.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['User_id', 'First_start', 'First_end', 'Second_start', 'Second_end', 'Extra_hour', 'Effective_hour']);

        //Para cada presença insere uma linha no ficheiro
        foreach ($presences as $presence) {
            fputcsv($handle, [$presence->user_id, $presence->first_start, $presence->first_end, $presence->second_start, $presence->second_end, $presence->extra_hour, $presence->effective_hour]); // Add more fields as needed
        }

        fclose($handle);

        // Retorna o ficheiro
        return Response::make('', 200, $headers);
    }
}
