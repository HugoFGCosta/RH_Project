<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Presence;
use App\Models\User_Shift;
use App\Models\Work_Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use function Webmozart\Assert\Tests\StaticAnalysis\string;

class BankHourController extends Controller
{
    //

    public function index(Request $request)
    {
        // Vai buscar os inputs introduzidos pelo utilizador
        $month = $request->input('month');
        $year = $request->input('year');

        // Se a pesquisa for por todos os meses e todos os anos
        if ($month == 'Todos' && $year == null){
            $absences = Absence::all();
            $presences = Presence::all();
        }

        // Se a pesquisa for por todos os meses e um ano em especifico
        else if($month == 'Todos' && $year != null){
            $absences = Absence::whereYear('absence_start_date', $year)->get();
            $presencesFirst = Presence::whereYear('first_start', $year)->get();
            $presencesSecond = Presence::whereYear('second_start', $year)->get();
            $presences = $presencesFirst->merge($presencesSecond);
        }

        // Se a pesquisa for por um mes e um ano em especifico
        else if($month != null && $year != null){
            $absences = Absence::whereMonth('absence_start_date', $month)->whereYear('absence_start_date', $year)->get();
            $presencesFirst = Presence::whereMonth('first_start', $month)->whereYear('first_start', $year)->get();
            $presencesSecond = Presence::whereMonth('second_start', $month)->whereYear('second_start', $year)->get();
            $presences = $presencesFirst->merge($presencesSecond);

        }

        // Se a pesquisa for por um mes em especifico e por todos os anos
        else if($month != null && $year == null){
            $absences = Absence::whereMonth('absence_start_date', $month)->get();
            $presencesFirst = Presence::whereMonth('first_start', $month)->get();
            $presencesSecond = Presence::whereMonth('second_start', $month)->get();
            $presences = $presencesFirst->merge($presencesSecond);

        }
        else{
            $absences = Absence::all();
            $presences = Presence::all();
        }



        $timeAbsences = 0;
        $userShifts = User_Shift::all();
        $workShifts = Work_Shift::all();
        $bank = 0;
        $userShiftsId = 0;
        $user_id_logged = Auth::user()->id; //id do utilizador logado

        /*CALCULA HORAS DEVIDAS APARTIR DAS PRESENÇAS*/

        /*Percorre as presencas*/
        foreach ($presences as $presence){

            $timeEarlyArrivedFirstShift = 0;
            $timeEarlyArrivedSecondShift = 0;
            $diferenceWorkedTime = 0;

            //Percorre as presencas do utilizador logado
            if($presence->user_id == $user_id_logged){

                $durationFirstShift = 0;
                $durationSecondShift = 0;
                $workedTime = 0;

                //Se apareceu de manha vai buscar o horario pela hora de entrada de manha
                if($presence->first_start!=null){

                    $arrivalTime = $presence->first_start;

                }

                //Senão vai buscar o horario pela hora de entrada de tarde
                else{

                    $arrivalTime = $presence->second_start;

                }

                /*Pesquisa qual o horario que o utilizador tinha no dia da presenca*/
                foreach ($userShifts as $userShift){

                    if($userShift -> user_id == $user_id_logged){
                        if($arrivalTime >= $userShift->start_date && $arrivalTime <= $userShift->end_date  && $userShift->user_id == $user_id_logged){
                            $userShiftsId = $userShift->work_shift_id;
                            break;
                        }
                        else if($arrivalTime >= $userShift->start_date && $userShift->end_date == null &&  $userShift->user_id == $user_id_logged){
                            $userShiftsId = $userShift->work_shift_id;
                            break;
                        }
                    }

                }

                /*Calcula minutos que devia ter trabalhado*/
                $work_shift= Work_Shift::where('id', $userShiftsId)->first();
                $startHour = Carbon::parse($work_shift->start_hour);
                $endHour = Carbon::parse($work_shift->end_hour);
                $diff = $startHour->diffInMinutes($endHour);
                $totalWorkedHours = $diff; // minutos que o trabalhador devia ter trabalhado

                /*Calcula hora almoco*/
                $startHour = Carbon::parse($work_shift->break_start);
                $endHour = Carbon::parse($work_shift->break_end);
                $lunchBreakDuration = $startHour->diffInMinutes($endHour);

                // Calcula as horas de trabalho sem hora de almoço
                $totalWorkedHours -= $lunchBreakDuration;

                /*CALCULA ATRASOS MAS EM PERIODO DE TOLERANCIA (15 minutos mais cedo ou 15 minutos mais tarde)*/

                /*PRIMEIRO TURNO*/

                //Entrar 15 minutos mais tarde

                $arrivalTimeColaborator = Carbon::parse($presence->first_start);    //Hora entrada do funcionaro
                $arrivalTimeWorkShift = Carbon::parse($work_shift->start_hour);
                $arrivalTimeWorkShift->setDate($arrivalTimeColaborator->year, $arrivalTimeColaborator->month, $arrivalTimeColaborator->day); //Hora que devia ter entrado
                $arrivalTimeWorkShiftTolerated = $arrivalTimeWorkShift->copy()->addMinutes(15);    //Hora entrada no horario mais tolerancia


                //Se chegou a horas guarda quantos minutos chegou atrasado
                if($arrivalTimeColaborator >= $arrivalTimeWorkShift && $arrivalTimeColaborator<=$arrivalTimeWorkShiftTolerated){
                    $diferenceWorkedTime = $arrivalTimeColaborator->diffInMinutes($arrivalTimeWorkShift);
                }
                //Se chegou mais cedo guarda quantos minutos chegou adiantado
                else if($arrivalTimeColaborator<=$arrivalTimeWorkShift){
                    $timeEarlyArrivedFirstShift = $arrivalTimeColaborator->diffInMinutes($arrivalTimeWorkShift);
                }

                /*SEGUNDO TURNO*/

                //Entrar 15 minutos mais tarde
                $arrivalTimeColaborator = Carbon::parse($presence->second_start);    //Hora entrada do funcionaro
                $arrivalTimeWorkShift = Carbon::parse($work_shift->break_end);
                $arrivalTimeWorkShift->setDate($arrivalTimeColaborator->year, $arrivalTimeColaborator->month, $arrivalTimeColaborator->day); //Hora que devia ter entrado
                $arrivalTimeWorkShiftTolerated = $arrivalTimeWorkShift->copy()->addMinutes(15);    //Hora entrada no horario mais tolerancia
                $timeForBreak = Carbon::parse($work_shift->break_start);    //Hora entrada no horario mais tolerancia
                $timeForBreak->setDate($timeForBreak->year, $arrivalTimeColaborator->month, $arrivalTimeColaborator->day); //Hora que devia ter entrado


                //Se estrou a horas
                if($arrivalTimeColaborator >= $arrivalTimeWorkShift && $arrivalTimeColaborator<=$arrivalTimeWorkShiftTolerated){
                    $differenceSecondShift = $arrivalTimeColaborator->diffInMinutes($arrivalTimeWorkShift);
                    $diferenceWorkedTime+=$differenceSecondShift;
                }
                else if ($arrivalTimeColaborator >= $timeForBreak && $arrivalTimeColaborator<=$arrivalTimeWorkShift){
                    $timeEarlyArrivedSecondShift = $arrivalTimeColaborator->diffInMinutes($arrivalTimeWorkShift);
                }

                /*Calcula quantas horas trabalhou de manha*/
                $startHour = Carbon::parse($presence->first_start);
                $endHour = Carbon::parse($presence->first_end);
                $durationFirstShift = $startHour->diffInMinutes($endHour);

                /*Se o funcionario apareceu de tarde é que vai calcular quantas horas trabalhou de tarde*/
                if($presence->second_start!=null && $presence->second_end != null){
                    /*Calcula quantas horas trabalhou de tarde*/
                    $startHour = Carbon::parse($presence->second_start);
                    $endHour = Carbon::parse($presence->second_end);
                    $durationSecondShift = $startHour->diffInMinutes($endHour);

                }

                // $workedTime é o número de minutos que o trabalhador trabalhou
                $workedTime = $durationFirstShift + $durationSecondShift;

                // Diferença de minutos entre as que trabalhou e as que devia ter trabalhado
                $result = $workedTime - $totalWorkedHours;

                // Soma ao banco as horas que deve em minutos
                $bank += $result;
                $bank += $diferenceWorkedTime;

                if($timeEarlyArrivedFirstShift!= 0){

                    $bank -= $timeEarlyArrivedFirstShift;

                }
                if($timeEarlyArrivedSecondShift!= 0){

                    $bank -= $timeEarlyArrivedSecondShift;

                }

            }
        }

        $timePresences = $bank;

        // Converte o banco de minutos para formato HH:MM
        $hours = floor($bank / 60);
        $minutes = $bank % 60;

        //Se os minutos forem diferentes de 0 entao adiciona uma hora
        if($minutes!=0){
            $hours = $hours + 1;

            if($minutes!=0 && $hours==1){

                $hours = $hours - 1;

            }
        }

        $bankFormattedPresences = sprintf('%d:%02d', $hours, $minutes);

        $timeAbsences =+ $bank;

        /*CALCULA SE O TRABALHADOR DEVE HORAS POR CAUSA DE FALTAS*/
        $time = 0;
        //Percorre as faltas do utilizador logado
        foreach ($absences as $absence){

            $ver = false;

            if($absence->user_id == $user_id_logged){
                $durationDelay = 0;
                $lunchBreakDuration= 0;
                $absenceDuration=0;
                $ver = false;   //serve para verificar se o utilizador chegou atrasado

                /*Calcula duracao da falta*/
                $startHour = Carbon::parse($absence->absence_start_date);
                $endHour = Carbon::parse($absence->absence_end_date);
                $diff = $startHour->diffInMinutes($endHour);

                /*Pesquisa qual o horario que o utilizador tinha no dia da presenca*/
                foreach ($userShifts as $userShift){
                    if($userShift -> user_id == $user_id_logged){
                        if($startHour >= $userShift->start_date && $startHour <= $userShift->end_date  && $userShift->user_id == $user_id_logged){
                            $userShiftsId = $userShift->work_shift_id;
                            break;
                        }
                        else if($startHour >= $userShift->start_date && $userShift->end_date == null &&  $userShift->user_id == $user_id_logged){
                            $userShiftsId = $userShift->work_shift_id;
                            break;
                        }
                    }
                }

                if($absence->absence_types_id == 3){

                    /*Calcula duracao almoço*/
                    $work_shift= Work_Shift::where('id', $userShiftsId)->first();
                    $startHourInt = Carbon::parse($work_shift->break_start);
                    $endHourInt = Carbon::parse($work_shift->break_end);
                    $diffInt = $startHourInt->diffInMinutes($endHourInt);
                    $diff= $diff - $diffInt;

                }


                /*Verifica se chegou atrasado no primeiro ou no segundo turno*/
                foreach ($presences as $presence){

                    if ($presence->user_id == $user_id_logged) {
                        if ($presence->first_start >= $absence->absence_start_date && $presence->first_start <= $absence->absence_end_date){

                            $ver = true;

                        }
                        else if ($presence->second_start >= $absence->absence_start_date && $presence->second_start <= $absence->absence_end_date){

                            $ver = true;

                        }
                    }
                }

                //Se o utilizador teve falta e nem sequer chegou a comparecer, é retirado a duração de falta ao banco de horas
                if($ver == false){

                    $time = $time - $diff;

                }
            }
        }

        $timeAbsences =+ $time;

        // Faz a soma de total de tempo devido por presenças e por faltas
        if($timePresences < $timeAbsences  ){

            $timeAbsences = $timePresences + $timeAbsences;

        }
        else if($timePresences > $timeAbsences || $timePresences == $timeAbsences){

            $timeAbsences = $timeAbsences + $timePresences;

        }


        // Converte o banco de minutos para formato HH:MM
        $hours = floor($time / 60);
        $minutes = $time % 60;

        if($minutes!=0){

            $hours = $hours + 1;
        }

        $bankFormattedAbsences = sprintf('%d:%02d', $hours, $minutes);

        //Vai buscar o nome do mes pelo numero em portugues
        $monthsArray = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");


        if($month == "Todos"){

            $month == 'Todos';

        }
        else if($month != 0){

            $month = $monthsArray[$month-1];

        }

        if($year == null){

            $year = 'Todos';

        }

        $hours = floor($timeAbsences / 60);
        $minutes = $timeAbsences % 60;

        //Se os minutos forem diferentes de 0 entao adiciona uma hora
        if($minutes!=0){

            $hours = $hours + 1;

        }

        $bankTotal = sprintf('%d:%02d', $hours, $minutes);

        return view('pages.time-bank-balance.time-bank-balance ', ['month'=>$month, 'year'=>$year,'totalMinutes'=>$timeAbsences, 'bankFormattedFaltas'=>$bankFormattedAbsences, 'bankFormattedPresencas'=>$bankFormattedPresences, 'bankTotal'=>$bankTotal]);

    }

}
