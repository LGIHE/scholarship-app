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
use App\Models\User;
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
        'log_name'    => null,
        'event'       => null,
        'search'      => null,
        'date_from'   => null,
        'date_to'     => null,
        'error_limit' => '50',
        'tab'         => 'activity', // 'activity' | 'error'
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

    // ── Forms — one per tab so filters are contextual ─────────────────────────

    public function form(Form $form): Form
    {
        $tab = $this->data['tab'] ?? 'activity';

        $sharedFields = [
            TextInput::make('search')
                ->label('Search by name, email or description')
                ->placeholder('e.g. John Doe, john@example.com, "status updated"…')
                ->prefixIcon('heroicon-o-magnifying-glass')
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
        ];

        if ($tab === 'activity') {
            $schema = [
                Select::make('log_name')
                    ->label('Channel')
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

                ...$sharedFields,
            ];
            $columns = 5;
        } else {
            // Error tab — channel/event irrelevant; show limit instead
            $schema = [
                Select::make('error_limit')
                    ->label('Show most recent')
                    ->options([
                        '25'  => '25 entries',
                        '50'  => '50 entries',
                        '100' => '100 entries',
                        '200' => '200 entries',
                        '500' => '500 entries',
                    ])
                    ->native(false)
                    ->live(),

                ...$sharedFields,
            ];
            $columns = 4;
        }

        return $form
            ->schema([
                Section::make()
                    ->schema($schema)
                    ->columns($columns),
            ])
            ->statePath('data');
    }

    // ── Activity log query ────────────────────────────────────────────────────

    public function getActivityLogs(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $search = trim($this->data['search'] ?? '');

        $query = Activity::with('causer', 'subject')
            ->orderByDesc('created_at');

        if ($logName = $this->data['log_name'] ?? null) {
            $query->where('log_name', $logName);
        }

        if ($event = $this->data['event'] ?? null) {
            $query->where('event', $event);
        }

        if ($search !== '') {
            // Match the description OR the causer's name/email via a subquery
            $matchingCauserIds = User::where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->pluck('id');

            $query->where(function ($q) use ($search, $matchingCauserIds) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereIn('causer_id', $matchingCauserIds)
                  ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        if ($from = $this->data['date_from'] ?? null) {
            $query->whereDate('created_at', '>=', Carbon::parse($from));
        }

        if ($to = $this->data['date_to'] ?? null) {
            $query->whereDate('created_at', '<=', Carbon::parse($to));
        }

        return $query->paginate($this->perPage);
    }

    // ── Error log reader ──────────────────────────────────────────────────────

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

        // Most recent first
        $entries = array_reverse($entries);

        // Search: match message, stack trace, or any email/name in the full entry text
        if ($search = trim($this->data['search'] ?? '')) {
            $entries = array_filter($entries, function ($e) use ($search) {
                $haystack = $e['message'] . ' ' . $e['context'];
                return stripos($haystack, $search) !== false;
            });
        }

        // Date filters
        if ($from = $this->data['date_from'] ?? null) {
            $fromTs  = Carbon::parse($from)->startOfDay();
            $entries = array_filter($entries, fn ($e) => Carbon::parse($e['timestamp'])->gte($fromTs));
        }

        if ($to = $this->data['date_to'] ?? null) {
            $toTs    = Carbon::parse($to)->endOfDay();
            $entries = array_filter($entries, fn ($e) => Carbon::parse($e['timestamp'])->lte($toTs));
        }

        // Apply the "show most recent N" limit
        $limit = (int) ($this->data['error_limit'] ?? 50);

        return array_values(array_slice(array_values($entries), 0, $limit));
    }

    // ── Stats ─────────────────────────────────────────────────────────────────

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
