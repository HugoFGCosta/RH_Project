<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Models\Notification;
use App\Models\Vacation;
use App\Http\Requests\StoreVacationRequest;
use App\Http\Requests\UpdateVacationRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Mockery\Exception;

class VacationController extends Controller
{
    public function must_date($start, $end, $table_id)
    {
        $vacations = Vacation::where('user_id', auth::id())
            ->whereIn('vacation_approval_states_id', [3, 1])
            ->get(['id', 'date_start', 'date_end']);

        $totaldias = 0;
        $validar = false;

        foreach ($vacations as $vacation) {
            // Ignora a mesma entrada de tabela
            if ($vacation->id == $table_id) {
                continue;
            }

            $start_date = $vacation->date_start;
            $end_date = $vacation->date_end;

            $diff_date = Carbon::parse($start_date)->diffInDaysFiltered(function (Carbon $remover) {
                    return !$remover->isWeekend();
                }, Carbon::parse($end_date)) + 1;

            $totaldias += $diff_date;

            if ($diff_date >= 10) {
                $validar = true;
            }
        }

        $new_diff_date = Carbon::parse($start)->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($end)) + 1;

        if ((22 - $totaldias - $new_diff_date) >= 10 || $new_diff_date >= 10) {
            $validar = true;
        }

        return $validar;
    }

    public function difTotal($user)
    {
        $currentYear = date('Y');
        $starterDate = $currentYear . '-04-01';
        $finalDate = ($currentYear + 1) . '-03-31';

        $vacations = Vacation::where('user_id', $user)
            ->whereIn('vacation_approval_states_id', [3, 1])
            ->whereBetween('date_start', [$starterDate, $finalDate])
            ->get(['date_start', 'date_end']);

        $total_dias = 0;

        foreach ($vacations as $vacation) {
            $diff_date = Carbon::parse($vacation->date_start)->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($vacation->date_end))+1;
            $total_dias += $diff_date;
        }

        return $total_dias;
    }

    public function difInput($start,$end,$aprovacao, $start_anterior,$end_anterior )
    {
        $diff_date_anterior = Carbon::parse($start_anterior)->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($end_anterior)) + 1;
        $diff_date = Carbon::parse($start)->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($end)) + 1;
        if($aprovacao == 2 ){
        return ($this->difTotal(1) - $diff_date);
        }
        else {
            return ($this->difTotal(1) + $diff_date);
        }
    }

    public function index()
    {
        $total_dias = $this->difTotal(Auth::id());
        $roleId = auth()->user()->role_id;
        if ($roleId > 1) {
            $vacation = Vacation::with(['user', 'approvedBy'])->orderBy('id', 'asc')->get();
        } else {
            $vacation = Vacation::with(['user', 'approvedBy'])->orderBy('id', 'asc')->where('user_id', Auth::id())->get();
        }

        return view('pages.vacations.show', ['vacations' => $vacation])->with('totaldias', $total_dias)->with('role', $roleId);
    }

    public function create()
    {
        $roleId = auth()->user()->role_id;
        $totaldias = $this->difTotal(Auth::id());
        return view('pages.vacations.create')->with('totaldias', $totaldias)->with('role', $roleId);
    }

    public function timeCollide($vacation_id, $user_id, $start, $end)
    {
        $vacations = Vacation::where('user_id', $user_id)
            ->where('id', '<>', $vacation_id)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('date_start', [$start, $end])
                    ->orWhereBetween('date_end', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('date_start', '<=', $start)
                            ->where('date_end', '>=', $end);
                    });
            })
            ->exists();
        return !$vacations;
    }

    public function store(StoreVacationRequest $request)
    {
        $messages = [
            'date_start.required' => 'O dia de inicio é obrigatório.',
            'date_start.after' => 'O dia de inicio deve ser uma data após hoje.',
            'date_start.before' => 'O dia de inicio deve ser antes do dia de fim.',
            'date_end.required' => 'O dia de fim é obrigatório.',
            'date_end.after' => 'O dia de fim deve ser uma data após amanhã.',
            'date_end.after:date_start' => 'O dia de fim deve ser após o dia de inicio.',
            'date_start.weekday' => 'O dia de início não pode ser um fim de semana.',
            'date_end.weekday' => 'O dia de fim não pode ser um fim de semana.',
        ];

        // Validações iniciais
        if (!$request->has('date_start')) {
            return redirect(url('/vacations/create'))->with('error', $messages['date_start.required']);
        }
        if (!$request->has('date_end')) {
            return redirect(url('/vacations/create'))->with('error', $messages['date_end.required']);
        }
        if (Carbon::parse($request->date_start)->isWeekend()) {
            return redirect(url('/vacations/create'))->with('error', $messages['date_start.weekday']);
        }
        if (Carbon::parse($request->date_end)->isWeekend()) {
            return redirect(url('/vacations/create'))->with('error', $messages['date_end.weekday']);
        }
        if (strtotime($request->date_start) <= strtotime('today')) {
            return redirect(url('/vacations/create'))->with('error', $messages['date_start.after']);
        }
        if (strtotime($request->date_end) < strtotime($request->date_start)) {
            return redirect(url('/vacations/create'))->with('error', $messages['date_end.after:date_start']);
        }

        // Calcula a diferença de dias da nova solicitação
        $new_diff_date = Carbon::parse($request->date_start)->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($request->date_end)) + 1;

        // Obtém o total de dias de férias já marcados
        $total_dias_atuais = $this->difTotal(Auth::id());

        // Verifica se o novo total de dias não ultrapassa o limite
        if (($total_dias_atuais + $new_diff_date) > 22) {
            return redirect(url('/vacations/create'))->with('error', 'Você excedeu o limite de dias de férias disponíveis.');
        }

        // Verifica se há colisão de datas
        if ($this->timeCollide(0, auth::id(), $request->date_start, $request->date_end) &&
            $this->must_date($request->date_start, $request->date_end, 0)) {

            $vacation = new Vacation();
            $vacation->user_id = Auth::id();
            $vacation->vacation_approval_states_id = 3;
            $vacation->approved_by = null;
            $vacation->date_start = $request->date_start;
            $vacation->date_end = $request->date_end;
            $vacation->save();

            return redirect(url('/vacation'))->with('success', 'Criado com sucesso!');
        } else {
            return redirect(url('/vacations/create'))->with('error', 'Houve um erro durante a marcação de férias, verifique se já tem 10 dias seguidos marcados ou se os dias pedidos já estão marcados!');
        }
    }


    public function show()
    {
        $totaldias = $this->difTotal(Auth::id());
        $roleId = auth::id();
        $vacation = Vacation::where('user_id', $roleId)->orderBy('id', 'asc')->get();
        return view('pages.vacations.show', ['vacations' => $vacation])->with('totaldias', $totaldias)->with('role', $roleId);
    }

    public function edit(Vacation $vacation)
    {
        $roleId = Auth::user()->role_id;
        $totaldias = $this->difTotal(Auth::id());
        $role_id_table = $vacation->user->role_id;

        return view('pages.vacations.edit', [
            'vacations' => $vacation,
            'totaldias' => $totaldias,
            'role' => $roleId,
            'role_id_table' => $role_id_table
        ]);
    }

    public function update(UpdateVacationRequest $request, Vacation $vacation)
    {
        $messages = [
            'date_start.required' => 'O dia de inicio é obrigatório.',
            'date_start.after' => 'O dia de inicio deve ser uma data após hoje.',
            'date_start.before' => 'O dia de inicio deve ser antes do dia de fim.',
            'date_end.required' => 'O dia de fim é obrigatório.',
            'date_end.after' => 'O dia de fim deve ser uma data após amanhã.',
            'date_end.after:date_start' => 'O dia de fim deve ser após o dia de inicio.',
        ];

        // Validações necessárias
        $validatedData = $request->validate([
            'date_start' => 'required|date|after:today',
            'date_end' => 'required|date|after_or_equal:date_start',
        ], $messages);

        if (Carbon::parse($request->date_start)->isWeekend()) {
            return redirect()->back()->with('error', $messages['date_start.weekday']);
        }
        if (Carbon::parse($request->date_end)->isWeekend()) {
            return redirect()->back()->with('error', $messages['date_end.weekday']);
        }

        // Calcular a diferença de dias anterior e nova
        $dias_antigos = Carbon::parse($vacation->date_start)->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isWeekend();
            }, Carbon::parse($vacation->date_end)) + 1;

        $dias_novos = Carbon::parse($request->date_start)->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isWeekend();
            }, Carbon::parse($request->date_end)) + 1;

        $total_dias_atuais = $this->difTotal(Auth::id());

        // Verificar se o novo total de dias não ultrapassa o limite
        if (($total_dias_atuais - $dias_antigos + $dias_novos) > 22) {
            return redirect()->back()->with('error', 'Você excedeu o limite de dias de férias disponíveis.');
        }
        if ($vacation->vacation_approval_states_id == 2) {
            return redirect()->back()->with('error', 'Já não é possivel a edição destas ferias.');
        }


        if ($this->timeCollide($vacation->id, $vacation->user_id, $request->date_start, $request->date_end) &&
            $this->must_date($request->date_start, $request->date_end, $vacation->id)

           ){

            $vacation->date_start = $request->date_start;
            $vacation->date_end = $request->date_end;

            $roleId = auth()->user()->role_id;
            if ($roleId > 2 && $vacation->vacation_approval_states_id != null) {
                $vacation->vacation_approval_states_id = $request->vacation_approval_states_id;
                $vacation->approved_by = auth()->user()->id;
            } else {
                $vacation->vacation_approval_states_id = 3;
                $vacation->approved_by = null;
            }

            $vacation->save();

            // Criar uma nova notificação
            $notification = new Notification();
            $notification->user_id = $vacation->user_id; // Aqui você pode ajustar para o ID do usuário apropriado
            $notification->vacation_id = $vacation->id;
            $notification->state = false; // não lido
            $notification->save();

            // Enviar evento para Pusher após a atualização ser bem-sucedida
            event(new NotificationEvent('Vacation details updated successfully!', $notification->id));

            return redirect(url('/vacation'))->with('status', 'Atualizado com sucesso!');
        } else {
            return redirect()->back()->with('error', 'Houve um erro durante a atualização das férias, verifique se já tem 10 dias seguidos marcados ou se os dias pedidos já estão marcados!');
        }
    }



    public function destroy(Vacation $vacation)
    {
        $vacation = vacation::find($vacation->id);
        $vacation->delete();
        return redirect('/vacation')->with('success', 'Eliminado com sucesso!');

    }

    //Método import- importa os dados das férias de um ficheiro CSV
    public function import(Request $request)
    {
        $file = $request->file('file');

        if (!$file) {
            return redirect()->back()->with('error', 'Escolha um ficheiro antes de importar.');
        }

        $handle = fopen($file->getPathname(), 'r');

        if (!$handle) {
            return redirect()->back()->with('error', 'Erro ao abrir o ficheiro.');
        }

        // Ignorar a primeira linha (cabeçalhos)
        fgets($handle);

        // Verificar os dados do arquivo antes de truncar as tabelas
        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);

            //Separa a linha por ","
            $dataArray = explode(',', $line);

            //Busca o tamanho de $dataArray
            $tamanho = count($dataArray);

            //Verifica se o tamanho de $dataArray é igual a 7
            if($tamanho != 7){
                return redirect()->back()->with('error', 'Certifique-se que o ficheiro contem 7 colunas.');
            }

            //Remove as aspas
            $dataArray[0] = str_replace('"', '', $dataArray[0]);
            $dataArray[1] = str_replace('"', '', $dataArray[1]);
            $dataArray[2] = str_replace('"', '', $dataArray[2]);
            $dataArray[3] = str_replace('"', '', $dataArray[3]);
            $dataArray[4] = str_replace('"', '', $dataArray[4]);
            $dataArray[5] = str_replace('"', '', $dataArray[5]);
            $dataArray[6] = str_replace('"', '', $dataArray[6]);

            // Verifica se os IDs são inteiros
            if (!is_numeric($dataArray[0]) || !is_numeric($dataArray[1])) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de utilizador, de estado de Aprovação são números válidos.');
            }

            if($dataArray[2] != null){
                if (!is_numeric($dataArray[2])) {
                    return redirect()->back()->with('error', 'Certifique-se que os ID de Aprovado_Por é válido.');
                }
                $user = DB::table('users')->where('id', $dataArray[2])->first();
                if (!$user) {
                    return redirect()->back()->with('error', 'Certifique-se que o ID de Aprovado_Por existe na BD.');
                }
            }

            // Valida se os campos date_start e date_end são datas válidas
            if (strtotime($dataArray[3]) === false || strtotime($dataArray[4]) === false) {
                return redirect()->back()->with('error', 'Certifique-se que este ficheiro contem as data no formato AAAA-MM-DD.');
            }
        }

        // Fecha o arquivo após a verificação
        fclose($handle);

        // Abre novamente o arquivo para importar os dados
        $handle = fopen($file->getPathname(), 'r');

        // Ignora a primeira linha (cabeçalhos)
        fgets($handle);

        // Percorre o ficheiro e insere os dados na base de dados
        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);

            //Separa a linha por ","
            $dataArray = explode(',', $line);

            //Remove as aspas
            $dataArray[0] = str_replace('"', '', $dataArray[0]);
            $dataArray[1] = str_replace('"', '', $dataArray[1]);
            $dataArray[2] = str_replace('"', '', $dataArray[2]);
            $dataArray[3] = str_replace('"', '', $dataArray[3]);
            $dataArray[4] = str_replace('"', '', $dataArray[4]);
            $dataArray[5] = str_replace('"', '', $dataArray[5]);
            $dataArray[6] = str_replace('"', '', $dataArray[6]);


            $vacation = new Vacation();
            $vacation->user_id = $dataArray[0];
            $vacation->vacation_approval_states_id = $dataArray[1];
            if($dataArray[2] != null){
                $vacation->approved_by = $dataArray[2];
            }
            else{
                $vacation->approved_by = null;
            }
            $vacation->date_start = $dataArray[3];
            $vacation->date_end = $dataArray[4];
            $vacation->created_at = $dataArray[5];
            $vacation->updated_at = $dataArray[6];
            $vacation->save();
        }

        fclose($handle);

        // Retorna para a página anterior com uma mensagem de sucesso
        return redirect()->back()->with('success', 'Férias importadas com sucesso.');
    }

    //Método export- exporta os dados das férias para um ficheiro CSV
    public function export()
    {

        // Cria um vetor com todas as férias, define o nome do ficheiro e os cabeçalhos
        $vacations = Vacation::all();
        $csvFileName = 'vacations.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        // Cria um buffer para armazenar o conteúdo CSV temporariamente
        $output = fopen('php://temp', 'r+');

        fputcsv($output, ['Id_Utilizador', 'Id_Estado_Aprovacao_Ferias', 'Aprovado_Por', 'Data_Comeco', 'Data_Fim', 'Criado_A', 'Atualizado_A']); // Add more headers as needed

        //Percorre o vetor com as férias e escreve no ficheiro
        foreach ($vacations as $vacation) {
            fputcsv($output, [$vacation->user_id, $vacation->vacation_approval_states_id, $vacation->approved_by, $vacation->date_start, $vacation->date_end, $vacation->created_at, $vacation->updated_at]); // Add more fields as needed
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

