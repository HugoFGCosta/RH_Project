<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PersonRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Cria uma nova instância de Mailable.
     *
     * @param  mixed  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Construa o conteúdo do e-mail.
     *
     * @return \Illuminate\Contracts\Mail\Mailable
     */
    public function build()
    {
        return $this->view('emails.person_registered') // Nome correto da view
            ->with([
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]);
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Novo registo',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.person_registered',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}