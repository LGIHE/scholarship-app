<x-filament-panels::page>

    {{-- ── Info banner ─────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-950 px-5 py-4 text-sm text-blue-800 dark:text-blue-200 mb-6">
        <p class="font-semibold mb-1">What this page shows</p>
        <p>
            Every distinct raw value currently stored in the database for <strong>teaching subjects</strong>,
            <strong>academic programmes</strong>, and <strong>gender</strong> — together with whether each
            value matches the approved scholarship criteria.
            Values marked <span class="font-semibold text-red-600 dark:text-red-400">✗ Not approved</span>
            will be <strong>excluded</strong> from all reports and dashboard analytics.
            Use this page to identify data that needs cleaning up directly in the application records.
        </p>
    </div>

    {{-- ── Approved criteria reference ─────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Approved Subjects</p>
            <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-0.5">
                @foreach(\App\Support\ApprovedCriteria::approvedSubjectLabels() as $label)
                    <li class="flex items-center gap-1.5">
                        <x-heroicon-m-check-circle class="h-4 w-4 text-green-500 shrink-0" />
                        {{ $label }}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Approved Course</p>
            <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-0.5">
                @foreach(\App\Support\ApprovedCriteria::approvedCourseLabels() as $label)
                    <li class="flex items-center gap-1.5">
                        <x-heroicon-m-check-circle class="h-4 w-4 text-green-500 shrink-0" />
                        {{ $label }}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Approved Gender</p>
            <p class="text-sm text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                <x-heroicon-m-check-circle class="h-4 w-4 text-green-500 shrink-0" />
                {{ \App\Support\ApprovedCriteria::approvedGenderLabel() }}
            </p>
        </div>
    </div>

    {{-- ── Summary stats ────────────────────────────────────────────────── --}}
    @php $summary = $this->summary; @endphp
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($summary['total']) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total submitted applications</p>
        </div>
        <div class="rounded-xl border border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-950 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ number_format($summary['eligible']) }}</p>
            <p class="text-xs text-green-600 dark:text-green-400 mt-1">Eligible (meet all criteria)</p>
        </div>
        <div class="rounded-xl border border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-950 p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-red-700 dark:text-red-300">{{ number_format($summary['ineligible']) }}</p>
            <p class="text-xs text-red-600 dark:text-red-400 mt-1">Ineligible (excluded from reports)</p>
        </div>
    </div>

    {{-- ── Helper macro ─────────────────────────────────────────────────── --}}
    @php
        // Shared table renderer — avoids repeating HTML three times
        $renderTable = function (string $heading, string $subheading, array $rows, string $emptyMessage) {
            return ['heading' => $heading, 'subheading' => $subheading, 'rows' => $rows, 'empty' => $emptyMessage];
        };

        $sections = [
            $renderTable(
                'Teaching Subjects',
                'Distinct values found in teaching_subjects_1 and teaching_subjects_2 across all submitted applications.',
                $this->subjectRows,
                'No teaching subject values found.'
            ),
            $renderTable(
                'Academic Programmes (Course)',
                'Distinct values found in the academic_programme field.',
                $this->courseRows,
                'No academic programme values found.'
            ),
            $renderTable(
                'Gender (from NIN prefix)',
                'Gender is derived from the first two characters of the NIN. CF = Female, CM = Male.',
                $this->genderRows,
                'No NIN / gender data found.'
            ),
        ];
    @endphp

    @foreach ($sections as $section)
        <div class="mb-8">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-0.5">
                {{ $section['heading'] }}
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $section['subheading'] }}</p>

            @if (empty($section['rows']))
                <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $section['empty'] }}</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide w-8/12">
                                    Raw value (as entered by applicant)
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide w-2/12">
                                    Applications
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide w-2/12">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @foreach ($section['rows'] as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <td class="px-4 py-2.5 text-gray-800 dark:text-gray-200 font-mono text-xs">
                                        {{ $row['value'] }}
                                    </td>
                                    <td class="px-4 py-2.5 text-center text-gray-500 dark:text-gray-400 tabular-nums">
                                        {{ $row['count'] }}
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        @if ($row['approved'])
                                            <span class="inline-flex items-center gap-1 rounded-md bg-green-50 dark:bg-green-950 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-green-200 dark:ring-green-800">
                                                <x-heroicon-m-check-circle class="h-3.5 w-3.5" />
                                                Approved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-md bg-red-50 dark:bg-red-950 px-2 py-0.5 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-red-200 dark:ring-red-800">
                                                <x-heroicon-m-x-circle class="h-3.5 w-3.5" />
                                                Not approved
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @php
                    $approvedCount   = collect($section['rows'])->where('approved', true)->count();
                    $unapprovedCount = collect($section['rows'])->where('approved', false)->count();
                @endphp

                @if ($unapprovedCount > 0)
                    <p class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                        ⚠ {{ $unapprovedCount }} value(s) do not match the approved criteria and will be excluded from reports.
                        Edit the relevant application records to correct these values.
                    </p>
                @else
                    <p class="mt-2 text-xs text-green-600 dark:text-green-400">
                        ✓ All values for this field match the approved criteria.
                    </p>
                @endif
            @endif
        </div>
    @endforeach

</x-filament-panels::page>
