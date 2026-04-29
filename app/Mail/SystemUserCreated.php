<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Password;

class SystemUserCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $setupUrl;

    public function __construct(public User $user)
    {
        // Generate a password reset token for the new user
        $token = Password::createToken($user);
        
        // Use our custom admin password setup route (no signature required)
        $this->setupUrl = route('admin.password.setup', [
            'token' => $token,
            'email' => $user->email,
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to LGF Admin System - Set Your Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.users.system-user-created',
            with: [
                'setupUrl' => $this->setupUrl,
            ],
        );
    }
}
