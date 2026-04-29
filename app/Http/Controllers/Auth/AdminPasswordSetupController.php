<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

class AdminPasswordSetupController extends Controller
{
    /**
     * Handle admin password setup redirect.
     * This creates a properly signed URL and redirects to Filament's password reset page.
     */
    public function setup(Request $request)
    {
        // Validate that we have the required parameters
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
        ]);

        // Verify the token is valid
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user || !Password::tokenExists($user, $request->token)) {
            return redirect()->route('filament.admin.auth.login')
                ->withErrors(['email' => 'This password reset token is invalid or has expired.']);
        }

        // Verify user is a system user
        if (!$user->hasAnyRole(['System Admin', 'Committee Member'])) {
            return redirect()->route('login')
                ->withErrors(['email' => 'This link is for system administrators only.']);
        }

        // Create a properly signed URL for Filament's password reset page
        $signedUrl = URL::temporarySignedRoute(
            'filament.admin.auth.password-reset.reset',
            now()->addMinutes(30), // Short expiry since we're redirecting immediately
            [
                'token' => $request->token,
                'email' => $request->email,
            ]
        );

        // Redirect to the signed URL
        return redirect($signedUrl);
    }
}