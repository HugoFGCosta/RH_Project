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

class VacationController extends Controller
{
    const MAX_VACATION_DAYS = 22;
    const MIN_CONSECUTIVE_DAYS = 10;

    public function must_date($start, $end, $table_id)
    {
        $vacations = Vacation::where('user_id', auth::id())
            ->whereIn('vacation_approval_states_id', [3, 1])
            ->get(['id', 'date_start', 'date_end']);

        $totaldias = 0;
        $validar = false;

        foreach ($vacations as $vacation) {
            if ($vacation->id == $table_id) {
                continue;
            }

            $start_date = $vacation->date_start;
            $end_date = $vacation->date_end;

            $diff_date = Carbon::parse($start_date)->diffInDaysFiltered(function (Carbon $remover) {
                    return !$remover->isWeekend();
                }, Carbon::parse($end_date)) + 1;

            $totaldias += $diff_date;

            if ($diff_date >= self::MIN_CONSECUTIVE_DAYS) {
                $validar = true;
            }
        }

        $new_diff_date = Carbon::parse($start)->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($end)) + 1;

        if ((self::MAX_VACATION_DAYS - $totaldias - $new_diff_date) >= self::MIN_CONSECUTIVE_DAYS || $new_diff_date >= self::MIN_CONSECUTIVE_DAYS) {
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
                }, Carbon::parse($vacation->date_end)) + 1;
            $total_dias += $diff_date;
        }

        return $total_dias;
    }

    public function difInput($start, $end, $aprovacao, $start_anterior, $end_anterior)
    {
        $diff_date_anterior = Carbon::parse($start_anterior)->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($end_anterior)) + 1;
        $diff_date = Carbon::parse($start)->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($end)) + 1;

        if ($aprovacao == 2) {
            return ($this->difTotal(Auth::id()) - $diff_date);
        } else {
            return ($this->difTotal(Auth::id()) + $diff_date - $diff_date_anterior);
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

    public function show()
    {
        $totaldias = $this->difTotal(Auth::id());
        $roleId = auth()->user()->role_id;
        $vacation = Vacation::where('user_id', Auth::id())->orderBy('id', 'asc')->get();
        return view('pages.vacations.show', ['vacations' => $vacation])->with('totaldias', $totaldias)->with('role', $roleId);
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

        $new_diff_date = Carbon::parse($request->date_start)->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($request->date_end)) + 1;

        $total_dias = $this->difTotal(Auth::id());

        if ($total_dias + $new_diff_date > self::MAX_VACATION_DAYS) {
            return redirect(url('/vacations/create'))->with('error', 'Não pode marcar mais de 22 dias de férias!');
        }

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

        $validatedData = $request->validate([
            'date_start' => 'required|date|after:today',
            'date_end' => 'required|date|after_or_equal:date_start',
        ], $messages);

        $current_user_id = Auth::id();
        $total_dias_atuais = $this->difTotal($current_user_id);

        $diff_date = Carbon::parse($request->date_start)->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($request->date_end)) + 1;

        $diff_date_anterior = Carbon::parse($vacation->date_start)->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($vacation->date_end)) + 1;

        $novo_total_dias = $total_dias_atuais - $diff_date_anterior + $diff_date;

        if ($novo_total_dias > self::MAX_VACATION_DAYS) {
            return redirect('/vacation')->with('status', 'Não pode marcar mais do que 22 dias de férias!');
        }

        $validar = false;
        if ($diff_date >= self::MIN_CONSECUTIVE_DAYS) {
            $validar = true;
        } else {
            $vacations = Vacation::where('user_id', $current_user_id)
                ->where('id', '<>', $vacation->id)
                ->whereIn('vacation_approval_states_id', [3, 1])
                ->get(['date_start', 'date_end']);

            foreach ($vacations as $v) {
                $diff_date_existing = Carbon::parse($v->date_start)->diffInDaysFiltered(function (Carbon $remover) {
                        return !$remover->isWeekend();
                    }, Carbon::parse($v->date_end)) + 1;

                if ($diff_date_existing >= self::MIN_CONSECUTIVE_DAYS) {
                    $validar = true;
                    break;
                }
            }
        }

        if (!$validar) {
            return redirect('/vacation')->with('status', 'Tem que haver uma marcação de pelo menos 10 dias consecutivos!');
        }

        if ($this->timeCollide($vacation->id, $current_user_id, $request->date_start, $request->date_end)) {
            $vacation->date_start = $request->date_start;
            $vacation->date_end = $request->date_end;
            $vacation->vacation_approval_states_id = 3;
            $vacation->approved_by = null;

            if (Auth::user()->role_id == 3) {
                if ($request->has('vacation_approval_states_id')) {
                    $vacation->vacation_approval_states_id = $request->vacation_approval_states_id;
                    if ($request->vacation_approval_states_id != 3) {
                        $vacation->approved_by = Auth::id();
                    } else {
                        $vacation->approved_by = null;
                    }
                }
            }

            $vacation->save();

            $notification = new Notification();
            $notification->user_id = $vacation->user_id;
            $notification->vacation_id = $vacation->id;
            $notification->state = false;
            $notification->save();

            event(new NotificationEvent('Vacation details updated successfully!', $notification->id));

            return redirect(url('/vacation'))->with('status', 'Atualizado com sucesso!');
        } else {
            return redirect('/vacation')->with('status', 'O Utilizador já marcou férias neste(s) dia(s)!');
        }
    }




    public function destroy(Vacation $vacation)
    {
        $vacation->delete();
        return redirect('/vacation')->with('error', 'Eliminado com sucesso!');
    }

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

            if (count($data) != 5) {
                return redirect()->back()->with('error', 'Certifique-se que este ficheiro contém informações de férias.');
            }

            // Verifica se os IDs são inteiros
            if (!is_numeric($data[0]) || !is_numeric($data[1]) || !is_numeric($data[2])) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de utilizador, de estado de Aprovação, e Aprovado_Por são números válidos.');
            }

            // Valida se os campos date_start e date_end são datas válidas
            if (strtotime($data[3]) === false || strtotime($data[4]) === false) {
                return redirect()->back()->with('error', 'Certifique-se que este ficheiro contém as datas no formato AAAA-MM-DD.');
            }
        }

        // Fecha o arquivo após a verificação
        fclose($handle);

        // Desativa as verificações de chave estrangeira
        Schema::disableForeignKeyConstraints();

        // Trunca as tabelas
        DB::table('vacations')->truncate();

        // Reabilita as verificações de chave estrangeira
        Schema::enableForeignKeyConstraints();

        // Abre novamente o arquivo para importar os dados
        $handle = fopen($file->getPathname(), 'r');

        // Ignora a primeira linha (cabeçalhos)
        fgets($handle);

        // Percorre o ficheiro e insere os dados na base de dados
        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);

            Vacation::create([
                'user_id' => $data[0],
                'vacation_approval_states_id' => $data[1],
                'approved_by' => $data[2],
                'date_start' => $data[3],
                'date_end' => $data[4],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        fclose($handle);

        // Retorna para a página anterior com uma mensagem de sucesso
        return redirect()->back()->with('success', 'Férias importadas com sucesso.');
    }

    public function export()
    {
        // Cria um vetor com todas as férias, define o nome do ficheiro e os cabeçalhos
        $vacations = Vacation::all();
        $csvFileName = 'vacations.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Id_Utilizador', 'Id_Estado_Aprovacao_Falta', 'Aprovado_Por', 'Data_Comeco', 'Data_Fim', 'Criado_A', 'Atualizado_A']); // Add more headers as needed

        //Percorre o vetor com as férias e escreve no ficheiro
        foreach ($vacations as $vacation) {
            fputcsv($handle, [$vacation->user_id, $vacation->vacation_approval_states_id, $vacation->approved_by, $vacation->date_start, $vacation->date_end, $vacation->created_at, $vacation->updated_at]); // Add more fields as needed
        }

        fclose($handle);

        // Retorna o ficheiro
        return Response::make('', 200, $headers);
    }

    public function approve($id)
    {
        $vacation = Vacation::find($id);

        if (!$vacation) {
            return redirect('/vacation')->with('error', 'Férias não encontradas!');
        }

        $vacation->vacation_approval_states_id = 1; // Estado aprovado
        $vacation->approved_by = Auth::id();
        $vacation->save();

        // Criar uma nova notificação
        $notification = new Notification();
        $notification->user_id = $vacation->user_id;
        $notification->vacation_id = $vacation->id;
        $notification->state = false; // não lido
        $notification->save();

        // Enviar evento para Pusher após a atualização ser bem-sucedida
        event(new NotificationEvent('Vacation approved successfully!', $notification->id));

        return redirect('/vacation')->with('status', 'Férias aprovadas com sucesso!');
    }

    public function reject($id)
    {
        $vacation = Vacation::find($id);

        if (!$vacation) {
            return redirect('/vacation')->with('error', 'Férias não encontradas!');
        }

        $vacation->vacation_approval_states_id = 2; // Estado rejeitado
        $vacation->approved_by = Auth::id();
        $vacation->save();

        // Criar uma nova notificação
        $notification = new Notification();
        $notification->user_id = $vacation->user_id;
        $notification->vacation_id = $vacation->id;
        $notification->state = false; // não lido
        $notification->save();

        // Enviar evento para Pusher após a atualização ser bem-sucedida
        event(new NotificationEvent('Vacation rejected successfully!', $notification->id));

        return redirect('/vacation')->with('status', 'Férias rejeitadas com sucesso!');
    }
}
