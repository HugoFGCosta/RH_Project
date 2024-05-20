<?php

namespace App\Http\Controllers;

use App\Models\Vacation;
use App\Http\Requests\StoreVacationRequest;
use App\Http\Requests\UpdateVacationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class VacationController extends Controller
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
    public function store(StoreVacationRequest $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVacationRequest $request, Vacation $vacation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacation $vacation)
    {
        //
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
