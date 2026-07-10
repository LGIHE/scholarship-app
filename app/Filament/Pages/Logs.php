<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Log as LaravelLog;
use Carbon\Carbon;

class Logs extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Logs';
    protected static ?string $title           = 'System Logs';
    protected static ?string $navigationGroup = 'System';
    protected static ?int    $navigationSort  = 50;
    protected static string  $view            = 'filament.pages.logs';

    // ── Filter state ──────────────────────────────────────────────────────────

    public array $data = [
        'log_name'   => null,
        'event'      => null,
        'search'     => null,
        'date_from'  => null,
        'date_to'    => null,
        'tab'        => 'activity', // 'activity' | 'error'
    ];

    public int $perPage = 25;

    // ── Access control ────────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('System Admin');
    }

    // ── Livewire reactive ─────────────────────────────────────────────────────

    public function updatedData(): void
    {
        // Re-render happens automatically on every field change
    }

    public function setTab(string $tab): void
    {
        $this->data['tab'] = $tab;
    }

    // ── Form ──────────────────────────────────────────────────────────────────

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('log_name')
                            ->label('Log Channel')
                            ->options([
                                'application' => 'Applications',
                                'scholar'     => 'Scholars',
                                'email'       => 'Emails',
                                'auth'        => 'Auth / Signups',
                            ])
                            ->native(false)
                            ->placeholder('All channels')
                            ->nullable()
                            ->live(),

                        Select::make('event')
                            ->label('Event Type')
                            ->options([
                                'created' => 'Created',
                                'updated' => 'Updated',
                                'deleted' => 'Deleted',
                            ])
                            ->native(false)
                            ->placeholder('All events')
                            ->nullable()
                            ->live(),

                        TextInput::make('search')
                            ->label('Search description')
                            ->placeholder('e.g. "email sent" or applicant name…')
                            ->nullable()
                            ->live(onBlur: true),

                        DatePicker::make('date_from')
                            ->label('From')
                            ->nullable()
                            ->displayFormat('d/m/Y')
                            ->live(onBlur: true),

                        DatePicker::make('date_to')
                            ->label('To')
                            ->nullable()
                            ->displayFormat('d/m/Y')
                            ->live(onBlur: true),
                    ])
                    ->columns(5),
            ])
            ->statePath('data');
    }

    // ── Data helpers ──────────────────────────────────────────────────────────

    public function getActivityLogs(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Activity::with('causer', 'subject')
            ->orderByDesc('created_at');

        if ($logName = $this->data['log_name'] ?? null) {
            $query->where('log_name', $logName);
        }

        if ($event = $this->data['event'] ?? null) {
            $query->where('event', $event);
        }

        if ($search = $this->data['search'] ?? null) {
            $query->where('description', 'like', "%{$search}%");
        }

        if ($from = $this->data['date_from'] ?? null) {
            $query->whereDate('created_at', '>=', Carbon::parse($from));
        }

        if ($to = $this->data['date_to'] ?? null) {
            $query->whereDate('created_at', '<=', Carbon::parse($to));
        }

        return $query->paginate($this->perPage);
    }

    public function getErrorLogs(): array
    {
        $logPath = storage_path('logs/laravel.log');

        if (! file_exists($logPath)) {
            return [];
        }

        $lines   = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            // Laravel log entries start with a timestamp like [2026-07-10 ...]
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)$/', $line, $m)) {
                if ($current) {
                    $entries[] = $current;
                }
                $current = [
                    'timestamp'   => $m[1],
                    'environment' => $m[2],
                    'level'       => strtolower($m[3]),
                    'message'     => $m[4],
                    'context'     => '',
                ];
            } elseif ($current) {
                $current['context'] .= $line . "\n";
            }
        }

        if ($current) {
            $entries[] = $current;
        }

        // Most recent first, cap at 200 lines
        $entries = array_reverse($entries);

        // Apply search filter
        if ($search = $this->data['search'] ?? null) {
            $entries = array_filter($entries, fn ($e) => stripos($e['message'], $search) !== false);
        }

        // Apply date filter
        if ($from = $this->data['date_from'] ?? null) {
            $fromTs  = Carbon::parse($from)->startOfDay();
            $entries = array_filter($entries, fn ($e) => Carbon::parse($e['timestamp'])->gte($fromTs));
        }

        if ($to = $this->data['date_to'] ?? null) {
            $toTs    = Carbon::parse($to)->endOfDay();
            $entries = array_filter($entries, fn ($e) => Carbon::parse($e['timestamp'])->lte($toTs));
        }

        return array_values(array_slice($entries, 0, 200));
    }

    public function getLogStats(): array
    {
        return [
            'total'        => Activity::count(),
            'emails'       => Activity::where('log_name', 'email')->count(),
            'applications' => Activity::where('log_name', 'application')->count(),
            'auth'         => Activity::where('log_name', 'auth')->count(),
            'scholars'     => Activity::where('log_name', 'scholar')->count(),
        ];
    }
}
