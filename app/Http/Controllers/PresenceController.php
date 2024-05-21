<?php

namespace App\Http\Controllers;

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


    public function store(Request $request)
    {
        Log::info('Store method called.');

        if (auth()->check()) {
        Log::info('User authenticated: ' . auth()->user()->id);
        $user=auth()->user();
        $user_id = $user->id;

        $userShift = User_Shift::where('user_id', $user_id)->first();
        $workShift = Work_Shift::find($userShift->work_shift_id);
        $presence = Presence::where('user_id', $user_id)->whereDate('created_at', Carbon::today())->first();

        if (!$presence) {
            $presence = new Presence;
            $presence->user_id = $user_id;
            $presence->first_start = $request->input('first_start') ?? now();
        } elseif (!$presence->first_end) {
            $presence->first_end = $request->input('first_end') ?? now();
        } elseif (!$presence->second_start) {
            $presence->second_start = $request->input('second_start') ?? now();
        } else {
            $existingPresence = Presence::where('user_id', $user_id)->whereNotNull('second_end')->whereDate('created_at', Carbon::today())->first();

            if ($existingPresence) {
                Log::info('Já existe um registro de presença completo para hoje.');
                return redirect()->to(url('user/presence'))->with('error', 'Já existe um registro de presença completo para hoje.');
            }

            $presence->second_end = $request->input('second_end') ?? now();

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

        return redirect()->to(url('/menu'))->with('success', 'Presença registrada com sucesso.');
        } else {
            Log::error('User not authenticated');
           return redirect()->back()->with('error', 'Usuário não autenticado.');
        }
    }

    public function getStatus()
    {
        $user = auth()->user();
        $user_id = $user->id;
        $presence = Presence::where('user_id', $user_id)->whereDate('created_at', Carbon::today())->first();

        if (!$presence || is_null($presence->first_start)) {
            return response()->json(['status' => 'out']);
        } elseif (is_null($presence->first_end)) {
            return response()->json(['status' => 'in']);
        } elseif (is_null($presence->second_start)) {
            return response()->json(['status' => 'out']);
        } elseif (is_null($presence->second_end)) {
            return response()->json(['status' => 'in']);
        } else {
            return response()->json(['status' => 'out']);
        }
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

            if(!strtotime($data[1]) || !strtotime($data[2]) || !strtotime($data[3]) || !strtotime($data[4])){
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


    public function export(){
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
