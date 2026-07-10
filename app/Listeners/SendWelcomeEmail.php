<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\WelcomeApplicant;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        // Only send welcome email to applicants (not system users)
        if ($event->user->hasRole('Applicant')) {
            try {
                Mail::to($event->user)->send(new WelcomeApplicant($event->user));
                activity('email')
                    ->causedBy($event->user)
                    ->performedOn($event->user)
                    ->withProperties(['recipient' => $event->user->email, 'type' => 'WelcomeApplicant'])
                    ->log('Email sent: Welcome email to new applicant');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send welcome email: ' . $e->getMessage());
                activity('email')
                    ->causedBy($event->user)
                    ->performedOn($event->user)
                    ->withProperties(['error' => $e->getMessage(), 'type' => 'WelcomeApplicant'])
                    ->log('Email failed: Welcome email to new applicant');
            }
        }

        // Log user signup regardless of role
        activity('auth')
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->withProperties(['email' => $event->user->email, 'role' => $event->user->getRoleNames()->first() ?? 'none'])
            ->log('New user registered');
    }
}
