<?php

namespace App\Filament\Pages;

use App\Exports\BreakdownReportExport;
use App\Exports\GeneralBreakdownExport;
use App\Exports\ReportExport;
use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

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
        'report_type'       => null,
        'status'            => null,
        'gender'            => null,
        'nationality'       => null,
        'date_from'         => null,
        'date_to'           => null,
        // Granular filters for per-applicant report types
        'university_filter' => null,
        'district_filter'   => null,
        'gender_filter'     => null,
        // Split-by-group option (downloads a zip of separate reports)
        'split_by_group'    => false,
        'zip_format'        => 'pdf',   // 'pdf' or 'excel'
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

    /** Individual breakdown types still used by legacy preview + makeExport(). */
    private const BREAKDOWN_TYPES = [
        'breakdown_by_region',
        'breakdown_by_subregion',
        'breakdown_by_district',
        'breakdown_by_university',
        'breakdown_by_country',
        'breakdown_by_nationality',
        'breakdown_by_disability',
        'breakdown_by_refugee',
        'breakdown_by_entry_level',
    ];

    /** Ordered list used to build the combined PDF/Excel exports. */
    private const GENERAL_BREAKDOWN_ORDER = [
        'breakdown_by_country',
        'breakdown_by_region',
        'breakdown_by_subregion',
        'breakdown_by_district',
        'breakdown_by_university',
        'breakdown_by_nationality',
        'breakdown_by_disability',
        'breakdown_by_refugee',
        'breakdown_by_entry_level',
    ];

    private const GENERAL_BREAKDOWN_TITLES = [
        'breakdown_by_country'     => 'Breakdown by Country',
        'breakdown_by_region'      => 'Breakdown by Region',
        'breakdown_by_subregion'   => 'Breakdown by Subregion',
        'breakdown_by_district'    => 'Breakdown by District',
        'breakdown_by_university'  => 'Breakdown by University / Institution',
        'breakdown_by_nationality' => 'Breakdown by Nationality',
        'breakdown_by_disability'  => 'Breakdown by Disability',
        'breakdown_by_refugee'     => 'Breakdown by Refugee Status',
        'breakdown_by_entry_level' => 'Breakdown by Entry Level',
    ];

    private function isBreakdown(): bool
    {
        return in_array($this->data['report_type'] ?? null, self::BREAKDOWN_TYPES, true);
    }

    private function isGeneralBreakdown(): bool
    {
        return in_array(
            $this->data['report_type'] ?? null,
            ['general_breakdown_pdf', 'general_breakdown_excel'],
            true
        );
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
                                    'general_breakdown_pdf'   => 'General Breakdown (PDF)',
                                    'general_breakdown_excel' => 'General Breakdown (Excel)',
                                ],
                            ])
                            ->native(false)
                            ->placeholder('Select a report type…')
                            ->live()
                            ->columnSpan(2),

                        // ── University filter (shown only for university_report) ──────────
                        Select::make('university_filter')
                            ->label('Filter by University')
                            ->options(fn () => $this->getUniversityOptions())
                            ->native(false)
                            ->placeholder('All universities')
                            ->nullable()
                            ->searchable()
                            ->live()
                            ->visible(fn () => ($this->data['report_type'] ?? null) === 'university_report')
                            ->columnSpan(2),

                        // ── District filter (shown only for district_report) ──────────────
                        Select::make('district_filter')
                            ->label('Filter by District / Region')
                            ->options(fn () => $this->getDistrictOptions())
                            ->native(false)
                            ->placeholder('All districts')
                            ->nullable()
                            ->searchable()
                            ->live()
                            ->visible(fn () => ($this->data['report_type'] ?? null) === 'district_report')
                            ->columnSpan(2),

                        // ── Gender filter (shown only for gender_report) ──────────────────
                        Select::make('gender_filter')
                            ->label('Filter by Gender')
                            ->options([
                                'Female' => 'Female',
                                'Male'   => 'Male',
                            ])
                            ->native(false)
                            ->placeholder('All genders')
                            ->nullable()
                            ->live()
                            ->visible(fn () => ($this->data['report_type'] ?? null) === 'gender_report')
                            ->columnSpan(2),

                        // ── Split-by-group toggle (shown for the three per-applicant types) ─
                        Toggle::make('split_by_group')
                            ->label(fn () => match ($this->data['report_type'] ?? null) {
                                'university_report' => 'Split into separate reports per university (download as ZIP)',
                                'district_report'   => 'Split into separate reports per district (download as ZIP)',
                                'gender_report'     => 'Split into separate reports per gender (download as ZIP)',
                                default             => 'Split into separate reports (download as ZIP)',
                            })
                            ->default(false)
                            ->live()
                            ->visible(fn () => in_array(
                                $this->data['report_type'] ?? null,
                                ['university_report', 'district_report', 'gender_report'],
                                true
                            ))
                            ->columnSpan(2),

                        // ── ZIP format selector (only when split mode is on) ─────────────
                        Select::make('zip_format')
                            ->label('ZIP file format')
                            ->options([
                                'pdf'   => 'PDF files',
                                'excel' => 'Excel files (.xlsx)',
                            ])
                            ->native(false)
                            ->default('pdf')
                            ->live()
                            ->visible(fn () => $this->isSplitMode())
                            ->columnSpan(1),

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
                            ->live(onBlur: true),

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
            'status'            => $this->data['status']            ?? null,
            'gender'            => $this->data['gender']            ?? null,
            'nationality'       => $this->data['nationality']       ?? null,
            'date_from'         => $this->data['date_from']         ?? null,
            'date_to'           => $this->data['date_to']           ?? null,
            'university_filter' => $this->data['university_filter'] ?? null,
            'district_filter'   => $this->data['district_filter']   ?? null,
            'gender_filter'     => $this->data['gender_filter']     ?? null,
        ]);
    }

    /**
     * Returns a unique sorted list of normalised university names from the DB,
     * used to populate the university filter select.
     */
    private function getUniversityOptions(): array
    {
        $raw = Application::whereNotIn('status', ['draft'])
            ->pluck('personal_info')
            ->map(fn ($p) => trim((string) ($p['institution'] ?? '')))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $options = [];
        foreach ($raw as $name) {
            $options[$name] = $name;
        }

        return $options;
    }

    /**
     * Returns a unique sorted list of residence districts from the DB,
     * used to populate the district filter select.
     */
    private function getDistrictOptions(): array
    {
        $raw = Application::whereNotIn('status', ['draft'])
            ->pluck('personal_info')
            ->map(fn ($p) => ucwords(strtolower(trim((string) ($p['residence_district'] ?? '')))))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $options = [];
        foreach ($raw as $name) {
            $options[$name] = $name;
        }

        return $options;
    }

    /**
     * Determine the distinct group values for splitting.
     * Returns [ 'GroupLabel' => filterValue ] pairs.
     */
    private function getSplitGroups(): array
    {
        $type = $this->data['report_type'] ?? null;

        $apps = Application::whereNotIn('status', ['draft'])
            ->when(!empty($this->data['status']), fn ($q) => $q->where('status', $this->data['status']))
            ->when(!empty($this->data['date_from']), fn ($q) => $q->whereDate('created_at', '>=', $this->data['date_from']))
            ->when(!empty($this->data['date_to']), fn ($q) => $q->whereDate('created_at', '<=', $this->data['date_to']))
            ->when(!empty($this->data['gender']), function ($q) {
                $prefix = $this->data['gender'] === 'female' ? 'CF' : 'CM';
                $q->where('personal_info->nin', 'like', $prefix . '%');
            })
            ->when(!empty($this->data['nationality']), function ($q) {
                $q->where('personal_info->is_ugandan', $this->data['nationality'] === 'ugandan' ? 'yes' : 'no');
            })
            ->pluck('personal_info');

        if ($type === 'university_report') {
            return $apps
                ->map(fn ($p) => trim((string) ($p['institution'] ?? '')))
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->mapWithKeys(fn ($v) => [$v => $v])
                ->all();
        }

        if ($type === 'district_report') {
            return $apps
                ->map(fn ($p) => ucwords(strtolower(trim((string) ($p['residence_district'] ?? '')))))
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->mapWithKeys(fn ($v) => [$v => $v])
                ->all();
        }

        if ($type === 'gender_report') {
            return [
                'Female' => 'Female',
                'Male'   => 'Male',
            ];
        }

        return [];
    }

    private function filterSummary(): string
    {
        $parts = [];

        $status          = $this->data['status']            ?? null;
        $gender          = $this->data['gender']            ?? null;
        $nationality     = $this->data['nationality']       ?? null;
        $dateFrom        = $this->data['date_from']         ?? null;
        $dateTo          = $this->data['date_to']           ?? null;
        $universityFilter = $this->data['university_filter'] ?? null;
        $districtFilter  = $this->data['district_filter']   ?? null;
        $genderFilter    = $this->data['gender_filter']     ?? null;

        if ($universityFilter) $parts[] = 'University: '       . $universityFilter;
        if ($districtFilter)   $parts[] = 'District: '         . $districtFilter;
        if ($genderFilter)     $parts[] = 'Gender group: '     . $genderFilter;
        if ($status)           $parts[] = 'Status: '           . ucwords(str_replace('_', ' ', $status));
        if ($gender)           $parts[] = 'Gender: '           . ucfirst($gender);
        if ($nationality)      $parts[] = 'Nationality: '      . ($nationality === 'ugandan' ? 'Ugandan' : 'Non-Ugandan');
        if ($dateFrom)         $parts[] = 'From: '             . \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        if ($dateTo)           $parts[] = 'To: '               . \Carbon\Carbon::parse($dateTo)->format('d M Y');

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
            'general_breakdown_pdf'    => 'General Breakdown Report (PDF)',
            'general_breakdown_excel'  => 'General Breakdown Report (Excel)',
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

        // The two combined breakdown types have their own dedicated export methods.
        // makeExport() should never be called with them, but guard defensively.
        if ($this->isGeneralBreakdown()) {
            throw new \LogicException('Use exportGeneralBreakdownPdf/Excel for combined breakdown types.');
        }

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

    /**
     * Export one PDF or Excel file per group (university / district / gender)
     * and bundle them into a ZIP archive for download.
     * Format is controlled by $this->data['zip_format'] ('pdf' or 'excel').
     */
    public function exportZip(): StreamedResponse
    {
        if (!$this->validateForm()) {
            return response()->streamDownload(fn () => null, 'error.zip');
        }

        $type      = $this->data['report_type'];
        $groups    = $this->getSplitGroups();
        $format    = $this->data['zip_format'] ?? 'pdf';   // 'pdf' | 'excel'

        if (empty($groups)) {
            Notification::make()
                ->title('No groups found to split by')
                ->warning()
                ->send();

            return response()->streamDownload(fn () => null, 'empty.zip');
        }

        $tmpDir  = sys_get_temp_dir() . '/report_zip_' . uniqid();
        mkdir($tmpDir, 0755, true);
        $zipPath = $tmpDir . '/reports.zip';

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $baseFilters = $this->buildFilters();
        $filterKey   = match ($type) {
            'university_report' => 'university_filter',
            'district_report'   => 'district_filter',
            'gender_report'     => 'gender_filter',
            default             => '__noop__',
        };

        foreach ($groups as $label => $value) {
            $groupFilters = array_merge($baseFilters, [$filterKey => $value]);
            $export       = new ReportExport($type, $groupFilters);
            $safe         = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $label);

            if ($format === 'excel') {
                $fileName = $type . '_' . $safe . '_' . now()->format('Y-m-d') . '.xlsx';
                $filePath = $tmpDir . '/' . $fileName;

                $raw = \Maatwebsite\Excel\Facades\Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);
                file_put_contents($filePath, $raw);
            } else {
                // PDF (default)
                $groupTitle = $this->reportTitle() . ' — ' . $label;
                $summary    = $this->filterSummary();
                if ($summary === 'None (all submitted applications)') {
                    $summary = $groupTitle;
                }

                $headings = $export->headings();
                $rows     = $export->collection()->map(fn ($row) => $export->map($row))->toArray();

                $pdf = Pdf::loadView('reports.pdf', [
                    'title'         => $groupTitle,
                    'headings'      => $headings,
                    'rows'          => $rows,
                    'filterSummary' => $summary,
                ])->setPaper('a4', 'landscape');

                $fileName = $type . '_' . $safe . '_' . now()->format('Y-m-d') . '.pdf';
                $filePath = $tmpDir . '/' . $fileName;

                file_put_contents($filePath, $pdf->output());
            }

            $zip->addFile($filePath, $fileName);
        }

        $zip->close();

        $zipFilename = $type . '_split_' . now()->format('Y-m-d_His') . '.zip';
        $zipContents = file_get_contents($zipPath);

        foreach (glob($tmpDir . '/*') as $f) {
            @unlink($f);
        }
        @rmdir($tmpDir);

        return response()->streamDownload(
            fn () => print($zipContents),
            $zipFilename,
            ['Content-Type' => 'application/zip']
        );
    }

    // ── Combined general breakdown exports ───────────────────────────────────

    /**
     * Download a single PDF containing all 9 breakdown sections,
     * one per page, in the canonical order defined by GENERAL_BREAKDOWN_ORDER.
     */
    public function exportGeneralBreakdownPdf(): StreamedResponse
    {
        $filters = $this->buildFilters();
        $sections = [];

        foreach (self::GENERAL_BREAKDOWN_ORDER as $type) {
            $export = new BreakdownReportExport($type, $filters);
            $rows   = $export->collection()->map(fn ($row) => $export->map($row))->toArray();

            $sections[] = [
                'title'                  => self::GENERAL_BREAKDOWN_TITLES[$type],
                'headings'               => $export->headings(),
                'rows'                   => $rows,
                'is_total_row_included'  => count($rows) > 0,
            ];
        }

        $pdf = Pdf::loadView('reports.breakdown_pdf', [
            'sections'      => $sections,
            'filterSummary' => $this->filterSummary(),
        ])->setPaper('a4', 'landscape');

        $filename = 'general_breakdown_' . now()->format('Y-m-d_His') . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }

    /**
     * Download a single Excel workbook with all 9 breakdown types as separate
     * sheets, in the canonical order defined by GENERAL_BREAKDOWN_ORDER.
     */
    public function exportGeneralBreakdownExcel(): BinaryFileResponse
    {
        $filters  = $this->buildFilters();
        $filename = 'general_breakdown_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new GeneralBreakdownExport($filters), $filename);
    }

    // ── Reactive preview ──────────────────────────────────────────────────────

    /**
     * Whether "split into separate reports" is active for the current type.
     */
    public function isSplitMode(): bool
    {
        return !empty($this->data['split_by_group'])
            && in_array(
                $this->data['report_type'] ?? null,
                ['university_report', 'district_report', 'gender_report'],
                true
            );
    }

    /**
     * Called from the Blade view on every render.
     * Because all form fields are ->live(), any change triggers a re-render,
     * which calls this method with the latest $this->data values.
     */
    public function getPreviewData(): array
    {
        $reportType = $this->data['report_type'] ?? null;

        if (empty($reportType)) {
            return ['headings' => [], 'rows' => [], 'total' => 0, 'is_breakdown' => false, 'is_general_breakdown' => false];
        }

        // Combined breakdown types — no single table preview; show description instead.
        if ($this->isGeneralBreakdown()) {
            return [
                'headings'             => [],
                'rows'                 => [],
                'total'                => 0,
                'is_breakdown'         => false,
                'is_general_breakdown' => true,
                'format'               => $reportType === 'general_breakdown_pdf' ? 'PDF' : 'Excel',
            ];
        }

        $export   = $this->makeExport();
        $headings = $export->headings();
        $allRows  = $export->collection();
        $total    = $allRows->count();

        // Breakdown: show all rows (small set). Detail: cap at 15 for preview.
        $preview = $this->isBreakdown() ? $allRows : $allRows->take(15);
        $rows    = $preview->map(fn ($row) => $export->map($row))->toArray();

        return [
            'headings'             => $headings,
            'rows'                 => $rows,
            'total'                => $total,
            'is_breakdown'         => $this->isBreakdown(),
            'is_general_breakdown' => false,
        ];
    }
}
