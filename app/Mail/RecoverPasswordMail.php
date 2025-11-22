<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecoverPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        //
        $this->token = is_object($token) ? (string) ($token->token ?? json_encode($token)) : (string) $token;

        //$this->url = 'http://localhost:4200/response-password?token=' . urlencode($this->token);
        $this->url = 'http://192.168.110.101:4200/response-password?token=' . urlencode($this->token);
        Log::info('Recover URL: ' . $this->url);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Recuperacion de contraseña',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
     return new Content(
            view: 'email.passwordRecover', // vista blade
            with: [
                'token' => $this->token,
                'url'   => $this->url,
            ]
            
        );
    }

    /*public function build()
    {
        return $this->view('email.passwordRecover')
                ->with([
                    'token' => $this->token,
                    'url'   => $this->url,
                ]);
    }*/

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
