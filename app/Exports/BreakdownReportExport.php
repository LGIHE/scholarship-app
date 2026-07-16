<?php

namespace App\Exports;

use App\Models\Application;
use App\Support\ApprovedCriteria;
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
 *   breakdown_by_disability    – With disability / without / type breakdown
 *   breakdown_by_refugee       – Refugee vs non-refugee vs Ugandan
 *   breakdown_by_entry_level   – A-Level / Diploma / HEAC / Mature Entry admission route
 */
class BreakdownReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected string $reportType;
    protected array  $filters;

    // Canonical institution keyword → display name (mirrors ApplicationsByUniversityChart)
    private const KEYWORD_MAP = [
        'makerere'                      => 'Makerere University',
        'makarere'                      => 'Makerere University',
        'mubs'                          => 'Makerere University',
        'kyambogo'                      => 'Kyambogo University',
        'kyam'                          => 'Kyambogo University',
        'busitema'                      => 'Busitema University',
        // Islamic University — kabojja/females campus variants before generic patterns
        'kabojja'                       => 'Islamic University in Uganda',
        'females campus'                => 'Islamic University in Uganda',
        'islamic university in uganda'  => 'Islamic University in Uganda',
        'islamic university'            => 'Islamic University in Uganda',
        'iuiu'                          => 'Islamic University in Uganda',
        // Gulu University — bare "gulu" as well as full name
        'gulu university'               => 'Gulu University',
        'gulu'                          => 'Gulu University',
        // Mountains of the Moon — handle missing 's' and mixed-case variants
        'mountains of the moon'         => 'Mountains of the Moon University',
        'mountain of the moon'          => 'Mountains of the Moon University',
        'mmu'                           => 'Mountains of the Moon University',
        // Mbarara — bare "mbarara" catches "Mbarara school of science..." etc.
        'mbarara university of science' => 'Mbarara University of Science and Technology',
        'mbarara university'            => 'Mbarara University of Science and Technology',
        'mbarara school of science'     => 'Mbarara University of Science and Technology',
        'mbarara'                       => 'Mbarara University of Science and Technology',
        'must'                          => 'Mbarara University of Science and Technology',
        // Uganda Martyrs — "marty's" / "martys" typo variants
        'uganda martyrs'                => 'Uganda Martyrs University',
        'uganda marty'                  => 'Uganda Martyrs University',
        'umu'                           => 'Uganda Martyrs University',
        // Kabale University — before bare "kabale" and "kabaale" typo
        'kabale university'             => 'Kabale University',
        'kabaale university'            => 'Kabale University',
        // UNITE campuses — specific patterns before bare keywords
        'mubende unite'                 => 'UNITE Mubende Campus',
        'unite mubende'                 => 'UNITE Mubende Campus',
        'mubende'                       => 'UNITE Mubende Campus',
        'unite-kabale'                  => 'UNITE Kabale Campus',
        'unite campus (kabale'          => 'UNITE Kabale Campus',
        'unite kabale'                  => 'UNITE Kabale Campus',
        'unite kaliro'                  => 'UNITE Kaliro Campus',
        'kaliro'                        => 'UNITE Kaliro Campus',
        'unite muni'                    => 'UNITE Muni Campus',
        'unite unyama'                  => 'UNITE Unyama Campus',
        'unyama'                        => 'UNITE Unyama Campus',
        // Bare "kabale" → UNITE Kabale Campus (after "kabale university" above)
        'kabale'                        => 'UNITE Kabale Campus',
        // Muni University — bare "muni" after "unite muni" to avoid false match
        'muni university'               => 'Muni University',
        'muni'                          => 'Muni University',
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
            'breakdown_by_disability'  => 'By Disability',
            'breakdown_by_refugee'     => 'By Refugee Status',
            'breakdown_by_entry_level' => 'By Entry Level',
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
            'breakdown_by_disability' => [
                'Disability Status', 'Disability Type / Detail', 'Total Applications', '% of Total',
                'Submitted', 'Under Review', 'Approved', 'Rejected',
            ],
            'breakdown_by_refugee' => [
                'Category', 'Total Applications', '% of Total',
                'Submitted', 'Under Review', 'Approved', 'Rejected',
            ],
            'breakdown_by_entry_level' => [
                'Entry / Admission Level', 'Total Applications', '% of Total',
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
            'breakdown_by_disability'  => $this->groupByDisability($apps),
            'breakdown_by_refugee'     => $this->groupByRefugee($apps),
            'breakdown_by_entry_level' => $this->groupByEntryLevel($apps),
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

        // Cohort filter
        if (!empty($this->filters['cohort_id'])) {
            $query->where('cohort_id', $this->filters['cohort_id']);
        }

        // ── Eligibility filter (approved gender + course + subject) ──────────
        // Applied in PHP after the DB query because matching uses fuzzy keyword
        // logic that cannot be expressed in SQL on JSON fields.
        return $query->get(['personal_info', 'disability_info', 'status'])->filter(
            fn ($app) => ApprovedCriteria::isEligible($app->personal_info ?? [])
        )->values();
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

            // Skip blank or unrecognised institutions — they are not on the
            // approved list and must not appear in analytics or reports.
            if ($label === null) {
                continue;
            }

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
    // Breakdown by Disability
    // ─────────────────────────────────────────────────────────────────────────

    private function groupByDisability(Collection $apps): Collection
    {
        // Rows:
        //   "With Disability"  → broken down by disability type (from disability_info)
        //   "Without Disability"
        //   "Not Specified"
        $bags  = [];
        $grand = 0;

        // Disability difficulty labels
        $difficultyLabels = [
            'difficulty_walking'       => 'Physical / Walking',
            'difficulty_seeing'        => 'Visual',
            'difficulty_hearing'       => 'Hearing',
            'difficulty_communicating' => 'Communication / Speech',
            'difficulty_picking'       => 'Manual / Picking Objects',
            'difficulty_self_care'     => 'Self-Care',
            'difficulty_emotions'      => 'Psychosocial / Emotional',
        ];

        foreach ($apps as $app) {
            $p  = $app->personal_info   ?? [];
            $di = $app->disability_info ?? [];

            $hasDisability = strtolower(trim((string) ($p['has_disability'] ?? '')));

            if ($hasDisability === 'yes') {
                // Collect all marked difficulty types
                $types = [];
                foreach ($difficultyLabels as $field => $label) {
                    if (!empty($di[$field])) {
                        $types[] = $label;
                    }
                }

                // Functional level as extra context
                $level = trim((string) ($di['functionality_level'] ?? ''));

                if (!empty($types)) {
                    foreach ($types as $type) {
                        $key = 'With Disability|' . $type;
                        if (!isset($bags[$key])) {
                            $bags[$key] = $this->emptyBag() + [
                                'status_label' => 'With Disability',
                                'detail'       => $type,
                            ];
                        }
                        $this->incrementBag($bags[$key], $app->status);
                    }
                } else {
                    // Has disability flagged but no specific type ticked
                    $detail = $level !== '' ? 'Unspecified (Level: ' . $level . ')' : 'Unspecified';
                    $key    = 'With Disability|' . $detail;
                    if (!isset($bags[$key])) {
                        $bags[$key] = $this->emptyBag() + [
                            'status_label' => 'With Disability',
                            'detail'       => $detail,
                        ];
                    }
                    $this->incrementBag($bags[$key], $app->status);
                }
            } elseif ($hasDisability === 'no') {
                $key = 'Without Disability|—';
                if (!isset($bags[$key])) {
                    $bags[$key] = $this->emptyBag() + [
                        'status_label' => 'Without Disability',
                        'detail'       => '—',
                    ];
                }
                $this->incrementBag($bags[$key], $app->status);
            } else {
                $key = 'Not Specified|—';
                if (!isset($bags[$key])) {
                    $bags[$key] = $this->emptyBag() + [
                        'status_label' => 'Not Specified',
                        'detail'       => '—',
                    ];
                }
                $this->incrementBag($bags[$key], $app->status);
            }

            $grand++;
        }

        // Custom sort: With Disability first, Without second, Not Specified last
        $order = ['With Disability' => 0, 'Without Disability' => 1, 'Not Specified' => 2];
        uasort($bags, function ($a, $b) use ($order) {
            $oa = $order[$a['status_label']] ?? 9;
            $ob = $order[$b['status_label']] ?? 9;
            return $oa !== $ob ? $oa <=> $ob : $b['total'] <=> $a['total'];
        });

        $rows = [];
        foreach ($bags as $bag) {
            $pct    = $grand > 0 ? round($bag['total'] / $grand * 100, 1) : 0;
            $rows[] = [
                $bag['status_label'], $bag['detail'], $bag['total'], $pct . '%',
                $bag['submitted'], $bag['under_review'], $bag['approved'], $bag['rejected'],
            ];
        }

        $t = $this->emptyBag();
        foreach ($bags as $b) { foreach (array_keys($t) as $k) $t[$k] += $b[$k]; }
        // Grand total counts each disability type separately for "With Disability" apps,
        // so recalculate from original $grand for the totals footer
        $rows[] = ['TOTAL', '', $grand, '100%',
            $t['submitted'], $t['under_review'], $t['approved'], $t['rejected']];

        return collect($rows);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Breakdown by Refugee Status
    // ─────────────────────────────────────────────────────────────────────────

    private function groupByRefugee(Collection $apps): Collection
    {
        // Categories:
        //   Ugandan Citizen
        //   Refugee (non-Ugandan with a refugee_card_number)
        //   Non-Ugandan (non-refugee)
        //   Not Specified
        $bags  = [];
        $grand = 0;

        foreach ($apps as $app) {
            $p          = $app->personal_info ?? [];
            $isUgandan  = strtolower(trim((string) ($p['is_ugandan'] ?? '')));
            $refugee    = trim((string) ($p['refugee_card_number'] ?? ''));
            $passportNo = trim((string) ($p['passport_number'] ?? ''));
            $foreignId  = trim((string) ($p['foreign_id_number'] ?? ''));

            if ($isUgandan === 'yes') {
                $label = 'Ugandan Citizen';
            } elseif ($isUgandan === 'no') {
                if ($refugee !== '') {
                    $label = 'Refugee';
                } else {
                    $label = 'Non-Ugandan (Non-Refugee)';
                }
            } else {
                $label = 'Not Specified';
            }

            if (!isset($bags[$label])) $bags[$label] = $this->emptyBag();
            $this->incrementBag($bags[$label], $app->status);
            $grand++;
        }

        // Sort: Ugandan first, Refugee, Non-Ugandan, Not Specified
        $order = [
            'Ugandan Citizen'           => 0,
            'Refugee'                   => 1,
            'Non-Ugandan (Non-Refugee)' => 2,
            'Not Specified'             => 3,
        ];
        uksort($bags, fn ($a, $b) => ($order[$a] ?? 9) <=> ($order[$b] ?? 9));

        return $this->bagsToRows($bags, $grand, fn ($key, $bag, $pct) => [
            $key, $bag['total'], $pct . '%',
            $bag['submitted'], $bag['under_review'], $bag['approved'], $bag['rejected'],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Breakdown by Entry / Admission Level
    // ─────────────────────────────────────────────────────────────────────────

    private function groupByEntryLevel(Collection $apps): Collection
    {
        // Determine entry route from which admission field group is filled.
        // An applicant may technically have data in more than one group
        // (partial fills), so we pick the "most complete" one.
        // Priority: A-Level → Diploma → HEAC → Mature Entry → Not Specified
        $bags  = [];
        $grand = 0;

        foreach ($apps as $app) {
            $p = $app->personal_info ?? [];

            $hasAlevel  = !empty(trim((string) ($p['alevel_school_exam'] ?? '')))
                       || !empty(trim((string) ($p['alevel_index']      ?? '')));
            $hasDiploma = !empty(trim((string) ($p['diploma_school'] ?? '')))
                       || !empty(trim((string) ($p['diploma_index']  ?? '')));
            $hasHeac    = !empty(trim((string) ($p['heac_school'] ?? '')))
                       || !empty(trim((string) ($p['heac_index']  ?? '')));
            $hasMature  = !empty(trim((string) ($p['mature_school'] ?? '')))
                       || !empty(trim((string) ($p['mature_index']  ?? '')));

            if ($hasAlevel) {
                $label = "A' Level";
            } elseif ($hasDiploma) {
                $label = 'Diploma';
            } elseif ($hasHeac) {
                $label = 'HEAC';
            } elseif ($hasMature) {
                $label = 'Mature Entry';
            } else {
                $label = 'Not Specified';
            }

            if (!isset($bags[$label])) $bags[$label] = $this->emptyBag();
            $this->incrementBag($bags[$label], $app->status);
            $grand++;
        }

        // Fixed display order
        $order = ["A' Level" => 0, 'Diploma' => 1, 'HEAC' => 2, 'Mature Entry' => 3, 'Not Specified' => 4];
        uksort($bags, fn ($a, $b) => ($order[$a] ?? 9) <=> ($order[$b] ?? 9));

        return $this->bagsToRows($bags, $grand, fn ($key, $bag, $pct) => [
            $key, $bag['total'], $pct . '%',
            $bag['submitted'], $bag['under_review'], $bag['approved'], $bag['rejected'],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // University normalisation
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Map a raw institution string to its canonical display name.
     * Returns null for unrecognised values — callers should skip those.
     */
    private function normaliseUniversity(string $raw): ?string
    {
        if ($raw === '') return null;

        $lower = mb_strtolower($raw);
        foreach (self::KEYWORD_MAP as $keyword => $canonical) {
            if (str_contains($lower, $keyword)) {
                return $canonical;
            }
        }

        // Not on the approved list — exclude from analytics
        return null;
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
