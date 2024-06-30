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
}
