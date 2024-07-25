<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Presence;
use App\Http\Requests\StorePresenceRequest;
use App\Http\Requests\UpdatePresenceRequest;
use App\Models\User;
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
    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    // Metodo verifyPresence - Verifica as presenças em caso de esquecimento de saida
    // Caso tenha passado 15 horas da entrada fecha o turno e atribui hora efetiva e extra como 0
    // função agora desempenhada pelo MYSQL
    public function verifyPresence()
    {
        $presences = Presence::whereNull('first_end')
            ->orWhereNull('second_start')
            ->orWhereNull('second_end')
            ->get();


        foreach ($presences as $presence) {
            $first_start = Carbon::parse($presence->first_start);
            if ($first_start->addHours(15) <= Carbon::now()->addHour()) {

                // Se nao tiver a first_end do periodo de trabalho apos 15 horas
                // Atribui a hora de entrada para (first_end, second_start, second_end) como first_start + 15horas
                if ($presence->first_end == null) {
                    $presence->first_end = $first_start;
                    $presence->second_start = $first_start;
                    $presence->second_end = $first_start;
                    $presence->extra_hour = 0;
                    $presence->effective_hour = 0;
                    $presence->save();


                    // Se nao tiver a second_end do periodo de trabalho apos 15 horas
                    // Atribui a hora de entrada para (first_end, second_start, second_end) como first_start + 15horas
                } elseif ($presence->second_end == null) {
                    $presence->second_end = $first_start;
                    $presence->extra_hour = 0;
                    $presence->effective_hour = 0;
                    $presence->save();
                }
            }
        }
    }


    //Metodo store - Guarda as presenças de cada user logado.
    public function store(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();


        // Buscar o turno de trabalho mais recente para o utilizador
        $userShift = User_Shift::where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->whereDate('start_date', '<=', $today)
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$userShift) {
            Log::error('User shift not found for user', ['user_id' => $user->id]);
            return response()->json(['error' => 'User shift not found.'], 404);
        }
        $workShift = Work_Shift::find($userShift->work_shift_id);


        // VERIFICA se existe um registro de presença para user hoje
        $presence = Presence::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->first();


        // VERIFICA se todos os 4 registros já foram preenchidos
        if ($presence && $presence->first_start && $presence->first_end && $presence->second_start && $presence->second_end) {
            return response()->json(['error' => 'Já existe um registro de presença completo para hoje.'], 400);
        }


        // Verificar se o utilizador tem uma falta no primeiro turno
        $absenceFirstShift = Absence::where('user_id', $user->id)
            ->where('absence_types_id', 1)
            ->whereDate('absence_start_date', Carbon::today())
            ->first();


        //Se houver uma falta no primeiro turno  e nao existir presença cria a presença no segundo turno
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


        // CALCULA hora extra para quem entra antes da hora do turno
        $extra_hours = 0;
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

        // Se hora extra for menos que 0, atribui valor 0
        if ($extra_hours < 0) {
            $extra_hours = 0;
        }
        $presence->extra_hour = $extra_hours;


        // Se hora efetiva for menor que 0, atribui valor 0. Caso contrario subtrai as horas extras da hora efetiva.
        if (($effective_hour - $extra_hours) < 0) {
            $presence->effective_hour = 0;
        } else {
            $presence->effective_hour = $effective_hour - $extra_hours;
        }

        $presence->save();
        return response()->json(['success' => 'Presença registrada com sucesso.']);
    }


    // Metodo getStatus - Determina se está entrando ou saindo do turno
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


    public function show(Presence $presence)
    {
        //
    }


    public function edit(Presence $presence)
    {
        //
    }


    public function update(UpdatePresenceRequest $request, Presence $presence)
    {
        //
    }


    public function destroy(Presence $presence)
    {
        //
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
            if (!is_numeric($data[0])) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de utilizador são números válidos.');
            }

            if (!is_numeric($data[5]) || !is_numeric($data[6])) {
                return redirect()->back()->with('error', 'Certifique-se que os campos de horas extra e efetivas são válidos.');
            }

            if (!strtotime($data[1]) || !strtotime($data[2]) || !strtotime($data[3]) || !strtotime($data[4])) {
                return redirect()->back()->with('error', 'Certifique-se que as datas estão no formato correto.');
            }

            //Verifica se o user_id existe
            $user = User::find($data[0]);

            if (!$user) {
                return redirect()->back()->with('error', 'Certifique se todos os Ids de utilizador correspondem a um utilizador existente.');
            }

            //Verifica se existe um horario para o utilizador na altura da presença
            $usersShifts = User_Shift::all();
            $firstStartDate = strtotime($data[1]);
            $firstEndDate = strtotime($data[2]);
            $secondStartDate = strtotime($data[3]);
            $secondEndDate = strtotime($data[4]);

            $verFirstStart = false;
            $verFirstEnd = false;
            $verSecondStart = false;
            $verSecondEnd = false;

            foreach ($usersShifts as $usersShift){

                if(date("Y-m-d H:i:s", $firstStartDate)>=$usersShift->start_date && date("Y-m-d H:i:s", $firstStartDate) <= $usersShift->end_date && $usersShift->user_id == $data[0]) {
                    $verFirstStart = true;
                }
                elseif(date("Y-m-d H:i:s", $firstStartDate)>=$usersShift->start_date && $usersShift->end_date == null && $usersShift->user_id == $data[0]){
                    $verFirstStart = true;
                }

                if(date("Y-m-d H:i:s", $firstEndDate)>=$usersShift->start_date && date("Y-m-d H:i:s", $firstEndDate) <= $usersShift->end_date && $usersShift->user_id == $data[0]) {
                    $verFirstEnd = true;
                }
                elseif(date("Y-m-d H:i:s", $firstEndDate)>=$usersShift->start_date && $usersShift->end_date == null && $usersShift->user_id == $data[0]){
                    $verFirstEnd = true;
                }

                if(date("Y-m-d H:i:s", $secondStartDate)>=$usersShift->start_date && date("Y-m-d H:i:s", $secondStartDate) <= $usersShift->end_date && $usersShift->user_id == $data[0]) {
                    $verSecondStart = true;
                }
                elseif(date("Y-m-d H:i:s", $secondStartDate)>=$usersShift->start_date && $usersShift->end_date == null && $usersShift->user_id == $data[0]){
                    $verSecondStart = true;
                }

                if(date("Y-m-d H:i:s", $secondEndDate)>=$usersShift->start_date && date("Y-m-d H:i:s", $secondEndDate) <= $usersShift->end_date && $usersShift->user_id == $data[0]) {
                    $verSecondEnd = true;
                }
                elseif(date("Y-m-d H:i:s", $secondEndDate)>=$usersShift->start_date && $usersShift->end_date == null && $usersShift->user_id == $data[0]){
                    $verSecondEnd = true;
                }

            }

            //Caso nao exista um horario para o utilizador na altura da presaença, é mostrada uma mensagem de erro
            if($verFirstStart == false || $verFirstEnd == false ||  $verSecondStart == false || $verSecondEnd == false){
                return redirect()->back()->with('error', 'Certifique-se que os utilizadores têm um horário na altura de todas as presenças.');
            }

        }

        fclose($handle);

        // Se houver erros, redireciona de volta com as mensagens de erro
        if (!empty($errors)) {
            return redirect()->back()->with('error', $errors);
        }

        // Desativa as verificações de chave estrangeira
        Schema::disableForeignKeyConstraints();

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

        // Cria um buffer para armazenar o conteúdo CSV temporariamente
        $output = fopen('php://temp', 'r+');

        fputcsv($output, ['User_id', 'First_start', 'First_end', 'Second_start', 'Second_end', 'Extra_hour', 'Effective_hour']);

        //Para cada presença insere uma linha no ficheiro
        foreach ($presences as $presence) {
            fputcsv($output, [$presence->user_id, $presence->first_start, $presence->first_end, $presence->second_start, $presence->second_end, $presence->extra_hour, $presence->effective_hour]); // Add more fields as needed
        }

        // Volta para o início do buffer para leitura
        rewind($output);

        // Captura o conteúdo CSV
        $csvContent = stream_get_contents($output);

        // Fecha o buffer
        fclose($output);

        // Retorna a resposta com o conteúdo CSV e os headers apropriados
        return response($csvContent, 200, $headers);
    }
}
