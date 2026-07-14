<x-filament-panels::page>

    {{-- ── Info banner ─────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-950 px-5 py-4 text-sm text-amber-800 dark:text-amber-200 mb-6">
        <p class="font-semibold mb-1">What this tool does</p>
        <p>
            Scans the <strong>institution</strong> field across all applications and corrects any value
            that can be matched to the canonical institution list. Values that cannot be matched are
            shown separately for manual review — they are excluded from analytics and reports.
        </p>
    </div>

    {{-- ── Stats row ───────────────────────────────────────────────────── --}}
    @if ($this->scanned)
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 mb-6">
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 text-center shadow-sm">
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $this->totalAffected }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Application(s) to correct</p>
            </div>
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 text-center shadow-sm">
                <p class="text-2xl font-bold {{ $this->totalUnknown > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-gray-800 dark:text-gray-100' }}">
                    {{ $this->totalUnknown }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unrecognised (excluded from reports)</p>
            </div>
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 text-center shadow-sm sm:col-span-1 col-span-2">
                @if ($this->applied)
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Done</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">All corrections applied</p>
                @elseif ($this->totalAffected === 0)
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Clean</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">No corrections needed</p>
                @else
                    <p class="text-2xl font-bold text-amber-500 dark:text-amber-400">Pending</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Awaiting your approval</p>
                @endif
            </div>
        </div>
    @endif

    {{-- ── Action buttons ───────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <x-filament::button
            wire:click="scan"
            color="gray"
            icon="heroicon-o-magnifying-glass"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove wire:target="scan">Re-scan Database</span>
            <span wire:loading wire:target="scan">Scanning…</span>
        </x-filament::button>

        @if ($this->totalAffected > 0 && !$this->applied)
            <x-filament::button
                wire:click="apply"
                color="danger"
                icon="heroicon-o-check-circle"
                wire:loading.attr="disabled"
                wire:confirm="This will update {{ $this->totalAffected }} application(s). Are you sure?"
            >
                <span wire:loading.remove wire:target="apply">Apply Corrections ({{ $this->totalAffected }})</span>
                <span wire:loading wire:target="apply">Applying…</span>
            </x-filament::button>
        @endif
    </div>

    {{-- ── Corrections preview table ───────────────────────────────────── --}}
    @if ($this->scanned)

        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            Corrections to Apply
        </h3>

        @if (count($this->rows) === 0)
            <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-10 text-center mb-6">
                <x-heroicon-o-check-badge class="mx-auto h-10 w-10 text-green-400 dark:text-green-500 mb-2" />
                <p class="font-semibold text-gray-700 dark:text-gray-300">
                    {{ $this->applied ? 'All corrections have been applied.' : 'No matchable corrections found.' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-6">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">App ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Applicant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Current Value</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Will Be Changed To</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                        @foreach ($this->rows as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $row['id'] }}</td>
                                <td class="px-4 py-2.5 text-gray-800 dark:text-gray-200">{{ $row['name'] }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex rounded-md bg-red-50 dark:bg-red-950 px-2 py-0.5 text-xs font-medium text-red-700 dark:text-red-300 ring-1 ring-red-200 dark:ring-red-800">
                                        {{ $row['current'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex rounded-md bg-green-50 dark:bg-green-950 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-300 ring-1 ring-green-200 dark:ring-green-800">
                                        {{ $row['corrected'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- ── Unrecognised institutions ─────────────────────────────────── --}}
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            Unrecognised Institutions
            <span class="ml-1 text-xs font-normal text-gray-400 dark:text-gray-500">(not on the approved list — excluded from analytics &amp; reports)</span>
        </h3>

        @if (count($this->unknownRows) === 0)
            <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-10 text-center">
                <x-heroicon-o-check-badge class="mx-auto h-10 w-10 text-green-400 dark:text-green-500 mb-2" />
                <p class="font-semibold text-gray-700 dark:text-gray-300">No unrecognised institutions found.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-amber-200 dark:border-amber-700 shadow-sm">
                <table class="min-w-full divide-y divide-amber-100 dark:divide-amber-800 text-sm">
                    <thead class="bg-amber-50 dark:bg-amber-950">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-amber-700 dark:text-amber-300 uppercase tracking-wide">App ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-amber-700 dark:text-amber-300 uppercase tracking-wide">Applicant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-amber-700 dark:text-amber-300 uppercase tracking-wide">Institution (as entered)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-50 dark:divide-amber-900 bg-white dark:bg-gray-900">
                        @foreach ($this->unknownRows as $row)
                            <tr class="hover:bg-amber-50 dark:hover:bg-amber-950 transition-colors">
                                <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $row['id'] }}</td>
                                <td class="px-4 py-2.5 text-gray-800 dark:text-gray-200">{{ $row['name'] }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex rounded-md bg-amber-50 dark:bg-amber-950 px-2 py-0.5 text-xs font-medium text-amber-700 dark:text-amber-300 ring-1 ring-amber-200 dark:ring-amber-700">
                                        {{ $row['current'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                These {{ $this->totalUnknown }} entry/entries cannot be automatically matched.
                They will not appear in breakdown reports or the university chart.
                To fix them, update the keyword map in
                <code class="font-mono">NormaliseInstitutions</code> or edit the application directly.
            </p>
        @endif

    @endif

</x-filament-panels::page>
