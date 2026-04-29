<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\ResetPassword as BaseResetPassword;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPassword extends BaseResetPassword
{
    protected function handlePasswordReset($user, $password): void
    {
        // Set the password
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ]);

        // Auto-verify email for system users
        if ($user->hasAnyRole(['System Admin', 'Committee Member'])) {
            $user->forceFill(['email_verified_at' => now()]);
        }

        $user->save();

        event(new PasswordReset($user));
    }
}
