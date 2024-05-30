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

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function dif()
    {
        $user = Auth::id();
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
    public function index()
    {
  //  ISTO ESTA A FAZER A DIFF DE DATE START E END SEM WEEKENDS

   $totaldias = $this->dif();

       $vacation = vacation::with('user')->orderBy('id', 'asc')->paginate(3);
       return view('pages.vacations.show',['vacations' => $vacation])->with('totaldias',$totaldias);




        //  $diff=$vacation_start[2]->diffInDays($vacation_start[2]);
        //   $dias_ferias= Vacation::select('date_start','date_end')->where('user_id',$user);
        //   $dias_ferias= Vacation::pluck('date_start','date_end')->where('user_id',$user);
        //$dias_ferias = Vacation::pluck('date_start','date_end')->where('user_id',$user);
    //     Vacation::all('date_start','date_end')('user_id')->where($user);
      //   dd( $vacation_end);
      /*foreach(explode(':',$dias_ferias)as $dias[$i]){
       //   print $dias[$i];
         // print '<br>';
          $i++;
      }
      dd($dias);
           Destination::orderByDesc(
                  Flight::select('arrived_at')
                      ->whereColumn('destination_id', 'destinations.id')
                      ->orderByDesc('arrived_at')
                      ->limit(1)
              )->get();
            for($i=0;$i<2;$i++){
                  $diff = now()->diffInDays(Carbon::parse($date));
                 // print $diff[$i];
              }
      */
//print $dias_ferias;

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $totaldias = $this->dif();
        return view ('pages.vacations.create')->with('totaldias', $totaldias);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVacationRequest $request)
    {
        $request->validate([
            'date_start' => 'required|after:today,before:date_end' ,
            'date_end' => 'required|after:tomorrow|after:date_start'
        ]);
        $vacation = new Vacation();
        $vacation->user_id = Auth::id();
        $vacation->vacation_approval_states_id = 3;
        $vacation->approved_by = null;
        $vacation->date_start =$request->date_start ;
        $vacation->date_end = $request->date_end ;
        $vacation->save();
        return redirect(url('/vacation'))->with('status','Item created successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Vacation $vacation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacation $vacation)
    {
        $totaldias= $this->dif();
        return view('pages.vacations.edit', ['vacations' => $vacation])->with('totaldias', $totaldias);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVacationRequest $request, Vacation $vacation)
    {
        $request->validate([
            'date_start' => 'required|after:today,before:date_end' ,
            'date_end' => 'required|after:tomorrow|after:date_start',
            'vacation_approval_states_id' => 'required'
        ]);
        $vacation = vacation::find($vacation->id);
        //  if(find($vacation->approved_by))
        $vacation->approved_by = null;
        $vacation->vacation_approval_states_id = $request->vacation_approval_states_id;
        $vacation->date_start = $request->date_start;
        $vacation->date_end = $request->date_end;


        $vacation->save();
        return redirect(url('/vacation'))->with('status','Item edited successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
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
