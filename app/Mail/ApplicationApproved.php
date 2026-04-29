<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Application $application)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations - LGF Scholarship Application Approved!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.approved',
        );
    }
}
