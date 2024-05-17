<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Requests\UpdateAbsenceRequest;
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
        //
        $file = $request->file('file');

        // Se não for escolhido nenhum ficheiro mostra uma mensagem de erro
        if(!$file) {
            return redirect()->back()->with('error', 'Escolha um ficheiro antes de importar.');
        }

        $handle = fopen($file->getPathname(), 'r');


        // Ignora a primeira linha do ficheiro
        fgets($handle);

        // Desativa as verificações de chave estrangeira
        Schema::disableForeignKeyConstraints();

        // Trunca as tabelas
        DB::table('absences')->truncate();

        // Reabilita as verificações de chave estrangeira
        Schema::enableForeignKeyConstraints();


        //Percorre o ficheiro e insere os dados na base de dados
        while (($line = fgets($handle)) !== false) {

            $data = str_getcsv($line);

            // Verifica se há exatamente 5 campos
            if(count($data) != 5) {
                return redirect()->back()->with('error', 'Certifique-se que este ficheiro contém informações de faltas.');
            }

            // Verifica se os IDs são inteiros
            if (!is_numeric($data[0]) || !is_numeric($data[1]) || !is_numeric($data[2])) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de utilizador, estado de falta e aprovador são números válidos.');
            }

            // Verifica se o campo absence_date é uma data válida
            if (strtotime($data[3]) === false) {
                return redirect()->back()->with('error', 'A data fornecida não é válida.');
            }

            // Verifica se justification pode ser convertido para uma data válida
            if (strtotime($data[4]) !== false) {
                return redirect()->back()->with('error', 'A justificativa não deve ser uma data.');
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

        // Redireciona para a página anterior com uma mensagem de sucesso
        return redirect()->back()->with('success', 'Faltas importadas com Successo.');

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
}
