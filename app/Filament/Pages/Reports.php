<?php

namespace App\Filament\Pages;

use App\Exports\BreakdownReportExport;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?string $title           = 'Reports';
    protected static ?string $navigationGroup = 'Application Management';
    protected static ?int    $navigationSort  = 10;
    protected static string  $view            = 'filament.pages.reports';

    // ── Filament form data bag ────────────────────────────────────────────────
    // Using statePath('data') so Filament stores everything in $this->data.
    // Livewire calls updatedData() on every field change, which triggers a
    // full component re-render — giving us live preview updates.

    public array $data = [
        'report_type' => null,
        'status'      => null,
        'gender'      => null,
        'nationality' => null,
        'date_from'   => null,
        'date_to'     => null,
    ];

    // ── Access control ────────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return auth()->user()->can('report.view');
    }

    // ── Livewire lifecycle: fires on every field change ───────────────────────

    /**
     * Called by Livewire whenever any key inside $data changes.
     * The empty body is intentional — Livewire re-renders the component
     * automatically after this hook runs, which is all we need.
     */
    public function updatedData(): void
    {
        // Intentionally empty — the re-render happens automatically.
    }

    // ── Report type groups ────────────────────────────────────────────────────

    private const BREAKDOWN_TYPES = [
        'breakdown_by_region',
        'breakdown_by_subregion',
        'breakdown_by_district',
        'breakdown_by_university',
        'breakdown_by_country',
        'breakdown_by_nationality',
    ];

    private function isBreakdown(): bool
    {
        return in_array($this->data['report_type'] ?? null, self::BREAKDOWN_TYPES, true);
    }

    // ── Form definition ───────────────────────────────────────────────────────

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Report Configuration')
                    ->description('Results update automatically as you change the filters below.')
                    ->schema([
                        Select::make('report_type')
                            ->label('Report Type')
                            ->required()
                            ->options([
                                'Detailed Reports' => [
                                    'applications_summary' => 'Applications Summary',
                                    'applicant_details'    => 'Applicant Details',
                                    'scoring_report'       => 'Scoring Report',
                                    'district_report'      => 'District / Region (per applicant)',
                                    'university_report'    => 'University (per applicant)',
                                    'gender_report'        => 'Gender (per applicant)',
                                    'financial_report'     => 'Financial Needs Report',
                                    'approved_scholars'    => 'Approved Scholars List',
                                ],
                                'Breakdown / Summary Reports' => [
                                    'breakdown_by_region'      => 'Breakdown by Region',
                                    'breakdown_by_subregion'   => 'Breakdown by Subregion',
                                    'breakdown_by_district'    => 'Breakdown by District',
                                    'breakdown_by_university'  => 'Breakdown by University / Institution',
                                    'breakdown_by_country'     => 'Breakdown by Country',
                                    'breakdown_by_nationality' => 'Breakdown by Nationality',
                                ],
                            ])
                            ->native(false)
                            ->placeholder('Select a report type…')
                            ->live()          // emit Livewire update immediately on change
                            ->columnSpan(2),

                        Select::make('status')
                            ->label('Filter by Status')
                            ->options([
                                'submitted'    => 'Submitted',
                                'under_review' => 'Under Review',
                                'approved'     => 'Approved',
                                'rejected'     => 'Rejected',
                            ])
                            ->native(false)
                            ->placeholder('All statuses (excl. drafts)')
                            ->nullable()
                            ->live(),

                        Select::make('gender')
                            ->label('Filter by Gender')
                            ->options([
                                'female' => 'Female',
                                'male'   => 'Male',
                            ])
                            ->native(false)
                            ->placeholder('All genders')
                            ->nullable()
                            ->live(),

                        Select::make('nationality')
                            ->label('Filter by Nationality')
                            ->options([
                                'ugandan'     => 'Ugandan',
                                'non_ugandan' => 'Non-Ugandan',
                            ])
                            ->native(false)
                            ->placeholder('All nationalities')
                            ->nullable()
                            ->live(),

                        DatePicker::make('date_from')
                            ->label('Submitted From')
                            ->nullable()
                            ->displayFormat('d/m/Y')
                            ->live(onBlur: true),   // update when user leaves the field

                        DatePicker::make('date_to')
                            ->label('Submitted To')
                            ->nullable()
                            ->displayFormat('d/m/Y')
                            ->live(onBlur: true),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function buildFilters(): array
    {
        return array_filter([
            'status'      => $this->data['status']      ?? null,
            'gender'      => $this->data['gender']      ?? null,
            'nationality' => $this->data['nationality'] ?? null,
            'date_from'   => $this->data['date_from']   ?? null,
            'date_to'     => $this->data['date_to']     ?? null,
        ]);
    }

    private function filterSummary(): string
    {
        $parts = [];

        $status      = $this->data['status']      ?? null;
        $gender      = $this->data['gender']      ?? null;
        $nationality = $this->data['nationality'] ?? null;
        $dateFrom    = $this->data['date_from']   ?? null;
        $dateTo      = $this->data['date_to']     ?? null;

        if ($status)      $parts[] = 'Status: '      . ucwords(str_replace('_', ' ', $status));
        if ($gender)      $parts[] = 'Gender: '      . ucfirst($gender);
        if ($nationality) $parts[] = 'Nationality: ' . ($nationality === 'ugandan' ? 'Ugandan' : 'Non-Ugandan');
        if ($dateFrom)    $parts[] = 'From: '        . \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        if ($dateTo)      $parts[] = 'To: '          . \Carbon\Carbon::parse($dateTo)->format('d M Y');

        return implode(' | ', $parts) ?: 'None (all submitted applications)';
    }

    private function reportTitle(): string
    {
        return match ($this->data['report_type'] ?? null) {
            'applications_summary'     => 'Applications Summary Report',
            'applicant_details'        => 'Applicant Details Report',
            'scoring_report'           => 'Scoring Report',
            'district_report'          => 'Applications by District / Region',
            'university_report'        => 'Applications by University',
            'gender_report'            => 'Applications by Gender',
            'financial_report'         => 'Financial Needs Report',
            'approved_scholars'        => 'Approved Scholars List',
            'breakdown_by_region'      => 'Breakdown by Region',
            'breakdown_by_subregion'   => 'Breakdown by Subregion',
            'breakdown_by_district'    => 'Breakdown by District',
            'breakdown_by_university'  => 'Breakdown by University / Institution',
            'breakdown_by_country'     => 'Breakdown by Country',
            'breakdown_by_nationality' => 'Breakdown by Nationality',
            default                    => 'Report',
        };
    }

    private function validateForm(): bool
    {
        if (empty($this->data['report_type'])) {
            Notification::make()
                ->title('Please select a report type')
                ->warning()
                ->send();
            return false;
        }

        return true;
    }

    private function makeExport(): ReportExport|BreakdownReportExport
    {
        $type    = $this->data['report_type'];
        $filters = $this->buildFilters();

        return $this->isBreakdown()
            ? new BreakdownReportExport($type, $filters)
            : new ReportExport($type, $filters);
    }

    // ── Export actions ────────────────────────────────────────────────────────

    public function exportExcel(): BinaryFileResponse
    {
        if (!$this->validateForm()) {
            return response()->file(tempnam(sys_get_temp_dir(), 'rpt'));
        }

        $filename = ($this->data['report_type']) . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download($this->makeExport(), $filename);
    }

    public function exportPdf(): StreamedResponse
    {
        if (!$this->validateForm()) {
            return response()->streamDownload(fn () => null, 'error.pdf');
        }

        $export   = $this->makeExport();
        $headings = $export->headings();
        $rows     = $export->collection()->map(fn ($row) => $export->map($row))->toArray();

        $pdf = Pdf::loadView('reports.pdf', [
            'title'         => $this->reportTitle(),
            'headings'      => $headings,
            'rows'          => $rows,
            'filterSummary' => $this->filterSummary(),
        ])->setPaper('a4', 'landscape');

        $filename = ($this->data['report_type']) . '_' . now()->format('Y-m-d_His') . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }

    // ── Reactive preview ──────────────────────────────────────────────────────

    /**
     * Called from the Blade view on every render.
     * Because all form fields are ->live(), any change triggers a re-render,
     * which calls this method with the latest $this->data values.
     */
    public function getPreviewData(): array
    {
        $reportType = $this->data['report_type'] ?? null;

        if (empty($reportType)) {
            return ['headings' => [], 'rows' => [], 'total' => 0, 'is_breakdown' => false];
        }

        $export   = $this->makeExport();
        $headings = $export->headings();
        $allRows  = $export->collection();
        $total    = $allRows->count();

        // Breakdown: show all rows (small set). Detail: cap at 15 for preview.
        $preview = $this->isBreakdown() ? $allRows : $allRows->take(15);
        $rows    = $preview->map(fn ($row) => $export->map($row))->toArray();

        return [
            'headings'     => $headings,
            'rows'         => $rows,
            'total'        => $total,
            'is_breakdown' => $this->isBreakdown(),
        ];
    }
}
