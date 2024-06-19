<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    public function dateValidation($date_start,$date_end,$date_start_request,$date_end_request) : bool
    {

        if((($date_start >= $date_start_request) && ($date_start <= $date_end_request) ) || ( ($date_end >= $date_start_request) &&($date_end <= $date_end_request)  )){
            return true;
        }
        else return false;
    }

    public function timeCollide($current_table,$userId,$date_start_request,$date_end_request)
    {

        $vacations=Vacation::where('user_id',$userId)->get();
        $date_start_request= date('Y-m-d', strtotime($date_start_request));
        $date_end_request= date('Y-m-d', strtotime($date_end_request));

        foreach ($vacations as &$vacation){

            if($current_table == $vacation->id){ //check if it's the same table from edit
                print '<p> same table </p>';
            }
            else{
                $vacation->date_start= date('Y-m-d', strtotime($vacation->date_start));
                $vacation->date_end= date('Y-m-d', strtotime($vacation->date_end));
                if($this->dateValidation($vacation->date_start,$vacation->date_end,$date_start_request,$date_end_request)){
                    print '<p> same table </p>';
                    return false;
                }
                else {
                    print '<p></p>';
                    print '$date_start :'. $vacation->date_start;
                    print '<p></p>';
                    print '$date_end :'.$vacation->date_end;
                    print '<p></p>';
                    print '$date_start_request :'. $date_start_request;
                    print '<p></p>';
                    print  '$date_end_request :' .  $date_end_request;
                    print '<p></p>';
                }
            }
        }

        return true;
    }
    public function difTotal($user)
    {

        $vacations= Vacation::where('user_id',$user)->get();

        $total= 0;
        $totaldias=0;
        foreach ($vacations as $vacation){
            $total = $total +1;
            $diff_date = Carbon::parse($vacation->date_start)->diffInDaysFiltered(function (Carbon $remover){
                return !$remover->isWeekend();
            },Carbon::parse($vacation->date_end));
            $totaldias=$totaldias+$diff_date;
        }
        return $totaldias;
    }
    public function difInput($start, $end,$total): bool|int
    {

        $diff_date = Carbon::parse($start)->diffInDaysFiltered(function (Carbon $remover){
            return !$remover->isWeekend();
        },Carbon::parse($end));
        if ($total + $diff_date <= 22 ){
            return true;
        }
        else
            return false;
    }

    public function index()
    {
        $totaldias = $this->difTotal(Auth::id());
        $roleId = auth()->user()->role_id;
        if ($roleId >1)
            $vacation = vacation::with('user')->orderBy('id', 'asc')->paginate(15);
        else
            $vacation = vacation::with('user')->orderBy('id', 'asc')->where('user_id',Auth::id())->paginate(15);

        return view('pages.vacations.show',['vacations' => $vacation])->with('totaldias',$totaldias)->with('role',$roleId);

    }
    public function create()
    {
        $roleId = auth()->user()->role_id;
        $totaldias = $this->difTotal(Auth::id());
        return view ('pages.vacations.create')->with('totaldias', $totaldias)->with('role',$roleId);
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
        if($this->difInput($request->date_start , $request->date_end ,$this->difTotal(Auth::id()))!=null && $this->timeCollide(0,auth::id(),$request->date_start,$request->date_end)){

            $vacation = new Vacation();
            $vacation->user_id = Auth::id();
            $vacation->vacation_approval_states_id = 3;
            $vacation->approved_by = null;
            $vacation->date_start =$request->date_start ;
            $vacation->date_end = $request->date_end ;
            $vacation->save();
            return redirect(url('/vacation'))->with('status','Criado com sucesso!');
        }
        else return redirect(url('/vacations/create'))->with('status','O Utilizador já marcou ferias neste(s) dia(s)!!');

    }


    public function show(Vacation $vacation)
    {
        //
    }

    public function edit(Vacation $vacation)
    {
        $roleId = Auth::user()->role_id;
        $role_id_table= Vacation::with('User')->where('id',$vacation->id)->get();

        $totaldias= $this->difTotal($roleId);
        return view('pages.vacations.edit', ['vacations' => $vacation])->with('totaldias', $totaldias)->with('role',$roleId)->with('role_id_table',$role_id_table[0]->user->role_id);

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
        if($this->timeCollide($vacation->id,$vacation->user_id,$request->date_start,$request->date_end)){

            $vacation = Vacation::find($vacation->id);
            if($roleId >= 2 && $vacation->vacation_approval_states_id != $request->vacation_approval_states_id){
                $vacation->vacation_approval_states_id = $request->vacation_approval_states_id;
                $vacation->approved_by= auth()->user()->id;
            }
            else{
                $vacation->vacation_approval_states_id = 3;
                $vacation->approved_by= null;

            }
            $vacation->date_start = $request->date_start;
            $vacation->date_end = $request->date_end;

            $vacation->save();
            return redirect(url('/vacation'))->with('status', 'Atualizado com sucesso!');
        }
        else
            return redirect('/vacation')->with('status', 'O Utilizador já marcou ferias neste(s) dia(s)!');
    }



    public function destroy(Vacation $vacation)
    {
        $vacation = vacation::find($vacation->id);
        $vacation->delete();
        return redirect('vacation')->with('status','Eliminado com sucesso!');

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
                return redirect()->back()->with('error', 'Certifique-se que os IDs de utilizador são números válidos.');
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
            ]);
        }

        fclose($handle);

        // Retorna para a página anterior com uma mensagem de sucesso
        return redirect()->back()->with('success', 'Férias importadas com sucesso.');
    }

    public function export(){

        // Cria um vetor com todas as férias, define o nome do ficheiro e os cabeçalhos
        $vacations = Vacation::all();
        $csvFileName = 'vacations.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['User_id','Vacation_approval_states_id', 'Approved_by','Date_start','Date_end']); // Add more headers as needed

        //Percorre o vetor com as férias e escreve no ficheiro
        foreach ($vacations as $vacation) {
            fputcsv($handle, [$vacation->user_id,$vacation->vacation_approval_states_id, $vacation->approved_by,$vacation->date_start,$vacation->date_end]); // Add more fields as needed
        }

        fclose($handle);

        // Retorna o ficheiro
        return Response::make('', 200, $headers);
    }
}

