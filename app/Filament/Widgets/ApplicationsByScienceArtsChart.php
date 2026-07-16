<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Support\ApprovedCriteria;
use Filament\Widgets\ChartWidget;

class ApplicationsByScienceArtsChart extends ChartWidget
{
    // This chart is now "Applications by Approved Subject" since only approved
    // science subjects are eligible for this scholarship.
    protected static ?string $heading = 'Applications by Approved Subject';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_charts');
    }

    protected function getData(): array
    {
        $counts = [];

        Application::query()
            ->whereNotNull('personal_info')
            ->whereNotIn('status', ['draft'])
            ->get(['personal_info'])
            ->each(function ($app) use (&$counts) {
                $info = $app->personal_info ?? [];

                // Skip applications that do not meet all eligibility criteria
                if (!ApprovedCriteria::isEligible($info)) {
                    return;
                }

                // Count each approved teaching subject individually
                foreach (['teaching_subjects_1', 'teaching_subjects_2'] as $field) {
                    $raw = trim((string) ($info[$field] ?? ''));
                    if ($raw === '') {
                        continue;
                    }

                    // Only count if this subject is an approved one
                    if (!ApprovedCriteria::subjectMatches($raw)) {
                        continue;
                    }

                    // Normalise to a display label
                    $label = $this->normaliseSubjectLabel($raw);
                    $counts[$label] = ($counts[$label] ?? 0) + 1;
                }
            });

        arsort($counts);

        if (empty($counts)) {
            return [
                'datasets' => [['label' => 'Subject Selections', 'data' => [0], 'backgroundColor' => ['rgb(156, 163, 175)']]],
                'labels'   => ['No eligible data'],
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
            'rgb(156, 163, 175)',  // gray
        ];

        $labels  = array_keys($counts);
        $data    = array_values($counts);
        $colours = array_map(fn ($i) => $palette[$i % count($palette)], array_keys($data));

        return [
            'datasets' => [
                [
                    'label'           => 'Subject Selections',
                    'data'            => $data,
                    'backgroundColor' => $colours,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Map a raw subject string to a clean display label using the approved keywords.
     */
    private function normaliseSubjectLabel(string $raw): string
    {
        $lower = strtolower(trim($raw));

        if (str_contains($lower, 'biology'))                                        return 'Biology';
        if (str_contains($lower, 'chemistry'))                                      return 'Chemistry';
        if (str_contains($lower, 'physics'))                                        return 'Physics';
        if (str_contains($lower, 'math'))                                           return 'Mathematics';
        if (str_contains($lower, 'agriculture'))                                    return 'Agriculture';
        if (str_contains($lower, 'computer studies'))                               return 'Computer Studies';
        if (str_contains($lower, 'computer science'))                               return 'Computer Studies';
        if (str_contains($lower, 'ict') || str_contains($lower, 'information'))    return 'ICT';

        // Fallback — should not reach here since subjectMatches() already passed
        return ucwords($raw);
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
