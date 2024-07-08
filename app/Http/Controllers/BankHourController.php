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
        //
        $month = $request->input('month');
        $year = $request->input('year');


        if ($month == 'Todos' && $year == null){
            $absences = Absence::all();
            $presences = Presence::all();
        }
        else if($month == 'Todos' && $year != null){
            $absences = Absence::whereYear('absence_start_date', $year)->get();
            $presencesFirst = Presence::whereYear('first_start', $year)->get();
            $presencesSecond = Presence::whereYear('second_start', $year)->get();
            $presences = $presencesFirst->merge($presencesSecond);
        }
        else if($month != null && $year != null){
            $absences = Absence::whereMonth('absence_start_date', $month)->whereYear('absence_start_date', $year)->get();
            $presencesFirst = Presence::whereMonth('first_start', $month)->whereYear('first_start', $year)->get();
            $presencesSecond = Presence::whereMonth('second_start', $month)->whereYear('second_start', $year)->get();
            $presences = $presencesFirst->merge($presencesSecond);

        }
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



        $totalMinutes = 0;

        $userShifts = User_Shift::all();
        $workShifts = Work_Shift::all();
        $bank = 0;
        $userShiftsId = 0;
        $user_id_logged = Auth::user()->id; //id do utilizador logado

        /*CALCULA HORAS DEVIDAS APARTIR DAS PRESENÇAS*/

        /*Percorre as presencas*/
        foreach ($presences as $presence){

            $horasChegouMaisCedoManha = 0;
            $horasChegouMaisCedoTarde = 0;
            $desconto = 0;

            //Percorre as presencas do utilizador logado
            if($presence->user_id == $user_id_logged){

                $duracaoManha = 0;
                $duracaoTarde = 0;
                $horasTrabalhadas = 0;

                //Se apareceu de manha vai buscar o horario pela hora de entrada de manha
                if($presence->first_start!=null){
                    $horaEntrada = $presence->first_start;

                }
                else{   //Senão vai buscar o horario pela hora de entrada de tarde
                    $horaEntrada = $presence->second_start;
                }

                info("hora entrada".$horaEntrada);

                /*Pesquisa qual o horario que o utilizador tinha no dia da presenca*/
                foreach ($userShifts as $userShift){
                    if($userShift -> user_id == $user_id_logged){
                        if($horaEntrada >= $userShift->start_date && $horaEntrada <= $userShift->end_date  && $userShift->user_id == $user_id_logged){
                            $userShiftsId = $userShift->work_shift_id;
                            break;
                        }
                        else if($horaEntrada >= $userShift->start_date && $userShift->end_date == null &&  $userShift->user_id == $user_id_logged){
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
                $horasDeTrabalho = $diff; // minutos que o trabalhador devia ter trabalhado

                /*Calcula hora almoco*/
                $startHour = Carbon::parse($work_shift->break_start);
                $endHour = Carbon::parse($work_shift->break_end);
                $duracaoAlmoco = $startHour->diffInMinutes($endHour);

                // Calcula as horas de trabalho sem hora de almoço
                $horasDeTrabalho -= $duracaoAlmoco;

                /*CALCULA ATRASOS MAS EM PERIODO DE TOLERANCIA (15 minutos mais cedo ou 15 minutos mais tarde)*/

                /*PRIMEIRO TURNO*/

                //Entrar 15 minutos mais tarde

                $horaEntradaFuncionario = Carbon::parse($presence->first_start);    //Hora entrada do funcionaro
                $horaEntradaHorario = Carbon::parse($work_shift->start_hour);
                $horaEntradaHorario->setDate($horaEntradaFuncionario->year, $horaEntradaFuncionario->month, $horaEntradaFuncionario->day); //Hora que devia ter entrado
                $horaEntradaComTolerancia = $horaEntradaHorario->copy()->addMinutes(15);    //Hora entrada no horario mais tolerancia


                //Se chegou a horas guarda quantos minutos chegou atrasado
                if($horaEntradaFuncionario >= $horaEntradaHorario && $horaEntradaFuncionario<=$horaEntradaComTolerancia){
                    $desconto = $horaEntradaFuncionario->diffInMinutes($horaEntradaHorario);
                }
                //Se chegou mais cedo guarda quantos minutos chegou adiantado
                else if($horaEntradaFuncionario<=$horaEntradaHorario){
                    $horasChegouMaisCedoManha = $horaEntradaFuncionario->diffInMinutes($horaEntradaHorario);
                }

                /*SEGUNDO TURNO*/

                //Entrar 15 minutos mais tarde
                $horaEntradaFuncionario = Carbon::parse($presence->second_start);    //Hora entrada do funcionaro
                $horaEntradaHorario = Carbon::parse($work_shift->break_end);
                $horaEntradaHorario->setDate($horaEntradaFuncionario->year, $horaEntradaFuncionario->month, $horaEntradaFuncionario->day); //Hora que devia ter entrado
                $horaEntradaComTolerancia = $horaEntradaHorario->copy()->addMinutes(15);    //Hora entrada no horario mais tolerancia
                $horaSaidaParaIntervalo = Carbon::parse($work_shift->break_start);    //Hora entrada no horario mais tolerancia
                $horaSaidaParaIntervalo->setDate($horaSaidaParaIntervalo->year, $horaEntradaFuncionario->month, $horaEntradaFuncionario->day); //Hora que devia ter entrado


                //Se estrou a horas
                if($horaEntradaFuncionario >= $horaEntradaHorario && $horaEntradaFuncionario<=$horaEntradaComTolerancia){
                    $descontoSegundoTurno = $horaEntradaFuncionario->diffInMinutes($horaEntradaHorario);
                    $desconto+=$descontoSegundoTurno;
                }
                else if ($horaEntradaFuncionario >= $horaSaidaParaIntervalo && $horaEntradaFuncionario<=$horaEntradaHorario){
                    $horasChegouMaisCedoTarde = $horaEntradaFuncionario->diffInMinutes($horaEntradaHorario);
                }


                /*Calcula quantas horas trabalhou de manha*/
                $startHour = Carbon::parse($presence->first_start);
                $endHour = Carbon::parse($presence->first_end);
                $duracaoManha = $startHour->diffInMinutes($endHour);


                /*Se o funcionario apareceu de tarde é que vai calcular quantas horas trabalhou de tarde*/
                if($presence->second_start!=null && $presence->second_end != null){
                    /*Calcula quantas horas trabalhou de tarde*/
                    $startHour = Carbon::parse($presence->second_start);
                    $endHour = Carbon::parse($presence->second_end);
                    $duracaoTarde = $startHour->diffInMinutes($endHour);

                }


                // $horasTrabalhadas é o número de minutos que o trabalhador trabalhou
                $horasTrabalhadas = $duracaoManha + $duracaoTarde;

                // Diferença de minutos entre as que trabalhou e as que devia ter trabalhado
                $resultado = $horasTrabalhadas - $horasDeTrabalho;


                // Soma ao banco as horas que deve em minutos
                $bank += $resultado;
                $bank += $desconto;

                if($horasChegouMaisCedoManha!= 0){
                    $bank -= $horasChegouMaisCedoManha;

                }
                if($horasChegouMaisCedoTarde!= 0){
                    $bank -= $horasChegouMaisCedoTarde;

                }

            }
        }

        $timePresencas = $bank;

        // Converte o banco de minutos para formato HH:MM
        $horas = floor($bank / 60);
        $minutos = $bank % 60;

        if($minutos!=0){
            $horas = $horas + 1;

            if($minutos!=0 && $horas==1){
                $horas = $horas - 1;

            }
        }

        $bankFormattedPresencas = sprintf('%d:%02d', $horas, $minutos);

        $totalMinutes =+ $bank;

        /*CALCULA SE O TRABALHADOR DEVE HORAS POR CAUSA DE FALTAS*/
        $time = 0;
        //Percorre as faltas do utilizador logado
        foreach ($absences as $absence){

            $ver = false;

            if($absence->user_id == $user_id_logged){
                $duracaoAtraso = 0;
                $duracaoAlmoco= 0;
                $duracaoFaltaSemAlmoco=0;
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


                /*VERIFICA SE CHEGOU ATRASADO*/
                /*Verifica atrasos para primeiro turno*/
                foreach ($presences as $presence){

                    if ($presence->user_id == $user_id_logged) {
                        // Se chegou atrasado no primeiro turno ou chegou atrasado no segundo turno
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

        $totalMinutes =+ $time;


        if($timePresencas < $totalMinutes  ){   // Total faltas maior que 0 e total presencas menor que 0

            $totalMinutes = $timePresencas + $totalMinutes;

        }
        else if($timePresencas > $totalMinutes || $timePresencas == $totalMinutes){   // Total faltas maior que 0 e total presencas maior que 0

            $totalMinutes = $totalMinutes + $timePresencas;

        }


        // Converte o banco de minutos para formato HH:MM
        $horas = floor($time / 60);
        $minutos = $time % 60;

        if($minutos!=0){
            $horas = $horas + 1;
        }

        $bankFormattedFaltas = sprintf('%d:%02d', $horas, $minutos);

        //Vai buscar o nome do mes pelo numero em portugues
        $meses = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");


        if($month == "Todos"){
            $month == 'Todos';
        }
        else if($month != 0){
            $month = $meses[$month-1];
        }

        if($year == null){
            $year = 'Todos';
        }

        $horas = floor($totalMinutes / 60);
        $minutos = $totalMinutes % 60;

        if($minutos!=0){
            $horas = $horas + 1;
        }

        $bankTotal = sprintf('%d:%02d', $horas, $minutos);

        return view('pages.time-bank-balance.time-bank-balance ', ['month'=>$month, 'year'=>$year,'totalMinutes'=>$totalMinutes, 'bankFormattedFaltas'=>$bankFormattedFaltas, 'bankFormattedPresencas'=>$bankFormattedPresencas, 'bankTotal'=>$bankTotal]);

    }

}
