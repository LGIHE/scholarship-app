<x-filament-panels::page.simple>
    @if (filament()->hasLogin())
        <x-slot name="subheading">
            {{ $this->loginAction }}
        </x-slot>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_PASSWORD_RESET_REQUEST_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::form id="form" wire:submit="request">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_PASSWORD_RESET_REQUEST_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

    {{-- Custom CSS to ensure input works --}}
    <style>
        .filament-email-input input {
            pointer-events: auto !important;
            user-select: text !important;
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
        }
        
        .fi-input input[type="email"] {
            pointer-events: auto !important;
            user-select: text !important;
            cursor: text !important;
        }
        
        /* Ensure the input is focusable and interactive */
        input[name="data.email"], 
        input[wire\\:model="data.email"] {
            pointer-events: auto !important;
            user-select: text !important;
            cursor: text !important;
            background-color: white !important;
            opacity: 1 !important;
        }
    </style>

    {{-- JavaScript to ensure form works --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find the email input
            const emailInput = document.querySelector('input[type="email"]') || 
                              document.querySelector('input[name*="email"]') ||
                              document.querySelector('#data\\.email');
            
            if (emailInput) {
                console.log('Email input found:', emailInput);
                
                // Ensure it's interactive
                emailInput.style.pointerEvents = 'auto';
                emailInput.style.userSelect = 'text';
                emailInput.style.cursor = 'text';
                emailInput.removeAttribute('readonly');
                emailInput.removeAttribute('disabled');
                
                // Focus the input
                setTimeout(() => {
                    emailInput.focus();
                }, 100);
                
                // Test if input works
                emailInput.addEventListener('input', function(e) {
                    console.log('Email input changed:', e.target.value);
                });
                
                emailInput.addEventListener('focus', function() {
                    console.log('Email input focused');
                });
                
                emailInput.addEventListener('blur', function() {
                    console.log('Email input blurred');
                });
            } else {
                console.error('Email input not found');
                console.log('Available inputs:', document.querySelectorAll('input'));
            }
        });
    </script>
</x-filament-panels::page.simple>