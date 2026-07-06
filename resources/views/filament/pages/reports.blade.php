<x-filament-panels::page>

    {{-- ── Filter Form ─────────────────────────────────────────────────── --}}
    <form wire:submit.prevent>
        {{ $this->form }}
    </form>

    {{-- ── Export buttons ──────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-3 mt-2">
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

        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
            Select a report type and optional filters, then download.
        </span>
    </div>

    {{-- ── Preview table ────────────────────────────────────────────────── --}}
    @php
        $preview = $this->getPreviewData();
    @endphp

    @if (!empty($preview['headings']))
    <div class="mt-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                @if ($preview['is_breakdown'])
                    Summary Preview
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                        ({{ $preview['total'] }} group{{ $preview['total'] !== 1 ? 's' : '' }})
                    </span>
                @else
                    Data Preview
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                        (first {{ min(10, $preview['total']) }} of {{ $preview['total'] }} records)
                    </span>
                @endif
            </h3>
        </div>

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
                            // Highlight the totals footer row for breakdown reports
                            $isTotalsRow = $preview['is_breakdown']
                                && $rowIndex === array_key_last($preview['rows'])
                                && in_array('TOTAL', $row);
                        @endphp
                        <tr class="{{ $isTotalsRow
                            ? 'bg-blue-50 dark:bg-blue-950 font-semibold'
                            : 'hover:bg-gray-50 dark:hover:bg-gray-800' }} transition-colors">
                            @foreach ($row as $cell)
                                <td class="px-3 py-2 {{ $isTotalsRow ? 'text-blue-800 dark:text-blue-200' : 'text-gray-700 dark:text-gray-300' }} whitespace-nowrap">
                                    @php
                                        $statusMap = [
                                            'Submitted'    => 'primary',
                                            'Under Review' => 'warning',
                                            'Approved'     => 'success',
                                            'Rejected'     => 'danger',
                                            'Draft'        => 'gray',
                                        ];
                                        $badgeColor = $statusMap[(string) $cell] ?? null;
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
                            <td colspan="{{ count($preview['headings']) }}" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                No records found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (!$preview['is_breakdown'] && $preview['total'] > 10)
            <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                Showing 10 of {{ $preview['total'] }} records. Export to see the full dataset.
            </p>
        @endif
    </div>
    @else
        <div class="mt-8 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-10 text-center">
            <x-heroicon-o-chart-bar class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" />
            <p class="text-gray-500 dark:text-gray-400 font-medium">Select a report type above to preview data</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                Apply filters then export to Excel or PDF.<br>
                <span class="text-xs">
                    <strong>Breakdown reports</strong> show aggregated counts grouped by region, district, university, country, or nationality.
                </span>
            </p>
        </div>
    @endif

</x-filament-panels::page>
