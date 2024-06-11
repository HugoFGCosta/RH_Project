<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Absence_State;
use App\Models\Justification;
use App\Http\Requests\StoreJustificationRequest;
use App\Http\Requests\UpdateJustificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class JustificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $justifications = Justification::all();

        return view ('pages.justifications.index',['justifications'=>$justifications]);
    }

    public function pendingJustifications()
    {
        //
        $justifications = Justification::all();

        return view ('pages.justifications.index',['justifications'=>$justifications]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($absence)
    {
        //
        $absences = Absence::all();
        $absencefound = $absences->find($absence);


        //encontra o estado da ausência
        $states = Absence_State::all();
        $statefound = $states->find($absencefound->absence_states_id);

        //calcula a duração da ausência em horas
        $datetime1 = new \DateTime($absencefound->absence_start_date);
        $datetime2 = new \DateTime($absencefound->absence_end_date);
        $interval = $datetime1->diff($datetime2);
        $duration = $interval->format('%H:%I');

        return view ('pages.justifications.create', ['absence' => $absencefound, 'state' => $statefound, 'duration' => $duration]);
    }

    /**
     * Store a newly created resource in storage.
     */
    /*public function store(StoreJustificationRequest $request)
    {
        //
    }*/

    public function store(Request $request, $id)
    {
        // Validação do request
        $this->validate($request,[
            'motive' => 'required',
            'justification_date' => 'required',
            'observation' => 'required',
            'file' => 'required|file|mimes:png,jpg,jpeg,pdf,docx|max:2048' // Adicione a validação do ficheiro
        ]);

        $paath = "";

        // Encontrar a ausência pelo ID
        $absence = Absence::find($id);
        if (!$absence) {
            return redirect()->back()->with('error', 'Ausência não encontrada');
        }

        // Atualizar o estado da ausência
        $absence->absence_states_id = 3;
        $absence->save();

        // Obter o ficheiro do request
        $ficheiro = $request->file('file');

        //
        //If we have an image file, we store it, and move it in the database
        if ($request->file('file')) {

            Storage::deleteDirectory('public/justifications/' . $id);

            // Get Image File
            $imagePath = $request->file('image');
            // Define Image Name
            // Definir o caminho onde o ficheiro será armazenado, incluindo o ID no caminho
            $fileName = time() . '_' . $ficheiro->getClientOriginalName();
            $filePathSave = "justifications/{$id}/{$fileName}";

        }

        // Armazenar o ficheiro
        $caminho = $ficheiro->storeAs("justifications/{$id}", $fileName, 'public');

        // Criar a justificativa
        Justification::create([
            'motive' => $request->motive,
            'justification_date' => $request->justification_date,
            'observation' => $request->observation,
            'absence_id' => $id,
            'file' => $filePathSave // Salvar o caminho do ficheiro no banco de dados
        ]);

        // Redirecionar com uma mensagem de sucesso
        return redirect('/users/' . Auth::user()->id . '/absences')->with('success', 'Justificação criada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Justification $justification)
    {
        //
    }

    public function justificationManage(Justification $justification){

        $absences= Absence::all();
        $absencefound = $absences->find($justification->absence->id);


        //encontra o estado da ausência
        $states = Absence_State::all();
        $statefound = $states->find($absencefound->absence_states_id);

        //calcula a duração da ausência em horas
        $datetime1 = new \DateTime($absencefound->absence_start_date);
        $datetime2 = new \DateTime($absencefound->absence_end_date);
        $interval = $datetime1->diff($datetime2);
        $duration = $interval->format('%H:%I');

        return view ('pages.justifications.justification-approve', ['justification'=>$justification,'duration'=>$duration]);

    }

    public function justificationDownload($id){

        $justification = Justification::find($id);

        $filepath = public_path('storage\\' . $justification->file);

        return Response::download($filepath);

    }

    public function justificationReject($id){

        $justification = Justification::find($id);

        $absence = Absence::find($justification->absence_id);

        $absence->absence_states_id = 2;
        $absence->approved_by = Auth::user()->id;
        $absence->save();

        return redirect('/justifications/')->with('error', 'Justificação Rejeitada');

    }

    public function justificationApprove($id){

        $justification = Justification::find($id);

        $absence = Absence::find($justification->absence_id);

        $absence->absence_states_id = 1;
        $absence->approved_by = Auth::user()->id;
        $absence->save();

        return redirect('/justifications/')->with('success', 'Justificação Aprovada');

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Justification $justification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJustificationRequest $request, Justification $justification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Justification $justification)
    {
        //
    }
}
