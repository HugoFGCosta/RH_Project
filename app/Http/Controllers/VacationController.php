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
    public function difTotal($user)
    {

        $vacation_start = Vacation::where('user_id', $user)->pluck('date_start');
        $vacation_end = Vacation::where('user_id', $user)->pluck('date_end');
        $total = 0;
        $totaldias = 0;
        foreach ($vacation_start as $x) {
            $total = $total + 1;
        }
        for ($i = 0; $total > $i; $i++) {
            $diff_date = Carbon::parse($vacation_start[$i])->diffInDaysFiltered(function (Carbon $remover) {
                return !$remover->isWeekend();
            }, Carbon::parse($vacation_end[$i]));
            $totaldias = $totaldias + $diff_date;
        }
        return $totaldias;
    }
    public function difInput($start, $end, $total): bool|int
    {

        $diff_date = Carbon::parse($start)->diffInDaysFiltered(function (Carbon $remover) {
            return !$remover->isWeekend();
        }, Carbon::parse($end));
        if ($total + $diff_date <= 22) {
            return true;
        } else
            return false;
    }

    public function index()
    {
        $totaldias = $this->difTotal(Auth::id());
        $roleId = auth()->user()->role_id;
        if ($roleId > 1) {
            $vacation = Vacation::with(['user', 'approvedBy'])->orderBy('id', 'asc')->get();
        } else {
            $vacation = Vacation::with(['user', 'approvedBy'])->orderBy('id', 'asc')->where('user_id', Auth::id())->get();
        }

        return view('pages.vacations.show', ['vacations' => $vacation])->with('totaldias', $totaldias)->with('role', $roleId);
    }


    public function create()
    {
        $roleId = auth()->user()->role_id;
        $totaldias = $this->difTotal(Auth::id());
        return view('pages.vacations.create')->with('totaldias', $totaldias)->with('role', $roleId);
        ;
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
            'date_start.required' => 'A data de inicio é obrigatória.',
            'date_start.after' => 'A data de inicio deve ser uma data após hoje.',
            'date_start.before' => 'A data de inicio deve ser antes da data de fim.',
            'date_end.required' => 'A data de fim é obrigatória.',
            'date_end.after' => 'A data de fim deve ser uma data após amanhã.',
            'date_end.after:date_start' => 'A data de fim deve ser após a data de inicio.',
        ];
        $validatedData = $request->validate([
            'date_start' => 'required|date|after:today|before:date_end',
            'date_end' => 'required|date|after:tomorrow|after:date_start',
        ], $messages);
        if ($this->difInput($request->date_start, $request->date_end, $this->difTotal(Auth::id())) != null && $this->timeCollide(0, auth::id(), $request->date_start, $request->date_end)) {

            $vacation = new Vacation();
            $vacation->user_id = Auth::id();
            $vacation->vacation_approval_states_id = 3;
            $vacation->approved_by = null;
            $vacation->date_start = $request->date_start;
            $vacation->date_end = $request->date_end;
            $vacation->save();
            return redirect(url('/vacation'))->with('status', 'Criado com sucesso!');
        } else
            return redirect(url('/vacations/create'))->with('status', 'O Utilizador já marcou ferias neste(s) dia(s)!!');

    }


    public function show(Vacation $vacation)
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
        $validatedData = $request->validate([
            'date_start' => 'required|date|after:today|before:date_end',
            'date_end' => 'required|date|after:tomorrow|after:date_start',
        ], $messages);

        $roleId = auth()->user()->role_id;
        if ($this->timeCollide($vacation->id, $vacation->user_id, $request->date_start, $request->date_end)) {

            $vacation = Vacation::find($vacation->id);
            if ($roleId >= 2 && $vacation->vacation_approval_states_id != $request->vacation_approval_states_id) {
                $vacation->vacation_approval_states_id = $request->vacation_approval_states_id;
                $vacation->approved_by = auth()->user()->id;
            } else {
                $vacation->vacation_approval_states_id = 3;
                $vacation->approved_by = null;

            }
            $vacation->date_start = $request->date_start;
            $vacation->date_end = $request->date_end;

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
        } else
            return redirect('/vacation')->with('status', 'O Utilizador já marcou ferias neste(s) dia(s)!');
    }


    public function destroy(Vacation $vacation)
    {
        $vacation = vacation::find($vacation->id);
        $vacation->delete();
        return redirect('vacation')->with('status', 'Eliminado com sucesso!');

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

        // Armazenar mensagens de erro
        $errors = [];

        // Verificar os dados do arquivo antes de truncar as tabelas
        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);

            if (count($data) != 5) {
                return redirect()->back()->with('error', 'Certifique-se que este ficheiro contem informações de férias.');
            }

            // Verifica se os IDs são inteiros
            if (!is_numeric($data[0]) || !is_numeric($data[1]) || !is_numeric($data[2])) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de utilizador, de estado de Aprovação, e Aprovado_Por são números válidos.');
            }

            // Valida se os campos date_start e date_end são datas válidas
            if (strtotime($data[3]) === false || strtotime($data[4]) === false) {
                return redirect()->back()->with('error', 'Certifique-se que este ficheiro contem as data no formato AAAA-MM-DD.');
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
}
