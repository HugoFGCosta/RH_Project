<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Http\Requests\StorePresenceRequest;
use App\Http\Requests\UpdatePresenceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class PresenceController extends Controller
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
    public function store(StorePresenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Presence $presence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presence $presence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePresenceRequest $request, Presence $presence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presence $presence)
    {
        //
    }

    public function import(Request $request)
    {

        // Se não for escolhido nenhum ficheiro mostra uma mensagem de erro
        $file = $request->file('file');

        if(!$file) {
            return redirect()->back()->with('error', 'Please choose a file before importing.');
        }

        $handle = fopen($file->getPathname(), 'r');

        
        // Ignorar a primeira linha (cabeçalhos)
        fgets($handle);

        // Desativa as verificações de chave estrangeira
        Schema::disableForeignKeyConstraints();

        // Trunca as tabelas
        DB::table('presences')->truncate();

        // Reabilita as verificações de chave estrangeira
        Schema::enableForeignKeyConstraints();

        // Ler o ficheiro linha a linha e insere na base de dados
        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);

            if(count($data) != 7) {
                return redirect()->back()->with('error', 'Invalid file format.');
            }

            Presence::create([
                'user_id' => $data[0],
                'first_start' => $data[1],
                'first_end' => $data[2],
                'second_start' => $data[3],
                'second_end' => $data[4],
                'extra_hour' => $data[5],
                'effective_hour' => $data[6],
            ]);

        }

        fclose($handle);

        // Redireciona para a página anterior com uma mensagem de sucesso
        return redirect()->back()->with('success', 'CSV file imported successfully.');
    }

    public function export(){

        // Define o nome do ficheiro e os cabeçalhos
        $presences = Presence::all();
        $csvFileName = 'presences.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['User_id','First_start', 'First_end','Second_start','Second_end','Extra_hour','Effective_hour']); 

        //Para cada presença insere uma linha no ficheiro
        foreach ($presences as $presence) {
            fputcsv($handle, [$presence->user_id,$presence->first_start, $presence->first_end,$presence->second_start,$presence->second_end,$presence->extra_hour,$presence->effective_hour]); // Add more fields as needed
        }

        fclose($handle);

        // Retorna o ficheiro
        return Response::make('', 200, $headers);
    }
}
