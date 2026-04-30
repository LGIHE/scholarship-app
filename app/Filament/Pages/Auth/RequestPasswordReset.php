<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    protected static string $view = 'filament.pages.auth.request-password-reset';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->autocomplete('email')
                    ->autofocus()
                    ->placeholder('Enter your email address')
                    ->extraInputAttributes([
                        'style' => 'pointer-events: auto !important; user-select: text !important;',
                        'tabindex' => '1',
                    ])
                    ->extraAttributes([
                        'class' => 'filament-email-input',
                    ]),
            ])
            ->statePath('data');
    }

    public function mount(): void
    {
        parent::mount();
        
        // Ensure the form is properly initialized
        $this->form->fill();
    }
}
