<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Support\ApprovedCriteria;
use Filament\Widgets\ChartWidget;

class ApplicationsByUniversityChart extends ChartWidget
{
    protected static ?string $heading = 'Applications by University';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_charts');
    }

    /**
     * Canonical institution list — must stay in sync with
     * resources/js/data/institutions.js and NormaliseInstitutions command.
     */
    private const CANONICAL = [
        'Makerere University (All Campuses)',
        'Kyambogo University (All Campuses)',
        'Busitema University (All Campuses)',
        'Islamic University in Uganda (All Campuses)',
        'Gulu University (All Campuses)',
        'Muni University (All Campuses)',
        'Mountains of the Moon University',
        'Mbarara University of Science and Technology (All Campuses)',
        'Uganda Martyrs University (All Campuses)',
        'Kabale University (All Campuses)',
        'UNITE Kabale Campus',
        'UNITE Kaliro Campus',
        'UNITE Mubende Campus',
        'UNITE Muni Campus',
        'UNITE Unyama Campus',
    ];

    /**
     * Keyword → canonical mapping (same logic as NormaliseInstitutions).
     * Order matters: more-specific patterns must come first.
     */
    private const KEYWORD_MAP = [
        'makerere'                      => 'Makerere University (All Campuses)',
        'makarere'                      => 'Makerere University (All Campuses)',
        'mubs'                          => 'Makerere University (All Campuses)',
        'kyambogo'                      => 'Kyambogo University (All Campuses)',
        'kyam'                          => 'Kyambogo University (All Campuses)',
        'busitema'                      => 'Busitema University (All Campuses)',
        // Islamic University — kabojja/females campus variants before generic patterns
        'kabojja'                       => 'Islamic University in Uganda (All Campuses)',
        'females campus'                => 'Islamic University in Uganda (All Campuses)',
        'islamic university in uganda'  => 'Islamic University in Uganda (All Campuses)',
        'islamic university'            => 'Islamic University in Uganda (All Campuses)',
        'iuiu'                          => 'Islamic University in Uganda (All Campuses)',
        // Gulu University — bare "gulu" as well as full name
        'gulu university'               => 'Gulu University (All Campuses)',
        'gulu'                          => 'Gulu University (All Campuses)',
        // Mountains of the Moon — handle missing 's' and mixed-case variants
        'mountains of the moon'         => 'Mountains of the Moon University',
        'mountain of the moon'          => 'Mountains of the Moon University',
        'mmu'                           => 'Mountains of the Moon University',
        // Mbarara — bare "mbarara" catches "Mbarara school of science..." etc.
        'mbarara university of science' => 'Mbarara University of Science and Technology (All Campuses)',
        'mbarara university'            => 'Mbarara University of Science and Technology (All Campuses)',
        'mbarara school of science'     => 'Mbarara University of Science and Technology (All Campuses)',
        'mbarara'                       => 'Mbarara University of Science and Technology (All Campuses)',
        'must'                          => 'Mbarara University of Science and Technology (All Campuses)',
        // Uganda Martyrs — "marty's" / "martys" typo variants
        'uganda martyrs'                => 'Uganda Martyrs University (All Campuses)',
        'uganda marty'                  => 'Uganda Martyrs University (All Campuses)',
        'umu'                           => 'Uganda Martyrs University (All Campuses)',
        // Kabale University — before bare "kabale" and "kabaale" typo
        'kabale university'             => 'Kabale University (All Campuses)',
        'kabaale university'            => 'Kabale University (All Campuses)',
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
        'muni university'               => 'Muni University (All Campuses)',
        'muni'                          => 'Muni University (All Campuses)',
    ];

    /**
     * Normalise a raw institution string to a canonical name,
     * or return null when it cannot be matched (→ "Others").
     */
    private function normalise(string $raw): ?string
    {
        $raw = trim($raw);

        if ($raw === '') {
            return null;
        }

        // Already canonical
        if (in_array($raw, self::CANONICAL, true)) {
            return $raw;
        }

        $lower = mb_strtolower($raw);

        foreach (self::KEYWORD_MAP as $keyword => $canonical) {
            if (str_contains($lower, $keyword)) {
                return $canonical;
            }
        }

        return null; // unrecognised → Others
    }

    protected function getData(): array
    {
        // Initialise counts: every canonical institution starts at 0
        $counts = array_fill_keys(self::CANONICAL, 0);
        $others = 0;

        Application::query()
            ->whereNotNull('personal_info')
            ->whereNotIn('status', ['draft'])
            ->get(['personal_info'])
            ->each(function ($app) use (&$counts, &$others) {
                $info = $app->personal_info ?? [];

                // Skip applications that do not meet all eligibility criteria
                if (!ApprovedCriteria::isEligible($info)) {
                    return;
                }

                $raw       = (string) ($info['institution'] ?? '');
                $canonical = $this->normalise($raw);

                if ($canonical !== null) {
                    $counts[$canonical]++;
                } else {
                    $others++;
                }
            });

        // Remove canonical entries with 0 applications to keep the chart clean,
        // but always append Others if > 0
        $filtered = array_filter($counts, fn ($v) => $v > 0);

        if ($others > 0) {
            $filtered['Others'] = $others;
        }

        if (empty($filtered)) {
            return [
                'datasets' => [['label' => 'Applications', 'data' => [0], 'backgroundColor' => ['rgb(156, 163, 175)']]],
                'labels'   => ['No data'],
            ];
        }

        $palette = [
            'rgb(59, 130, 246)',   // blue
            'rgb(34, 197, 94)',    // green
            'rgb(251, 191, 36)',   // amber
            'rgb(239, 68, 68)',    // red
            'rgb(168, 85, 247)',   // purple
            'rgb(249, 115, 22)',   // orange
            'rgb(20, 184, 166)',   // teal
            'rgb(236, 72, 153)',   // pink
            'rgb(99, 102, 241)',   // indigo
            'rgb(16, 185, 129)',   // emerald
            'rgb(245, 158, 11)',   // yellow
            'rgb(14, 165, 233)',   // sky
            'rgb(139, 92, 246)',   // violet
            'rgb(244, 63, 94)',    // rose
            'rgb(107, 114, 128)',  // gray (for UNITE Unyama, last canonical)
            'rgb(156, 163, 175)',  // light gray — reserved for "Others"
        ];

        $labels  = array_keys($filtered);
        $data    = array_values($filtered);
        $colours = array_map(fn ($i) => $palette[$i % count($palette)], array_keys($data));

        return [
            'datasets' => [
                [
                    'label'           => 'Applications',
                    'data'            => $data,
                    'backgroundColor' => $colours,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
