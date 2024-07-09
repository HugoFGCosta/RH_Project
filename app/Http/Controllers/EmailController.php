<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\JustificationMail;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    //

    public function justificationApproved($name, $email, $justifiedAbsences)
    {

        //Recebe o email e inicia a mensagem
        $toEmail = $email;
        $message = nl2br('Caro '.$name.','."\n".' As justificações de: ' . "\n");

        //Percorre as justificações aprovadas e adiciona as datas ao email
        foreach ($justifiedAbsences as $justifiedAbsence) {
            $message .= nl2br($justifiedAbsence->absence_start_date . "-" . $justifiedAbsence->absence_end_date . "\n");
        }

        $message .= 'foram aprovadas.';
        $subject = 'Justificação Aprovada';

        Mail::to($toEmail)->send(new JustificationMail($message, $subject));

    }

    public function justificationRejected($name, $email, $justifiedAbsences)
    {

        //Recebe o email e inicia a mensagem
        $toEmail = $email;
        $message = nl2br('Caro '.$name.','."\n".' As justificações de: ' . "\n");

        //Percorre as justificações aprovadas e adiciona as datas ao email
        foreach ($justifiedAbsences as $justifiedAbsence) {
            $message .= nl2br($justifiedAbsence->absence_start_date . "-" . $justifiedAbsence->absence_end_date . "\n");
        }

        $message .= 'foram rejeitadas.';
        $subject = 'Justificação Rejeitada';

        Mail::to($toEmail)->send(new JustificationMail($message, $subject));

    }

    public function justificationCreated ($emailSend,$emailName,$userName, $userEmail, $absences){

        //Recebe o email e inicia a mensagem
        $toEmail = $emailSend;
        $message = nl2br('Caro '.$emailName.','."\n".' O utilizador '.$userName.' com o email:'.$userEmail .' submeteu uma justificação das seguintes faltas:'. "\n");

        //Percorre as justificações aprovadas e adiciona as datas ao email
        foreach ($absences as $absence) {
            $message .= nl2br($absence->absence_start_date . "-" . $absence->absence_end_date . "\n");
        }

        $subject = 'Justificação submetida por colaborador';

        Mail::to($toEmail)->send(new JustificationMail($message, $subject));
    }
}
