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
        $user = auth()->user();
        $userShift = User_Shift::where('user_id', $user->id)->first();
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
        $end_hour = Carbon::parse($workShift->end_hour);

        // CALCULA as horas extras
        $extra_hours = 0;
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

    public function storeSimulated(Request $request)
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

        $effective_hour = ($first_start->diffInMinutes($first_end) + $second_start->diffInMinutes($second_end)) / 60;

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
        if ($second_end->gt($end_hour)) {
            $extra_hours = $second_end->diffInMinutes($end_hour) / 60;
        }

        $presence->extra_hour = $extra_hours;


        // ADICIONA as horas extras ao objeto Presence
        $presence->extra_hour = $extra_hours;
        $presence->effective_hour = $effective_hour - $extra_hours;

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