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
        $maisdez=0;
        $maisdez += $this->date_require($date_start_request,$date_end_request);


        foreach ($vacations as &$vacation){

           $maisdez += $this->date_require($vacation->date_start,$vacation->date_end);
           if(end($vacations)){
               dd($maisdez);
           }
                if ($current_table == $vacation->id) { //check if it's the same table from edit
                    print '<p> same table </p>' . $vacation->id;
                } else {

                    $date_start = date('Y-m-d', strtotime($vacation->date_start));
                    $date_end = date('Y-m-d', strtotime($vacation->date_end));
                    if ($this->dateValidation($date_start, $date_end, $date_start_request, $date_end_request)) {
                       return false;
                    }

                }

        }
        if($maisdez >= 1 ){
        return true;
    }else
       return false;
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
    public function difInput($start, $end)
    {

        $diff_date = Carbon::parse($start)->diffInDaysFiltered(function (Carbon $remover){
            return !$remover->isWeekend();
        },Carbon::parse($end));
        if ($this->difTotal(Auth::id()) + $diff_date <= 22 ){
            return true;
        }

        else
            return false;
    }

    public function date_require($start, $end) : int
    {


        $diff_date =  Carbon::parse($start)->diffInDaysFiltered(function (Carbon $remover) {
            return !$remover->isWeekend();
        }, Carbon::parse($end));

        if ( $diff_date >= 10) {
            return 1;
        } else {
            return 0;
        }
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
            'date_start.required' => 'The start date is required.',
            'date_start.after' => 'The start date must be a date after today.',
            'date_start.before' => 'The start date must be before the end date.',
            'date_end.required' => 'The end date is required.',
            'date_end.after' => 'The end date must be a date after tomorrow.',
            'date_end.after:date_start' => 'The end date must be after the start date.',
        ];
        $validatedData = $request->validate([
            'date_start' => 'required|date|after:today|before:date_end',
            'date_end' => 'required|date|after:tomorrow|after:date_start',
        ], $messages);
        if($this->difInput($request->date_start,$request->date_end) && $this->timeCollide(0,auth::id(),$request->date_start,$request->date_end)){

            $vacation = new Vacation();
            $vacation->user_id = Auth::id();
            $vacation->vacation_approval_states_id = 3;
            $vacation->approved_by = null;
            $vacation->date_start =$request->date_start ;
            $vacation->date_end = $request->date_end ;
            $vacation->save();
            return redirect(url('/vacation'))->with('status','Item created successfully!');
        }
        else return redirect(url('/vacations/create'))->with($messages);

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
            'date_start.required' => 'The start date is required.',
            'date_start.after' => 'The start date must be a date after today.',
            'date_start.before' => 'The start date must be before the end date.',
            'date_end.required' => 'The end date is required.',
            'date_end.after' => 'The end date must be a date after tomorrow.',
            'date_end.after:date_start' => 'The end date must be after the start date.',
        ];
        $validatedData = $request->validate([
            'date_start' => 'required|date|after:today|before:date_end',
            'date_end' => 'required|date|after:tomorrow|after:date_start',
        ], $messages);

        $roleId = auth()->user()->role_id;
        if($this->timeCollide($vacation->id,$vacation->user_id,$request->date_start,$request->date_end) && $this->difInput($request->date_start , $request->date_end ,$this->difTotal(Auth::id()))){


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
            return redirect(url('/vacation'))->with('status', 'Item edited successfully!');
        }
        else
            return redirect('/vacation')->with('status', 'O ' . auth()->user()->name . ' já marcou ferias neste(s) dia(s)!');
    }



    public function destroy(Vacation $vacation)
    {
        $vacation = vacation::find($vacation->id);
        $vacation->delete();
        return redirect('vacation')->with('status','Item deleted successfully!');

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
