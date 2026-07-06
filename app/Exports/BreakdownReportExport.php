<?php

namespace App\Exports;

use App\Models\Application;
use App\Support\DistrictHelper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

/**
 * Produces aggregated (grouped & counted) breakdown reports.
 *
 * Supported report types:
 *   breakdown_by_region        – Major region counts (Central / Eastern / Northern / Western)
 *   breakdown_by_subregion     – Subregion counts
 *   breakdown_by_district      – Residence district counts
 *   breakdown_by_university    – Institution / university counts
 *   breakdown_by_country       – Country of residence counts (plus nationality grouping)
 *   breakdown_by_nationality   – Ugandan vs Non-Ugandan (with country breakdown)
 */
class BreakdownReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected string $reportType;
    protected array  $filters;

    // Canonical institution keyword → display name (mirrors ApplicationsByUniversityChart)
    private const KEYWORD_MAP = [
        'makerere'                      => 'Makerere University',
        'kyambogo'                      => 'Kyambogo University',
        'kyam'                          => 'Kyambogo University',
        'busitema'                      => 'Busitema University',
        'islamic university in uganda'  => 'Islamic University in Uganda',
        'islamic university'            => 'Islamic University in Uganda',
        'iuiu'                          => 'Islamic University in Uganda',
        'gulu university'               => 'Gulu University',
        'mountains of the moon'         => 'Mountains of the Moon University',
        'mmu'                           => 'Mountains of the Moon University',
        'mbarara university of science' => 'Mbarara University of Science and Technology',
        'mbarara university'            => 'Mbarara University of Science and Technology',
        'must'                          => 'Mbarara University of Science and Technology',
        'uganda martyrs'                => 'Uganda Martyrs University',
        'umu'                           => 'Uganda Martyrs University',
        'kabale university'             => 'Kabale University',
        'unite kabale'                  => 'UNITE Kabale Campus',
        'unite kaliro'                  => 'UNITE Kaliro Campus',
        'kaliro'                        => 'UNITE Kaliro Campus',
        'unite mubende'                 => 'UNITE Mubende Campus',
        'unite muni'                    => 'UNITE Muni Campus',
        'unite unyama'                  => 'UNITE Unyama Campus',
        'unyama'                        => 'UNITE Unyama Campus',
        'muni university'               => 'Muni University',
    ];

    public function __construct(string $reportType, array $filters = [])
    {
        $this->reportType = $reportType;
        $this->filters    = $filters;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Sheet title
    // ─────────────────────────────────────────────────────────────────────────

    public function title(): string
    {
        return match ($this->reportType) {
            'breakdown_by_region'      => 'By Region',
            'breakdown_by_subregion'   => 'By Subregion',
            'breakdown_by_district'    => 'By District',
            'breakdown_by_university'  => 'By University',
            'breakdown_by_country'     => 'By Country',
            'breakdown_by_nationality' => 'By Nationality',
            default                    => 'Breakdown',
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Headings
    // ─────────────────────────────────────────────────────────────────────────

    public function headings(): array
    {
        return match ($this->reportType) {
            'breakdown_by_region' => [
                'Region', 'Total Applications', '% of Total',
                'Submitted', 'Under Review', 'Approved', 'Rejected',
            ],
            'breakdown_by_subregion' => [
                'Region', 'Subregion', 'Total Applications', '% of Total',
                'Submitted', 'Under Review', 'Approved', 'Rejected',
            ],
            'breakdown_by_district' => [
                'Subregion', 'District (as entered)', 'Total Applications', '% of Total',
                'Submitted', 'Under Review', 'Approved', 'Rejected',
            ],
            'breakdown_by_university' => [
                'University / Institution', 'Total Applications', '% of Total',
                'Submitted', 'Under Review', 'Approved', 'Rejected',
            ],
            'breakdown_by_country' => [
                'Country', 'Total Applications', '% of Total',
                'Submitted', 'Under Review', 'Approved', 'Rejected',
            ],
            'breakdown_by_nationality' => [
                'Nationality', 'Country / Details', 'Total Applications', '% of Total',
                'Submitted', 'Under Review', 'Approved', 'Rejected',
            ],
            default => ['Group', 'Total Applications', '% of Total'],
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Collection — returns aggregated rows (not raw application records)
    // ─────────────────────────────────────────────────────────────────────────

    public function collection(): Collection
    {
        $apps = $this->queryApplications();

        return match ($this->reportType) {
            'breakdown_by_region'      => $this->groupByRegion($apps),
            'breakdown_by_subregion'   => $this->groupBySubregion($apps),
            'breakdown_by_district'    => $this->groupByDistrict($apps),
            'breakdown_by_university'  => $this->groupByUniversity($apps),
            'breakdown_by_country'     => $this->groupByCountry($apps),
            'breakdown_by_nationality' => $this->groupByNationality($apps),
            default                    => collect(),
        };
    }

    // Row mapping — rows are already arrays, just return as-is
    public function map($row): array
    {
        return is_array($row) ? $row : (array) $row;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Query helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function queryApplications(): Collection
    {
        $query = Application::query()->whereNotIn('status', ['draft']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }
        if (!empty($this->filters['gender'])) {
            $prefix = $this->filters['gender'] === 'female' ? 'CF' : 'CM';
            $query->where('personal_info->nin', 'like', $prefix . '%');
        }
        if (!empty($this->filters['nationality'])) {
            if ($this->filters['nationality'] === 'ugandan') {
                $query->where('personal_info->is_ugandan', 'yes');
            } else {
                $query->where('personal_info->is_ugandan', 'no');
            }
        }

        return $query->get(['personal_info', 'status']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Status helpers
    // ─────────────────────────────────────────────────────────────────────────

    /** Initialise a per-status counter bag. */
    private function emptyBag(): array
    {
        return ['total' => 0, 'submitted' => 0, 'under_review' => 0, 'approved' => 0, 'rejected' => 0];
    }

    private function incrementBag(array &$bag, string $status): void
    {
        $bag['total']++;
        if (isset($bag[$status])) $bag[$status]++;
    }

    /** Convert bags into exportable rows. */
    private function bagsToRows(array $bags, int $grand, callable $rowBuilder): Collection
    {
        arsort($bags);  // sort by first element of bag (total) via usort below
        uasort($bags, fn ($a, $b) => $b['total'] <=> $a['total']);

        $rows = [];
        foreach ($bags as $key => $bag) {
            $pct     = $grand > 0 ? round($bag['total'] / $grand * 100, 1) : 0;
            $rows[]  = $rowBuilder($key, $bag, $pct);
        }

        // Totals footer
        $totals = $this->emptyBag();
        foreach ($bags as $bag) {
            foreach (array_keys($totals) as $k) $totals[$k] += $bag[$k];
        }

        $rows[] = $rowBuilder('TOTAL', $totals, 100.0);

        return collect($rows);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Grouping methods
    // ─────────────────────────────────────────────────────────────────────────

    private function groupByRegion(Collection $apps): Collection
    {
        $bags  = [];
        $grand = 0;

        foreach ($apps as $app) {
            $raw       = trim((string) ($app->personal_info['residence_district'] ?? ''));
            $subregion = DistrictHelper::subregion($raw);
            $region    = DistrictHelper::region($subregion);

            if (!isset($bags[$region])) $bags[$region] = $this->emptyBag();
            $this->incrementBag($bags[$region], $app->status);
            $grand++;
        }

        return $this->bagsToRows($bags, $grand, fn ($key, $bag, $pct) => [
            $key, $bag['total'], $pct . '%',
            $bag['submitted'], $bag['under_review'], $bag['approved'], $bag['rejected'],
        ]);
    }

    private function groupBySubregion(Collection $apps): Collection
    {
        $bags  = [];
        $grand = 0;

        foreach ($apps as $app) {
            $raw       = trim((string) ($app->personal_info['residence_district'] ?? ''));
            $subregion = DistrictHelper::subregion($raw);
            $region    = DistrictHelper::region($subregion);
            $label     = $subregion;

            if (!isset($bags[$label])) $bags[$label] = $this->emptyBag() + ['region' => $region];
            $this->incrementBag($bags[$label], $app->status);
            $grand++;
        }

        uasort($bags, fn ($a, $b) => $b['total'] <=> $a['total']);

        $rows = [];
        foreach ($bags as $subregion => $bag) {
            $pct    = $grand > 0 ? round($bag['total'] / $grand * 100, 1) : 0;
            $rows[] = [
                $bag['region'], $subregion, $bag['total'], $pct . '%',
                $bag['submitted'], $bag['under_review'], $bag['approved'], $bag['rejected'],
            ];
        }

        // Totals row
        $t = $this->emptyBag();
        foreach ($bags as $b) { foreach (array_keys($t) as $k) $t[$k] += $b[$k]; }
        $rows[] = ['', 'TOTAL', $t['total'], '100%',
            $t['submitted'], $t['under_review'], $t['approved'], $t['rejected']];

        return collect($rows);
    }

    private function groupByDistrict(Collection $apps): Collection
    {
        $bags  = [];
        $grand = 0;

        foreach ($apps as $app) {
            $raw       = trim((string) ($app->personal_info['residence_district'] ?? ''));
            $label     = $raw !== '' ? ucwords(strtolower($raw)) : 'Not Specified';
            $subregion = DistrictHelper::subregion($raw);

            if (!isset($bags[$label])) $bags[$label] = $this->emptyBag() + ['subregion' => $subregion];
            $this->incrementBag($bags[$label], $app->status);
            $grand++;
        }

        uasort($bags, fn ($a, $b) => $b['total'] <=> $a['total']);

        $rows = [];
        foreach ($bags as $district => $bag) {
            $pct    = $grand > 0 ? round($bag['total'] / $grand * 100, 1) : 0;
            $rows[] = [
                $bag['subregion'], $district, $bag['total'], $pct . '%',
                $bag['submitted'], $bag['under_review'], $bag['approved'], $bag['rejected'],
            ];
        }

        $t = $this->emptyBag();
        foreach ($bags as $b) { foreach (array_keys($t) as $k) $t[$k] += $b[$k]; }
        $rows[] = ['', 'TOTAL', $t['total'], '100%',
            $t['submitted'], $t['under_review'], $t['approved'], $t['rejected']];

        return collect($rows);
    }

    private function groupByUniversity(Collection $apps): Collection
    {
        $bags  = [];
        $grand = 0;

        foreach ($apps as $app) {
            $raw   = trim((string) ($app->personal_info['institution'] ?? ''));
            $label = $this->normaliseUniversity($raw);

            if (!isset($bags[$label])) $bags[$label] = $this->emptyBag();
            $this->incrementBag($bags[$label], $app->status);
            $grand++;
        }

        return $this->bagsToRows($bags, $grand, fn ($key, $bag, $pct) => [
            $key, $bag['total'], $pct . '%',
            $bag['submitted'], $bag['under_review'], $bag['approved'], $bag['rejected'],
        ]);
    }

    private function groupByCountry(Collection $apps): Collection
    {
        $bags  = [];
        $grand = 0;

        foreach ($apps as $app) {
            $raw   = trim((string) ($app->personal_info['residence_country'] ?? ''));
            $label = $raw !== '' ? ucwords(strtolower($raw)) : 'Not Specified';

            if (!isset($bags[$label])) $bags[$label] = $this->emptyBag();
            $this->incrementBag($bags[$label], $app->status);
            $grand++;
        }

        return $this->bagsToRows($bags, $grand, fn ($key, $bag, $pct) => [
            $key, $bag['total'], $pct . '%',
            $bag['submitted'], $bag['under_review'], $bag['approved'], $bag['rejected'],
        ]);
    }

    private function groupByNationality(Collection $apps): Collection
    {
        // Group by: Ugandan vs country of origin (non-Ugandan)
        $bags  = [];
        $grand = 0;

        foreach ($apps as $app) {
            $isUgandan = strtolower(trim((string) ($app->personal_info['is_ugandan'] ?? '')));
            $detail    = trim((string) ($app->personal_info['non_ugandan_explanation'] ?? ''));

            if ($isUgandan === 'yes') {
                $nationality = 'Ugandan';
                $country     = 'Uganda';
            } elseif ($isUgandan === 'no') {
                $nationality = 'Non-Ugandan';
                $country     = $detail !== '' ? ucwords(strtolower($detail)) : 'Non-Ugandan (unspecified)';
            } else {
                $nationality = 'Not Specified';
                $country     = '—';
            }

            $key = $nationality . '|' . $country;
            if (!isset($bags[$key])) {
                $bags[$key] = $this->emptyBag() + ['nationality' => $nationality, 'country' => $country];
            }
            $this->incrementBag($bags[$key], $app->status);
            $grand++;
        }

        uasort($bags, fn ($a, $b) => $b['total'] <=> $a['total']);

        $rows = [];
        foreach ($bags as $bag) {
            $pct    = $grand > 0 ? round($bag['total'] / $grand * 100, 1) : 0;
            $rows[] = [
                $bag['nationality'], $bag['country'], $bag['total'], $pct . '%',
                $bag['submitted'], $bag['under_review'], $bag['approved'], $bag['rejected'],
            ];
        }

        $t = $this->emptyBag();
        foreach ($bags as $b) { foreach (array_keys($t) as $k) $t[$k] += $b[$k]; }
        $rows[] = ['TOTAL', '', $t['total'], '100%',
            $t['submitted'], $t['under_review'], $t['approved'], $t['rejected']];

        return collect($rows);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // University normalisation
    // ─────────────────────────────────────────────────────────────────────────

    private function normaliseUniversity(string $raw): string
    {
        if ($raw === '') return 'Not Specified';

        $lower = mb_strtolower($raw);
        foreach (self::KEYWORD_MAP as $keyword => $canonical) {
            if (str_contains($lower, $keyword)) {
                return $canonical;
            }
        }

        // Return as-is (title-cased) if not recognised
        return ucwords(strtolower($raw));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Styles
    // ─────────────────────────────────────────────────────────────────────────

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        return [
            1         => ['font' => ['bold' => true]],
            $lastRow  => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DBEAFE'],
            ]],
        ];
    }
}
