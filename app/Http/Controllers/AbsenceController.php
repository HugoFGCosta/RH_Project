<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Requests\UpdateAbsenceRequest;
use App\Models\Absence_State;
use App\Models\AbsenceType;
use App\Models\Justification;
use App\Models\Presence;
use App\Models\User;
use App\Models\User_Shift;
use App\Models\Vacation;
use App\Models\Work_Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Termwind\Components\Anchor;

class AbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /*Metodo index- serve para listar todas as faltas*/
    public function index()
    {
        //
        $absences = Absence::all();
        $absences_states = Absence_State::all();
        $absences_types = AbsenceType::all();
        $justifications = Justification::all();

        return view('pages.absences.absences-list', ['absences'=>$absences, 'absences_states'=>$absences_states, 'absences_types'=>$absences_types, 'justifications'=>$justifications]);

    }

    /*Metodo absencesByUser- serve para listar todas as faltas de um user*/
    public function absencesByUser($id){

        //Vai buscar todas as faltas do utilizador e com absences_states_id = 3 (pendentes) e nenhuma justificação
        $absences = Absence::where('user_id', $id)->get();
        $absences_states = Absence_State::all();
        $absences_types = AbsenceType::all();

        return view ('pages.absences.absences-by-user',['absences'=>$absences, 'absences_states'=>$absences_states, 'absences_types'=>$absences_types]);

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

    /*Metodo import- serve para importar faltas para a base de dados*/
    /*Metodo import- serve para importar faltas para a base de dados*/
    /*Metodo import- serve para importar faltas para a base de dados*/
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

        $errors = [];

        // Percorre o ficheiro e insere os dados na base de dados
        while (($line = fgets($handle)) !== false) {

            $data = str_getcsv($line);


            // Verifica se há exatamente 5 campos
            if (count($data) != 9) {
                return redirect()->back()->with('error', 'Certifique-se que este ficheiro contém informações de faltas.');
            }

            // Verifica se os IDs são numéricos e depois converte para inteiros
            if (!is_numeric($data[0]) || !is_numeric($data[2]) || !is_numeric($data[3])) {
                return redirect()->back()->with('error', 'Certifique-se que os IDs de utilizador, estado de falta e aprovador são números válidos.');
            }


            // Verifica se o campo absence_date é uma data válida
            if (strtotime($data[5]) === false||strtotime($data[6]) === false) {
                return redirect()->back()->with('error', 'As datas fornecidas não são válidas.');
            }

            // Verifica se o ID de estado de aprovação de falta está entre 1 e 4
            if ($data[2] < 1 || $data[2] > 4){
                return redirect()->back()->with('error', 'Certifique-se que os IDs de estado são números válidos.');
            }

            // Verifica se o ID de tipo de aprovação de falta está entre 1 e 3
            if ($data[3] < 1 || $data[3] > 3){
                return redirect()->back()->with('error', 'Certifique-se que os IDs de tipo são números válidos.');
            }


            //Verifica se existe um horario para o utilizador na altura da falta
            $usersShifts = User_Shift::all();
            $startDate = strtotime($data[5]);
            $endDate = strtotime($data[5]);

            $verStart = false;
            $verEnd = false;

            foreach ($usersShifts as $usersShift){

             if(date("Y-m-d H:i:s", $startDate)>=$usersShift->start_date && date("Y-m-d H:i:s", $startDate) <= $usersShift->end_date && $usersShift->user_id == $data[0]) {
                 $verStart = true;
             }
             elseif (date("Y-m-d H:i:s", $startDate)>=$usersShift->start_date && $usersShift->end_date == null && $usersShift->user_id == $data[0]){
                 $verStart = true;
             }
             if(date("Y-m-d H:i:s", $endDate)>=$usersShift->start_date && date("Y-m-d H:i:s", $endDate) <= $usersShift->end_date && $usersShift->user_id == $data[0]) {
                 $verEnd = true;
             }
             elseif (date("Y-m-d H:i:s", $endDate)>=$usersShift->start_date && $usersShift->end_date == null && $usersShift->user_id == $data[0]){
                 $verEnd = true;
             }
            }

            //Caso nao exista um horario para o utilizador na altura da falta, é mostrada uma mensagem de erro
            if($verStart == false || $verEnd == false){
                return redirect()->back()->with('error', 'Certifique-se que os utilizadores têm um horário na altura de todas as faltas.');
            }


            $absenceData = [
                'user_id' => $data[0],
                'justification_id' => $data[1], // Verifica se justification_id está vazio
                'absence_states_id' => $data[2],
                'absence_types_id' => $data[3],
                'absence_start_date' => $data[5],
                'absence_end_date' => $data[6],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $absenceData['approved_by'] = !empty($data[4]) ? $data[4] : null;

            Absence::create($absenceData);

        }

        fclose($handle);

        // Reabilita as verificações de chave estrangeira
        Schema::enableForeignKeyConstraints();

        // Se houver erros, redireciona de volta com as mensagens de erro
        if (!empty($errors)) {
            return redirect()->back()->with('error', $errors);
        }

        // Redireciona para a página anterior com uma mensagem de sucesso
        return redirect()->back()->with('success', 'Faltas importadas com sucesso.');
    }

    /*Metodo export- serve para exportar todas as faltas existentes na base de dados*/
    public function export(){

        // Define o nome do ficheiro e os cabeçalhos
        $absences = Absence::all();
        $csvFileName = 'absences.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        //Escreve os cabeçalhos no ficheiro
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Id_Utilizador','Id_Justificacao','Id_Estado_Falta','Id_Tipo_Falta', 'Aprovado_Por','Data_Comeco_Falta','Data_Fim_Falta','Criado_A','Atualizado_A']);

        //Para cada falta insere uma linha no ficheiro
        foreach ($absences as $absence) {
            fputcsv($handle, [$absence->user_id,$absence->justification_id,$absence->absence_states_id,$absence->absence_types_id, $absence->approved_by,$absence->absence_start_date,$absence->absence_end_date, $absence->created_at, $absence->updated_at]); // Add more fields as needed
        }

        // Fecha o ficheiro
        fclose($handle);

        return Response::make('', 200, $headers);
    }

    // Metodo verifyPresences- serve para correr todos os metodos do procedure
    public function verifyPresences(){

        $this->verifyFirstShiftAbsence();
        $this->verifySecondShiftAbsence();

    }

    // Metodo verifyFirstShiftAbsence- serve para verificar se o utilizador faltou ao primeiro turno
    public function verifyFirstShiftAbsence(){

        $users = User::all();
        $presences = Presence::all();

        foreach ($users as $user){

            //Vai buscar a hora em que o utilizador tem de entrar no trabalho
            $user_shift = User_Shift::where('user_id', $user->id)->latest()->first();
            $work_shiftId = $user_shift->work_shift_id;
            $work_shift= Work_Shift::where('id', $work_shiftId)->first();
            $horaEntrada = Carbon::now()->format('Y-m-d') . ' ' . Carbon::parse($work_shift->start_hour)->format('H:i:s');

            //Cria uma variavel com a hora de entrada mais 15 minutos
            $horaEntradaComTolerancia = Carbon::parse($horaEntrada)->addMinutes(15)->format('Y-m-d H:i:s');

            if($this->verifyUserInVacation($user->id,$horaEntradaComTolerancia)){    //Se o utilizador estiver de férias passa para o próximo utilizador
                continue;
            }

            //Cria uma variavel com a hora de entrada menos 15 minutos
            $horaEntradaMenosTolerancia = Carbon::parse($horaEntrada)->subMinutes(15)->format('Y-m-d H:i:s');

            //Obtem data e hora atual
            $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');

            // Calcula a diferença de tempo entre $work_shift->start_hour e $work_shift->break_start
            $startHour = Carbon::parse($work_shift->start_hour);
            $breakStart = Carbon::parse($work_shift->break_start);

            // Ajuste para lidar com turnos noturnos que atravessam a meia-noite
            if ($breakStart->lessThan($startHour)) {
                $breakStart->addDay();
            }

            $diff = $startHour->diffInMinutes($breakStart);

            //Retira a diferença de tempo ao $work_shift->break_start para obter a hora de entrada do turno
            $horaEntradaCalculo = Carbon::parse($work_shift->break_start)->subMinutes($diff)->format('Y-m-d H:i:s');

            //Cria uma variavel com a hora de saída do turno
            $horaSaida = Carbon::parse($work_shift->break_start)->format('Y-m-d H:i:s');

            $ver = false;

            //Adiciona uma hora ao currentDateTime para que a hora de entrada com tolerância seja comparada com a hora atual
            $currentDateTime = Carbon::parse($currentDateTime)->addHour()->format('Y-m-d H:i:s');

            info('Data atual: ' . $currentDateTime);
            info('Hora de entrada com tolerância: ' . $horaEntradaComTolerancia);


            //Se a hora atual for igual a hora de entrada com tolerancia deste user significa que está na hora de verificar se este utilizador marcou a sua presença
            if($currentDateTime == $horaEntradaComTolerancia){
                //Verifica se o user marcou presença 15 minutos antes ou depois da hora de entrada
                foreach ($presences as $presence){
                    if($presence->user_id == $user->id){
                        if($presence->first_start >= $horaEntradaMenosTolerancia && $presence->first_start <= $horaEntradaComTolerancia) {
                            $ver= true;
                        }
                    }
                }

                //Se o user não marcou presença 15 minutos antes ou depois da hora de entrada, é marcada uma falta
                if(!$ver){
                    Absence::create([
                        'user_id' => $user->id,
                        'absence_states_id' => 4,
                        'absence_types_id'=>1,
                        'approved_by' => null,
                        'absence_start_date' => $horaEntradaCalculo,
                        'absence_end_date' => $horaSaida,
                        'justification' => 'Falta ao primeiro turno',
                    ]);
                }
            }
        }
    }

    // Metodo verifySecondShiftAbsence- serve para verificar se o utilizador faltou ao segundo turno
    public function verifySecondShiftAbsence(){

        $users = User::all();
        $presences = Presence::all();

        foreach ($users as $user){

            //Vai buscar a hora em que o utilizador tem de entrar no trabalho
            $user_shift = User_Shift::where('user_id', $user->id)->latest()->first();
            $work_shiftId = $user_shift->work_shift_id;
            $work_shift= Work_Shift::where('id', $work_shiftId)->first();
            $entranceHour = Carbon::now()->format('Y-m-d') . ' ' . Carbon::parse($work_shift->break_end)->format('H:i:s');

            //Cria uma variavel com a hora de entrada mais 15 minutos
            $entranceHourWithToleration = Carbon::parse($entranceHour)->addMinutes(15)->format('Y-m-d H:i:s');

            if($this->verifyUserInVacation($user->id,$entranceHourWithToleration)){    //Se o utilizador estiver de férias não é marcada falta
                continue;
            }

            //Cria uma variavel com a hora de entrada menos 15 minutos
            $horaEntradaMenosTolerancia = Carbon::parse($entranceHour)->subMinutes(15)->format('Y-m-d H:i:s');

            //Obtem data e hora atual
            $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');

            // Calcula a diferença de tempo entre $work_shift->break_end e $work_shift->end_hour
            $breakEnd = Carbon::parse($work_shift->break_end);
            $endHour = Carbon::parse($work_shift->end_hour);

            // Ajuste para lidar com turnos noturnos que atravessam a meia-noite
            if ($endHour->lessThan($breakEnd)) {
                $endHour->addDay();
            }

            $diff = $breakEnd->diffInMinutes($endHour);

            //Cria uma variavel com a hora de saída do turno
            $horaSaida = Carbon::parse($work_shift->end_hour)->format('Y-m-d H:i:s');

            $ver = false;

            //Adiciona uma hora ao currentDateTime para que a hora de entrada com tolerância seja comparada com a hora atual
            $currentDateTime = Carbon::parse($currentDateTime)->addHour()->format('Y-m-d H:i:s');


            //Se a hora atual for igual a hora de entrada com tolerancia deste user significa que está na hora de verificar se este utilizador marcou a sua presença
            if($currentDateTime == $entranceHourWithToleration){

                if($this->verifyTotalAbsence($user)){    //Se o utilizador já tiver uma falta no turno da manhã é lhe marcada falta total
                    continue; //Portanto a falta de segundo turno não é marcada
                }


                //Verifica se o user marcou presença 15 minutos antes ou depois da hora de entrada
                foreach ($presences as $presence){
                    if($presence->user_id == $user->id){
                        if($presence->first_start >= $horaEntradaMenosTolerancia && $presence->first_start <= $entranceHourWithToleration) {
                            $ver= true;
                        }
                    }
                }

                //Se o user não marcou presença 15 minutos antes ou depois da hora de entrada, é marcada uma falta
                if(!$ver){
                    Absence::create([
                        'user_id' => $user->id,
                        'absence_states_id' => 4,
                        'absence_types_id'=>2,
                        'approved_by' => null,
                        'absence_start_date' => $entranceHour,
                        'absence_end_date' => $horaSaida,
                        'justification' => 'Falta ao segundo turno',
                    ]);
                }
            }
        }
    }

    // Metodo verifySecondShiftAbsence- serve para verificar se o utilizador está de férias
    public function verifyUserInVacation($user_id, $data){

        $vacations = Vacation::all();

        foreach($vacations as $vacation){   //Caso o utilizador esteja de férias retorna true
            if($vacation->user_id == $user_id && $data>= $vacation->start_date && $data <= $vacation->end_date && $vacation->vacation_approval_states_id == 1){
                return true;
            }
        }

        return false;   //Caso o utilizador não esteja de férias retorna false

    }

    // Metodo verifyTotalAbsence- serve para verificar se o colaborador faltou o dia inteiro
    public function verifyTotalAbsence($user)
    {
        $absences = Absence::all();

        //Vai buscar a hora em que o utilizador tem de entrar no trabalho
        $user_shift = User_Shift::where('user_id', $user->id)->latest()->first();
        $work_shiftId = $user_shift->work_shift_id;
        $work_shift= Work_Shift::where('id', $work_shiftId)->first();
        $horaEntrada = Carbon::now()->format('Y-m-d') . ' ' . Carbon::parse($work_shift->start_hour)->format('H:i:s');

        // Calcula a diferença de tempo entre $work_shift->start_hour e $work_shift->break_start
        $startHour = Carbon::parse($work_shift->start_hour);
        $breakStart = Carbon::parse($work_shift->break_start);

        // Ajuste para lidar com turnos noturnos que atravessam a meia-noite
        if ($breakStart->lessThan($startHour)) {
            $breakStart->addDay();
        }

        $diff = $startHour->diffInMinutes($breakStart);

        //Retira a diferença de tempo ao $work_shift->break_start para obter a hora de entrada do turno
        $horaEntradaCalculo = Carbon::parse($work_shift->break_start)->subMinutes($diff)->format('Y-m-d H:i:s');

        //Calcula a diferenca de tempo em minutos entre start_hour e end_hour (tem em conta que a end_hour pode ser menor que a start_hour. Start_hour e end_hour são apenas horas=

        $startHour = Carbon::parse($work_shift->start_hour);
        $endHour = Carbon::parse($work_shift->end_hour);

        if ($endHour->lessThan($startHour)) {
            $endHour->addDay();
        }

        $diff = $startHour->diffInMinutes($endHour);

        foreach ($absences as $absence){    //Se o utilizador já tiver uma falta no turno da manhã é lhe marcada falta total e retorna true
            if($absence->user_id == $user->id && $absence->absence_start_date == $horaEntradaCalculo){
                $absence->justification = 'Falta total';
                $absence->absence_types_id = 3;
                $absence->absence_end_date = Carbon::parse($absence->absence_start_date)->addMinutes($diff)->format('Y-m-d H:i:s');;
                $absence->save();
                return true;
            }
        }

        return false;   //Caso o utilizador não tenha falta no turno da manhã retorna false

    }
}
