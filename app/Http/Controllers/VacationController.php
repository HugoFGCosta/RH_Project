<?php

namespace App\Http\Controllers;

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

        $vacation_start= Vacation::where('user_id',$user)->pluck('date_start');
        $vacation_end = Vacation::where('user_id',$user)->pluck('date_end');
        $total= 0;
        $totaldias=0;
        foreach ($vacation_start as $x){
            $total = $total +1;
        }
        for($i=0;$total>$i;$i++){
            $diff_date = Carbon::parse($vacation_start[$i])->diffInDaysFiltered(function (Carbon $remover){
                return !$remover->isWeekend();
            },Carbon::parse($vacation_end[$i]));
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
    $vacation = vacation::with('user')->orderBy('id', 'asc')->paginate(3);
else
    $vacation = vacation::with('user')->orderBy('id', 'asc')->where('user_id',Auth::id())->paginate(3);

return view('pages.vacations.show',['vacations' => $vacation])->with('totaldias',$totaldias)->with('role',$roleId);

    }
    public function create()
    {
        $roleId = auth()->user()->role_id;
        $totaldias = $this->difTotal(Auth::id());
        return view ('pages.vacations.create')->with('totaldias', $totaldias)->with('role',$roleId);;
    }

    public function store(StoreVacationRequest $request)
    {
        $request->validate([
            'date_start' => 'required|after:today,before:date_end' ,
            'date_end' => 'required|after:tomorrow|after:date_start'
        ]);
       if($this->difInput($request->date_start , $request->date_end ,$this->difTotal(Auth::id()))!=null){

           $vacation = new Vacation();
           $vacation->user_id = Auth::id();
           $vacation->vacation_approval_states_id = 3;
           $vacation->approved_by = null;
           $vacation->date_start =$request->date_start ;
           $vacation->date_end = $request->date_end ;
           $vacation->save();
           return redirect(url('/vacation'))->with('status','Item created successfully!');
    }
else return redirect(url('/vacations/create'))->with('status','error!');
    }


    public function show(Vacation $vacation)
    {
        //
    }

    public function edit(Vacation $vacation)
    {
    //   print   Vacation::with('user')->where('role_id', auth::id())->pluck("role_id");
        $roleId = Auth::user()->role_id;
        $totaldias= $this->difTotal($roleId);
        return view('pages.vacations.edit', ['vacations' => $vacation])->with('totaldias', $totaldias)->with('role',$roleId);;

    }

    public function update(UpdateVacationRequest $request, Vacation $vacation)
    {
        $request->validate([
            'date_start' => 'required|after:today,before:date_end' ,
            'date_end' => 'required|after:tomorrow|after:date_start',

        ]);
        $roleId = auth()->user()->role_id;
       if($this->difInput($request->date_start , $request->date_end ,$this->difTotal(Auth::user())) ) {


           $vacation = Vacation::find($vacation->id);
           //  if(find($vacation->approved_by))
           $vacation->approved_by = null;

           if($roleId > 2){
               $vacation->vacation_approval_states_id = $request->vacation_approval_states_id;
           $vacation->approved_by=auth::id();
           }
           else
               $vacation->vacation_approval_states_id = 3;

           $vacation->date_start = $request->date_start;
           $vacation->date_end = $request->date_end;

           $vacation->save();
           print $roleId;
           return redirect(url('/vacation'))->with('status', 'Item edited successfully!');
       }
       else
           return redirect('/vacation')->with('status', 'Erro!');
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
                'created_at'=>now(),
                'updated_at'=>now()
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
        fputcsv($handle, ['Id_Utilizador','Id_Estado_Aprovacao_Falta', 'Aprovado_Por','Data_Comeco','Data_Fim','Criado_A','Atualizado_A']); // Add more headers as needed

        //Percorre o vetor com as férias e escreve no ficheiro
        foreach ($vacations as $vacation) {
            fputcsv($handle, [$vacation->user_id,$vacation->vacation_approval_states_id, $vacation->approved_by,$vacation->date_start,$vacation->date_end,$vacation->created_at,$vacation->updated_at]); // Add more fields as needed
        }

        fclose($handle);

        // Retorna o ficheiro
        return Response::make('', 200, $headers);
    }
}
