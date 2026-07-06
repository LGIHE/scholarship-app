<?php

namespace App\Filament\Pages;

use App\Exports\BreakdownReportExport;
use App\Exports\ReportExport;
use App\Models\Application;
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

    // ── Form state ────────────────────────────────────────────────────────────

    public ?string $report_type  = null;
    public ?string $status       = null;
    public ?string $gender       = null;
    public ?string $nationality  = null;
    public ?string $date_from    = null;
    public ?string $date_to      = null;

    // ── Access control ────────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return auth()->user()->can('report.view');
    }

    // ── Report type groups ────────────────────────────────────────────────────

    /**
     * Report types that produce aggregated/breakdown rows (not per-applicant rows).
     */
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
        return in_array($this->report_type, self::BREAKDOWN_TYPES, true);
    }

    // ── Form definition ───────────────────────────────────────────────────────

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Report Configuration')
                    ->description('Choose the type of report and apply any filters before generating.')
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
                            ->nullable(),

                        Select::make('gender')
                            ->label('Filter by Gender')
                            ->options([
                                'female' => 'Female',
                                'male'   => 'Male',
                            ])
                            ->native(false)
                            ->placeholder('All genders')
                            ->nullable(),

                        Select::make('nationality')
                            ->label('Filter by Nationality')
                            ->options([
                                'ugandan'     => 'Ugandan',
                                'non_ugandan' => 'Non-Ugandan',
                            ])
                            ->native(false)
                            ->placeholder('All nationalities')
                            ->nullable(),

                        DatePicker::make('date_from')
                            ->label('Submitted From')
                            ->nullable()
                            ->displayFormat('d/m/Y'),

                        DatePicker::make('date_to')
                            ->label('Submitted To')
                            ->nullable()
                            ->displayFormat('d/m/Y'),
                    ])
                    ->columns(3),
            ])
            ->statePath('');   // bind directly to component properties
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function buildFilters(): array
    {
        return array_filter([
            'status'      => $this->status,
            'gender'      => $this->gender,
            'nationality' => $this->nationality,
            'date_from'   => $this->date_from,
            'date_to'     => $this->date_to,
        ]);
    }

    private function filterSummary(): string
    {
        $parts = [];

        if ($this->status)      $parts[] = 'Status: ' . ucwords(str_replace('_', ' ', $this->status));
        if ($this->gender)      $parts[] = 'Gender: ' . ucfirst($this->gender);
        if ($this->nationality) $parts[] = 'Nationality: ' . ($this->nationality === 'ugandan' ? 'Ugandan' : 'Non-Ugandan');
        if ($this->date_from)   $parts[] = 'From: ' . \Carbon\Carbon::parse($this->date_from)->format('d M Y');
        if ($this->date_to)     $parts[] = 'To: ' . \Carbon\Carbon::parse($this->date_to)->format('d M Y');

        return implode(' | ', $parts) ?: 'None (all submitted applications)';
    }

    private function reportTitle(): string
    {
        return match ($this->report_type) {
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
        if (empty($this->report_type)) {
            Notification::make()
                ->title('Please select a report type')
                ->warning()
                ->send();
            return false;
        }

        return true;
    }

    /** Build the correct export instance based on report type. */
    private function makeExport(): ReportExport|BreakdownReportExport
    {
        $filters = $this->buildFilters();

        return $this->isBreakdown()
            ? new BreakdownReportExport($this->report_type, $filters)
            : new ReportExport($this->report_type, $filters);
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function exportExcel(): BinaryFileResponse
    {
        if (!$this->validateForm()) {
            return response()->file(tempnam(sys_get_temp_dir(), 'rpt'));
        }

        $filename = $this->report_type . '_' . now()->format('Y-m-d_His') . '.xlsx';

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

        $filename = $this->report_type . '_' . now()->format('Y-m-d_His') . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }

    // ── Preview data ──────────────────────────────────────────────────────────

    public function getPreviewData(): array
    {
        if (empty($this->report_type)) {
            return ['headings' => [], 'rows' => [], 'total' => 0, 'is_breakdown' => false];
        }

        $export   = $this->makeExport();
        $headings = $export->headings();
        $allRows  = $export->collection();
        $total    = $allRows->count();

        // Breakdown reports: show all rows (typically small); detail reports: cap at 10
        $preview = $this->isBreakdown() ? $allRows : $allRows->take(10);
        $rows    = $preview->map(fn ($row) => $export->map($row))->toArray();

        return [
            'headings'     => $headings,
            'rows'         => $rows,
            'total'        => $total,
            'is_breakdown' => $this->isBreakdown(),
        ];
    }
}
