<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;


class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $token;
    public string $resetUrl;
  

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $resetUrl, string $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->resetUrl = $resetUrl; //Otvara se forma za reset lozinke na frontendu
       
    }

    /**
     *Build the message.
     */
   public function build()
   {
    return $this
    ->subject('Reset lozinke')
    ->markdown('emails.password-reset');
   }
}
