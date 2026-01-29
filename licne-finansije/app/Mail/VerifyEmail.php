<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;


class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $verificationUrl; 

    /**
     * Create a new message instance.
     */
    public function __construct($user, $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Build the message.
     */
    public function build() {
        return $this->subject('Verifikacija email adrese')
                    ->markdown('emails.verify-email');
    }
}
