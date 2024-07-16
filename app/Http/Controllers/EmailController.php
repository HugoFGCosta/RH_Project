<?php

namespace App\Http\Controllers;

use App\Mail\JustificationSubmitted;
use Illuminate\Http\Request;
use App\Mail\JustificationMail;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    // Metodo justificationApproved- serve para enviar um email para o colaborador quando a sua justificação é aprovada
    public function justificationApproved($name, $email, $justifiedAbsences)
    {
        $toEmail = $email;
        $message = 'Caro '.$name.','."<br>".' As justificações de: ' . "<br>";

        foreach ($justifiedAbsences as $justifiedAbsence) {
            $message .= $justifiedAbsence->absence_start_date . " - " . $justifiedAbsence->absence_end_date . "<br>";
        }

        $message .= 'foram aprovadas.';
        $subject = 'Justificação Aprovada';

        Mail::to($toEmail)->send(new JustificationMail($message, $subject));
    }

    // Metodo justificationRejected- serve para enviar um email para o colaborador quando a sua justificação é rejeitada
    public function justificationRejected($name, $email, $justifiedAbsences)
    {
        $toEmail = $email;
        $message = 'Caro '.$name.','."<br>".' As justificações de: ' . "<br>";

        foreach ($justifiedAbsences as $justifiedAbsence) {
            $message .= $justifiedAbsence->absence_start_date . " - " . $justifiedAbsence->absence_end_date . "<br>";
        }

        $message .= 'foram rejeitadas.';
        $subject = 'Justificação Rejeitada';

        Mail::to($toEmail)->send(new JustificationMail($message, $subject));
    }

    // Metodo justificationCreated- serve para enviar email para todos os gestores e admins quando um colaborador submete uma justificação
    public function justificationCreated($emailSend, $emailName, $userName, $userEmail, $absences)
    {
        $toEmail = $emailSend;
        $message = 'Caro '.$emailName.','."<br>".' O utilizador '.$userName.' com o email: '.$userEmail .' submeteu uma justificação das seguintes faltas:'. "<br>";

        foreach ($absences as $absence) {
            $message .= $absence->absence_start_date . " - " . $absence->absence_end_date . "<br>";
        }

        $subject = 'Justificação submetida por colaborador';

        Mail::to($toEmail)->send(new JustificationSubmitted($message, $subject));
    }
}

