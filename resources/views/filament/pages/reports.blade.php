<x-filament-panels::page>

    {{-- ── Filter Form ─────────────────────────────────────────────────── --}}
    {{-- All fields are ->live(), so Livewire re-renders automatically on change --}}
    <form wire:submit.prevent>
        {{ $this->form }}
    </form>

    {{-- ── Export buttons ──────────────────────────────────────────────── --}}
    @php $reportType = $this->data['report_type'] ?? null; @endphp

    <div class="flex flex-wrap items-center gap-3 mt-4">

        @if ($reportType === 'general_breakdown_pdf')
            {{-- Combined PDF: single dedicated button --}}
            <x-filament::button
                wire:click="exportGeneralBreakdownPdf"
                color="danger"
                icon="heroicon-o-document-arrow-down"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="exportGeneralBreakdownPdf">Download General Breakdown (PDF)</span>
                <span wire:loading wire:target="exportGeneralBreakdownPdf">Generating PDF…</span>
            </x-filament::button>

        @elseif ($reportType === 'general_breakdown_excel')
            {{-- Combined Excel: single dedicated button --}}
            <x-filament::button
                wire:click="exportGeneralBreakdownExcel"
                color="success"
                icon="heroicon-o-table-cells"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="exportGeneralBreakdownExcel">Download General Breakdown (Excel)</span>
                <span wire:loading wire:target="exportGeneralBreakdownExcel">Generating Excel…</span>
            </x-filament::button>

        @elseif ($reportType === 'applicant_details')
            {{-- Applicant Details: Excel only — full field export via ApplicationsExport --}}
            <x-filament::button
                wire:click="exportApplicantDetailsExcel"
                color="success"
                icon="heroicon-o-table-cells"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="exportApplicantDetailsExcel">Download Applicant Details (Excel)</span>
                <span wire:loading wire:target="exportApplicantDetailsExcel">Generating Excel…</span>
            </x-filament::button>

        @elseif ($reportType === 'participant_profile')
            {{-- Participant Profile: ZIP with PDFs and documents --}}
            <x-filament::button
                wire:click="exportParticipantProfile"
                color="warning"
                icon="heroicon-o-archive-box-arrow-down"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="exportParticipantProfile">Download Participant Profiles (ZIP)</span>
                <span wire:loading wire:target="exportParticipantProfile" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Generating ZIP... Please wait (may take several minutes)
                </span>
            </x-filament::button>

        @elseif ($reportType)
            {{-- Standard per-type export buttons --}}
            <x-filament::button
                wire:click="exportExcel"
                color="success"
                icon="heroicon-o-table-cells"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="exportExcel">Export to Excel</span>
                <span wire:loading wire:target="exportExcel">Generating…</span>
            </x-filament::button>

            <x-filament::button
                wire:click="exportPdf"
                color="danger"
                icon="heroicon-o-document-arrow-down"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="exportPdf">Export to PDF</span>
                <span wire:loading wire:target="exportPdf">Generating…</span>
            </x-filament::button>

            @if ($this->isSplitMode())
            <x-filament::button
                wire:click="exportZip"
                color="warning"
                icon="heroicon-o-archive-box-arrow-down"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="exportZip">Download Split Reports (ZIP)</span>
                <span wire:loading wire:target="exportZip">Generating ZIP…</span>
            </x-filament::button>
            @endif
        @endif

    </div>

    {{-- ── Live preview ────────────────────────────────────────────────── --}}
    @php $preview = $this->getPreviewData(); @endphp

    {{-- Wrap the whole preview in a relative container so the loading
         overlay is scoped only to this section, not the whole page. --}}
    <div class="relative mt-6">

        {{-- Loading overlay — visible while Livewire is processing a field change --}}
        <div
            wire:loading
            wire:target="updatedData,data.report_type,data.status,data.gender,data.nationality,data.date_from,data.date_to,data.university_filter,data.district_filter,data.gender_filter,data.split_by_group,data.zip_format,exportParticipantProfile"
            class="absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/70 dark:bg-gray-900/70 backdrop-blur-sm"
        >
            <div class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                <svg class="h-5 w-5 animate-spin text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Updating results…
            </div>
        </div>

        @if (!empty($preview['is_general_breakdown']))

            {{-- Info panel for combined breakdown types — no single preview table --}}
            <div class="rounded-xl border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-950 p-8 text-center">
                @if ($preview['format'] === 'PDF')
                    <x-heroicon-o-document-arrow-down class="mx-auto h-12 w-12 text-blue-400 dark:text-blue-500 mb-3" />
                @else
                    <x-heroicon-o-table-cells class="mx-auto h-12 w-12 text-blue-400 dark:text-blue-500 mb-3" />
                @endif
                <p class="text-blue-800 dark:text-blue-200 font-semibold text-base">
                    General Breakdown Report &mdash; {{ $preview['format'] }}
                </p>
                <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                    Generates a single {{ $preview['format'] === 'PDF' ? 'PDF with 9 pages' : 'Excel workbook with 9 sheets' }},
                    one per breakdown type.
                </p>
                <ul class="mt-4 inline-block text-left text-sm text-blue-700 dark:text-blue-300 space-y-1 list-disc list-inside">
                    <li>Breakdown by Country</li>
                    <li>Breakdown by Region</li>
                    <li>Breakdown by Subregion</li>
                    <li>Breakdown by District</li>
                    <li>Breakdown by University / Institution</li>
                    <li>Breakdown by Nationality</li>
                    <li>Breakdown by Disability</li>
                    <li>Breakdown by Refugee Status</li>
                    <li>Breakdown by Entry Level</li>
                </ul>
                <p class="mt-4 text-xs text-blue-500 dark:text-blue-500">
                    Apply filters above to scope all sections, then click the download button.
                </p>
            </div>

        @elseif (!empty($preview['headings']))

            {{-- Header bar --}}
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                        {{ $this->reportTitle() }}
                    </h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        @if ($preview['is_breakdown'])
                            {{ $preview['total'] }} group{{ $preview['total'] !== 1 ? 's' : '' }}
                        @else
                            Showing {{ min(15, $preview['total']) }} of {{ $preview['total'] }} records
                            &nbsp;&bull;&nbsp; Export for full dataset
                        @endif

                        @php
                            $summary = $this->filterSummary();
                        @endphp
                        @if ($summary !== 'None (all submitted applications)')
                            &nbsp;&bull;&nbsp; Filters: {{ $summary }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            @foreach ($preview['headings'] as $heading)
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide whitespace-nowrap">
                                    {{ $heading }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                        @forelse ($preview['rows'] as $rowIndex => $row)
                            @php
                                $isTotalsRow = $preview['is_breakdown']
                                    && $rowIndex === array_key_last($preview['rows'])
                                    && in_array('TOTAL', $row);
                            @endphp
                            <tr class="{{ $isTotalsRow
                                ? 'bg-blue-50 dark:bg-blue-950 font-semibold'
                                : 'hover:bg-gray-50 dark:hover:bg-gray-800' }} transition-colors">
                                @foreach ($row as $cell)
                                    <td class="px-3 py-2 {{ $isTotalsRow
                                        ? 'text-blue-800 dark:text-blue-200'
                                        : 'text-gray-700 dark:text-gray-300' }} whitespace-nowrap">
                                        @php
                                            $badgeColor = match((string) $cell) {
                                                'Submitted'    => 'primary',
                                                'Under Review' => 'warning',
                                                'Approved'     => 'success',
                                                'Rejected'     => 'danger',
                                                'Draft'        => 'gray',
                                                default        => null,
                                            };
                                        @endphp
                                        @if ($badgeColor)
                                            <x-filament::badge :color="$badgeColor">{{ $cell }}</x-filament::badge>
                                        @else
                                            {{ $cell }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($preview['headings']) }}"
                                    class="px-4 py-10 text-center text-gray-400 dark:text-gray-500">
                                    No records found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @else

            {{-- Empty state --}}
            <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-12 text-center">
                <x-heroicon-o-chart-bar class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" />
                <p class="text-gray-500 dark:text-gray-400 font-medium">
                    Select a report type to see results
                </p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                    Results update live as you change filters. Export to Excel or PDF when ready.
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                    <strong>General Breakdown</strong> reports generate a single PDF (9 pages) or Excel (9 sheets)
                    covering country, region, subregion, district, university, nationality, disability, refugee status, and entry level.
                </p>
            </div>

        @endif
    </div>

</x-filament-panels::page>
