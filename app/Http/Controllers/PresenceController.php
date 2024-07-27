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


    public function store(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Buscar o turno de trabalho mais recente para o utilizador
        $userShift = User_Shift::where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$userShift) {
            Log::error('User shift not found for user', ['user_id' => $user->id]);
            return response()->json(['error' => 'User shift not found.'], 404);
        }

        $workShift = Work_Shift::find($userShift->work_shift_id);
        Log::info('Work shift found', ['work_shift' => $workShift]);

        // Verifica se existe um registro de presença para o usuário hoje ou se a presença de ontem não foi completada
        $presence = Presence::where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereDate('created_at', Carbon::today())
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereDate('created_at', Carbon::yesterday())
                            ->whereNull('second_end');
                    });
            })
            ->first();

        $currentTime = Carbon::now();
        $start_hour = Carbon::parse($workShift->start_hour);
        $break_start = Carbon::parse($workShift->break_start);
        $break_end = Carbon::parse($workShift->break_end);
        $end_hour = Carbon::parse($workShift->end_hour);

        // Ajuste para considerar o `break_start`, `break_end`, e `end_hour` no dia seguinte, se necessário
        if ($break_start->lt($start_hour)) {
            $break_start->addDay();
        }
        if ($break_end->lt($break_start)) {
            $break_end->addDay();
        }
        if ($end_hour->lt($start_hour)) {
            $end_hour->addDay();
        }

        Log::info('Calculated shift times', [
            'current_time' => $currentTime,
            'start_hour' => $start_hour,
            'break_start' => $break_start,
            'break_end' => $break_end,
            'end_hour' => $end_hour
        ]);

        if (!$presence) {
            $presence = new Presence;
            $presence->user_id = $user->id;

            // Se a hora atual for após o break_start e first_start for nulo, registrar no second_start
            if ($currentTime->gte($break_start) && is_null($presence->first_start)) {
                $presence->second_start = $currentTime;
                Log::info('Registering second_start as first_start is null after break_start', ['time' => $currentTime]);
            } else {
                $presence->first_start = $currentTime;
                Log::info('Registering first_start', ['time' => $currentTime]);
            }
        } else {
            Log::info('Presence already exists', ['presence' => $presence]);

            // Fechar first_start se estiver preenchido e first_end estiver nulo
            if (!is_null($presence->first_start) && is_null($presence->first_end)) {
                $presence->first_end = $currentTime;
                Log::info('Updating first_end', ['time' => $currentTime]);
            } elseif (!is_null($presence->first_end) && is_null($presence->second_start)) {
                // Atualizar second_start se first_end estiver preenchido e second_start estiver nulo
                $presence->second_start = $currentTime;
                Log::info('Updating second_start', ['time' => $currentTime]);
            } elseif (!is_null($presence->second_start) && is_null($presence->second_end)) {
                $presence->second_end = $currentTime;
                Log::info('Updating second_end', ['time' => $currentTime]);
            } else {
                Log::error('Todos os horários já estão preenchidos', ['time' => $currentTime]);
                return response()->json(['error' => 'Todos os horários já estão preenchidos.'], 400);
            }
        }

        // Calcula a diferença em minutos e guarda em $presence->effective_hour
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
        $end_hour = Carbon::parse($workShift->end_hour)->addDay($workShift->end_hour < $workShift->start_hour ? 1 : 0);

        // CALCULA hora extra para quem entra antes da hora do turno
        $extra_hours = 0;
        if ($presence->first_start) {
            $first_start = Carbon::parse($presence->first_start);
            if ($first_start->lt($start_hour)) {
                $extra_hours_early = $first_start->diffInMinutes($start_hour) / 60;
                if ($extra_hours_early >= 1) {
                    $extra_hours += $extra_hours_early;
                }
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

        Log::info('Presence to be saved', ['presence' => $presence]);

        $presence->save();
        return response()->json(['success' => 'Presença registrada com sucesso.']);
    }

    public function getStatus()
    {
        $user = auth()->user();
        $presence = Presence::where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereDate('created_at', Carbon::today())
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereDate('created_at', Carbon::yesterday())
                            ->whereNull('second_end');
                    });
            })
            ->first();

        // Verifica se há um registro de presença existente
        if (!$presence) {
            return response()->json(['status' => 'out']);
        }

        // Lógica para verificar o estado dos registros de presença
        if (!is_null($presence->second_start)) {
            if (is_null($presence->second_end)) {
                return response()->json(['status' => 'in', 'shift' => 'second']);
            } else {
                return response()->json(['status' => 'completed']);
            }
        } else {
            if (is_null($presence->first_start)) {
                return response()->json(['status' => 'out', 'shift' => 'first']);
            } elseif (is_null($presence->first_end)) {
                return response()->json(['status' => 'in', 'shift' => 'first']);
            } elseif (is_null($presence->second_start)) {
                return response()->json(['status' => 'out', 'shift' => 'second']);
            }
        }

        return response()->json(['status' => 'completed']);
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


    //Método import- Serve para importar presenças de um ficheiro CSV
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

            if ($data[1] != "") {
                if (!strtotime($data[1])) {
                    return redirect()->back()->with('error', 'Certifique-se que as datas estão no formato correto.');
                }
            }

            if ($data[2] != "") {
                if (!strtotime($data[2])) {
                    return redirect()->back()->with('error', 'Certifique-se que as datas estão no formato correto.');
                }
            }

            if ($data[3] != "") {
                if (!strtotime($data[3])) {
                    return redirect()->back()->with('error', 'Certifique-se que as datas estão no formato correto.');
                }
            }

            if ($data[4] != "") {
                if (!strtotime($data[4])) {
                    return redirect()->back()->with('error', 'Certifique-se que as datas estão no formato correto.');
                }
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

            foreach ($usersShifts as $usersShift) {

                if (date("Y-m-d H:i:s", $firstStartDate) >= $usersShift->start_date && date("Y-m-d H:i:s", $firstStartDate) <= $usersShift->end_date && $usersShift->user_id == $data[0]) {
                    $verFirstStart = true;
                } elseif (date("Y-m-d H:i:s", $firstStartDate) >= $usersShift->start_date && $usersShift->end_date == null && $usersShift->user_id == $data[0]) {
                    $verFirstStart = true;
                }

                if (date("Y-m-d H:i:s", $firstEndDate) >= $usersShift->start_date && date("Y-m-d H:i:s", $firstEndDate) <= $usersShift->end_date && $usersShift->user_id == $data[0]) {
                    $verFirstEnd = true;
                } elseif (date("Y-m-d H:i:s", $firstEndDate) >= $usersShift->start_date && $usersShift->end_date == null && $usersShift->user_id == $data[0]) {
                    $verFirstEnd = true;
                }

                if (date("Y-m-d H:i:s", $secondStartDate) >= $usersShift->start_date && date("Y-m-d H:i:s", $secondStartDate) <= $usersShift->end_date && $usersShift->user_id == $data[0]) {
                    $verSecondStart = true;
                } elseif (date("Y-m-d H:i:s", $secondStartDate) >= $usersShift->start_date && $usersShift->end_date == null && $usersShift->user_id == $data[0]) {
                    $verSecondStart = true;
                }

                if (date("Y-m-d H:i:s", $secondEndDate) >= $usersShift->start_date && date("Y-m-d H:i:s", $secondEndDate) <= $usersShift->end_date && $usersShift->user_id == $data[0]) {
                    $verSecondEnd = true;
                } elseif (date("Y-m-d H:i:s", $secondEndDate) >= $usersShift->start_date && $usersShift->end_date == null && $usersShift->user_id == $data[0]) {
                    $verSecondEnd = true;
                }

            }

            //Se alguma data estiver vazia a validação é feita a true
            if ($data[1] == "") {
                $verFirstStart = true;
            }
            if ($data[2] == "") {
                $verFirstEnd = true;
            }
            if ($data[3] == "") {
                $verSecondStart = true;
            }
            if ($data[4] == "") {
                $verSecondEnd = true;
            }

            //Caso nao exista um horario para o utilizador na altura da presaença, é mostrada uma mensagem de erro
            if ($verFirstStart == false || $verFirstEnd == false || $verSecondStart == false || $verSecondEnd == false) {
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

            $presence = new Presence();
            $presence->user_id = $data[0];

            // Se existirem datas vazias, atribui null
            if ($data[1] != "") {
                $presence->first_start = $data[1];
            } else {
                $presence->first_start = null;
            }
            if ($data[2] != "") {
                $presence->first_end = $data[2];
            } else {
                $presence->first_end = null;
            }
            if ($data[3] != "") {
                $presence->second_start = $data[3];
            } else {
                $presence->second_start = null;
            }
            if ($data[4] != "") {
                $presence->second_end = $data[4];
            } else {
                $presence->second_end = null;
            }
            if ($data[5] != "") {
                $presence->extra_hour = $data[5];
            } else {
                $presence->extra_hour = null;
            }
            if ($data[6] != "") {
                $presence->effective_hour = $data[6];
            } else {
                $presence->effective_hour = null;
            }
            $presence->save();
        }

        fclose($handle);

        // Retorna para a página anterior com uma mensagem de sucesso
        return redirect()->back()->with('success', 'Presenças importadas com sucesso.');
    }


    //Método export - Exporta as presenças para um ficheiro CSV
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

        fputcsv($output, ['Id_Utilizador', 'Primeira_Entrada', 'Primeira_Saida', 'Segunda_Entrada', 'Segunda_Saida', 'Horas_Extra', 'Horas_Efetivas']);

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
