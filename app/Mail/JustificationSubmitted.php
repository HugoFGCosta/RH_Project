<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class JustificationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $mailMessage;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($message, $subject)
    {
        //
        $this->mailMessage = $message;
        $this->subject = $subject;
    }

    public function build()
    {
        return $this->view('emails.justification_submitted')
            ->subject($this->subject)
            ->with([
                'message' => $this->mailMessage,
                'subject' => $this->subject
            ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('sendertpsi@gmail.com', 'Hospital Tâmega e Sousa'),
            subject: $this->subject,
        );
    }


    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.justification_submitted',
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
