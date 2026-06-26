<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ApplicationsByScienceArtsChart extends ChartWidget
{
    protected static ?string $heading = 'Sciences vs Arts';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_charts');
    }

    /**
     * Known science subjects (normalised to lowercase, no extra spaces).
     * Any subject not in this list is treated as Arts/Humanities.
     */
    private const SCIENCE_SUBJECTS = [
        'mathematics', 'math', 'maths',
        'physics',
        'chemistry',
        'biology',
        'agriculture', 'agricultural science',
        'computer science', 'computer studies', 'ict', 'information technology',
        'technical drawing', 'technical education',
        'general science',
        'geography', // often classified as science in Ugandan curriculum
        'nutrition', 'food and nutrition',
        'physical education',
    ];

    protected function getData(): array
    {
        $counts = ['Sciences' => 0, 'Arts' => 0];

        // Each application can have two teaching subjects; each one casts a vote.
        $rows = Application::query()
            ->whereNotNull(DB::raw("json_extract(personal_info, '$.teaching_subjects_1')"))
            ->select(
                DB::raw("json_extract(personal_info, '$.teaching_subjects_1') as sub1"),
                DB::raw("json_extract(personal_info, '$.teaching_subjects_2') as sub2")
            )
            ->get();

        foreach ($rows as $row) {
            foreach (['sub1', 'sub2'] as $col) {
                $raw = trim((string) ($row->$col ?? ''));
                if ($raw === '') continue;

                $normalised = strtolower(preg_replace('/\s+/', ' ', $raw));
                $category   = in_array($normalised, self::SCIENCE_SUBJECTS, true)
                    ? 'Sciences'
                    : 'Arts';

                $counts[$category]++;
            }
        }

        $counts = array_filter($counts, fn ($v) => $v > 0);

        return [
            'datasets' => [
                [
                    'label'           => 'Subject Selections',
                    'data'            => array_values($counts),
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',  // blue  – Sciences
                        'rgb(249, 115, 22)',  // orange – Arts
                    ],
                ],
            ],
            'labels' => array_keys($counts),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
