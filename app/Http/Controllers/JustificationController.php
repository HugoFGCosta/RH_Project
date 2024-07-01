<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Absence_State;
use App\Models\Justification;
use App\Http\Requests\StoreJustificationRequest;
use App\Http\Requests\UpdateJustificationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use App\Http\Controllers\EmailController;


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
    public function create(Request $request)
    {
        //
        $selectedAbsences = $request->input('selected_absences'); // Isso retorna um array de IDs selecionados
        $absences = Absence::all();
        $states = [];
        $durations = [];
        $statesDb = Absence_State::all();
        $absencesFound = [];

        foreach ($absences as $absence) {
            if (in_array($absence->id, $selectedAbsences)) {


                $statefound = $statesDb->find($absence->absence_states_id);

                $absencefound = $absences->find($absence->id);

                array_push($absencesFound, $absencefound);

                // Adiciona o estado ao array
                array_push($states, $statefound);

                // Calcula a duração da ausência em horas
                $datetime1 = new \DateTime($absence->absence_start_date);
                $datetime2 = new \DateTime($absence->absence_end_date);
                $interval = $datetime1->diff($datetime2);
                $duration = $interval->format('%H:%I');  // Formato correto da duração

                // Adiciona a duração ao array
                array_push($durations, $duration);
            }
        }



        return view ('pages.justifications.create', ['absences' => $absencesFound, 'states' => $states, 'durations' => $durations]);
    }

    /**
     * Store a newly created resource in storage.
     */
    /*public function store(StoreJustificationRequest $request)
    {
        //
    }*/

    public function store(Request $request)
    {
        // Validação do request
        $this->validate($request,[
            'motive' => 'required',
            'justification_date' => 'required',
            'observation' => 'required',
            'file' => 'required|file|mimes:png,jpg,jpeg,pdf,docx|max:2048', // Adicione a validação do ficheiro
            'selected_absences' => 'required|array' // Adicione a validação para faltas selecionadas
        ], $this->messages());


        $paath = "";

        //Percorre todas as faltas autalizando o seu estado para "Pendente"
        foreach ($request->selected_absences as $id) {
            $absence = Absence::find($id);

            //Caso alguma das faltas não exista retorna para a view com uma mensagem de erro
            if (!$absence) {
                return redirect()->back()->with('error', 'Uma das ausências não foi encontrada');
            }

            // Atualizar o estado da ausência
            $absence->absence_states_id = 3;
            $absence->save();
        }

        //Obtem o id da nova justificação
        $justifications = Justification::all();
        $id = $justifications->count() + 1;

        // Obter o ficheiro do request
        $ficheiro = $request->file('file');

        // Caso o ficheiro não seja, jpeg, jpg, png, ou pdf, retorna para a view com uma mensagem de erro
        if (!in_array($ficheiro->getClientOriginalExtension(), ['jpeg', 'jpg', 'png', 'pdf'])) {
            return redirect()->back()->with('error', 'O ficheiro deve ser jpeg, jpg, png, ou pdf');
        }

        if ($ficheiro->getSize() > 1000 * 2084) {
            return redirect()->back()->with('error', 'O ficheiro não pode ser maior que 2MB');
        }


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

        Justification::create([
            'motive' => $request->motive,
            'justification_date' => $request->justification_date,
            'observation' => $request->observation,
            'file' => $filePathSave, // Salvar o caminho do ficheiro no banco de dados
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        $userName = '';
        $userEmail = '';
        $absences = [];

        //Atualiza o justification_id de cada falta
        foreach ($request->selected_absences as $absenceId) {
            $absence = Absence::find($absenceId);
            array_push($absences, $absence);
            $absence->justification_id = $id;
            $userName = $absence->user->name;
            $userEmail = $absence->user->email;
            $absence->save();
        }

        //Envia email para todos os gestores ou admins do sistema com a notificação da submição da justificação
        $users = User::all();
        foreach ($users as $user){
            if($user->role_id == 3 || $user->role_id == 2){
                //Chama o método justificationApproved do EmailController para enviar o email
                $emailController = new EmailController();
                $email = $user->email;
                $emailName = $user->name;
                $emailController->justificationCreated($email,$emailName,$userName, $userEmail, $absences);
            }
        }

        // Redirecionar com uma mensagem de sucesso
        return redirect('/users/' . Auth::user()->id . '/absences')->with('success', 'Justificação criada com sucesso');
    }

    //MÉTODO PARA APRESENTAR MENSAGENS DE ERRO PERSONALIZADAS
    public function messages()
    {
        return [
            'file.max' => 'O arquivo não pode ser maior que 1.5MB.',
            'file.required' => 'O arquivo é obrigatório.',
            'file.mimes' => 'O arquivo deve ser do tipo: jpeg, jpg, png, pdf, docx.',
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Justification $justification)
    {
        //
    }

    public function justificationManage(Justification $justification){

        $absences = Absence::all();
        $justifications = Justification::all();
        $statesDb = Absence_State::all();
        $justificationFound = $justifications->find($justification);
        $states = [];
        $durations = [];

        foreach ($absences as $absence) {

            if($absence->justification_id == $justification->id){

                $statefound = $statesDb->find($absence->absence_states_id);

                // Adiciona o estado ao array
                array_push($states, $statefound);

                // Calcula a duração da ausência em horas
                $datetime1 = new \DateTime($absence->absence_start_date);
                $datetime2 = new \DateTime($absence->absence_end_date);
                $interval = $datetime1->diff($datetime2);
                $duration = $interval->format('%H:%I');  // Formato correto da duração

                // Adiciona a duração ao array
                array_push($durations, $duration);
            }

        }

        return view ('pages.justifications.justification-approve', ['justification' => $justificationFound, 'states' => $states, 'durations' => $durations]);

    }

    public function justificationDownload($id){

        $justification = Justification::find($id);

        $filepath = public_path('storage/' . $justification->file);

        return Response::download($filepath);

    }

    public function justificationReject($id){

        $absences = Absence::all();
        $justifiedAbsences = [];

        foreach ($absences as $absence){
            if($absence->justification_id == $id){
                $absence->absence_states_id = 2;
                $absence->approved_by = Auth::user()->id;
                $email = $absence->user->email;
                $name = $absence->user->name;
                array_push($justifiedAbsences, $absence);
                $absence->save();
            }
        }
        //Chama o método justificationApproved do EmailController para enviar o email
        $emailController = new EmailController();
        $emailController->justificationRejected($name, $email, $justifiedAbsences);


        return redirect('/justifications/')->with('error', 'Justificação Rejeitada');

    }

    public function justificationApprove($id){

        $absences = Absence::all();
        $justifiedAbsences = [];

        foreach ($absences as $absence){
            if($absence->justification_id == $id){
                $absence->absence_states_id = 1;
                $absence->approved_by = Auth::user()->id;
                $email = $absence->user->email;
                $name = $absence->user->name;
                array_push($justifiedAbsences, $absence);
                $absence->save();
            }
        }

        //Chama o método justificationApproved do EmailController para enviar o email
        $emailController = new EmailController();
        $emailController->justificationApproved($name, $email, $justifiedAbsences);

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
