<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Requests\UpdateAbsenceRequest;
use App\Models\Presence;
use App\Models\User;
use App\Models\User_Shift;
use App\Models\Work_Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class AbsenceController extends Controller
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
    public function store(StoreAbsenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Absence $absence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Absence $absence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAbsenceRequest $request, Absence $absence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absence $absence)
    {
        //
    }

    public function import(Request $request)
    {
        $file = $request->file('file');

        // Se não for escolhido nenhum ficheiro, mostra uma mensagem de erro
        if (!$file) {
            return redirect()->back()->with('error', 'Escolha um ficheiro antes de importar.');
        }

        $handle = fopen($file->getPathname(), 'r');

        // Se houver erro ao abrir o arquivo, mostra uma mensagem de erro
        if (!$handle) {
            return redirect()->back()->with('error', 'Erro ao abrir o ficheiro.');
        }

        // Ignora a primeira linha do ficheiro
        fgets($handle);

        // Desativa as verificações de chave estrangeira
        Schema::disableForeignKeyConstraints();

        // Trunca a tabela de faltas
        DB::table('absences')->truncate();

        // Reabilita as verificações de chave estrangeira
        Schema::enableForeignKeyConstraints();

        $errors = [];

        // Percorre o ficheiro e insere os dados na base de dados
        while (($line = fgets($handle)) !== false) {

            $data = str_getcsv($line);

            // Verifica se há exatamente 5 campos
            if (count($data) != 5) {
                return redirect()->back()->with('error', 'Certifique-se que este ficheiro contém informações de faltas.');
            }

            // Verifica se os IDs são inteiros
            if (!is_int($data[0]) || !is_int($data[1]) || !is_int($data[2])) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de utilizador, estado de falta e aprovador são números válidos.');
            }

            // Verifica se o campo absence_date é uma data válida
            if (strtotime($data[3]) === false) {
                return redirect()->back()->with('error', 'A data fornecida não é válida.');
            }

            // Verifica se a justificativa não pode ser convertida para uma data válida
            if (strtotime($data[4]) !== false) {
                return redirect()->back()->with('error', 'A justificativa não deve ser uma data.');
            }

            // Verifica se o ID de estado de aprovação de falta está entre 1 e 3
            if ($data[1] < 1 || $data[1] > 3){
                return redirect()->back()->with('error', 'Certifique-se que os IDs de estado são números válidos.');
            }

            Absence::create([
                'user_id' => $data[0],
                'absence_states_id' => $data[1],
                'approved_by' => $data[2],
                'absence_date' => $data[3],
                'justification' => $data[4],
            ]);
        }

        fclose($handle);

        // Se houver erros, redireciona de volta com as mensagens de erro
        if (!empty($errors)) {
            return redirect()->back()->with('error', $errors);
        }

        // Redireciona para a página anterior com uma mensagem de sucesso
        return redirect()->back()->with('success', 'Faltas importadas com sucesso.');
    }


    public function export(){

        //

        // Define o nome do ficheiro e os cabeçalhos
        $absences = Absence::all();
        $csvFileName = 'absences.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        //Escreve os cabeçalhos no ficheiro
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['User_id','Absence_states_id', 'Approved_by','Absence_date','Justification']);

        //Para cada falta insere uma linha no ficheiro
        foreach ($absences as $absence) {
            fputcsv($handle, [$absence->user_id,$absence->absence_states_id, $absence->approved_by,$absence->absence_date,$absence->justification]); // Add more fields as needed
        }

        // Fecha o ficheiro
        fclose($handle);

        return Response::make('', 200, $headers);
    }

    public function marcaFalta(){

        $users = User::all();

        foreach ($users as $user){

            $user_shift = User_Shift::where('user_id', $user->id)->latest()->first();

            $work_shiftId = $user_shift->id;

            //Vai buscar o horário do utilizador
            $work_shift= Work_Shift::where('id', $work_shiftId)->first();

            //Vai buscar a hora atual
            $currentTime = Carbon::now()->format('H:i:s');

            //Vai buscar todas as presenças
            $presences = Presence::all();
            
        }

    }

}
