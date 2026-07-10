<x-filament-panels::page>

    {{-- ── Stats Row ───────────────────────────────────────────────────── --}}
    @php $stats = $this->getLogStats(); @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        @foreach ([
            ['label' => 'Total Events',    'value' => $stats['total'],        'icon' => 'heroicon-o-clipboard-document-list', 'color' => 'text-gray-600 dark:text-gray-300',    'bg' => 'bg-gray-50 dark:bg-gray-800'],
            ['label' => 'Emails Sent',     'value' => $stats['emails'],       'icon' => 'heroicon-o-envelope',               'color' => 'text-blue-600 dark:text-blue-400',    'bg' => 'bg-blue-50 dark:bg-blue-900/30'],
            ['label' => 'Applications',    'value' => $stats['applications'], 'icon' => 'heroicon-o-document-text',          'color' => 'text-amber-600 dark:text-amber-400',  'bg' => 'bg-amber-50 dark:bg-amber-900/30'],
            ['label' => 'Auth / Signups',  'value' => $stats['auth'],         'icon' => 'heroicon-o-user-plus',              'color' => 'text-green-600 dark:text-green-400',  'bg' => 'bg-green-50 dark:bg-green-900/30'],
            ['label' => 'Scholar Events',  'value' => $stats['scholars'],     'icon' => 'heroicon-o-academic-cap',           'color' => 'text-purple-600 dark:text-purple-400','bg' => 'bg-purple-50 dark:bg-purple-900/30'],
        ] as $stat)
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 {{ $stat['bg'] }} px-5 py-4 flex items-center gap-4">
            <div class="{{ $stat['color'] }}">
                <x-dynamic-component :component="$stat['icon']" class="h-8 w-8" />
            </div>
            <div>
                <p class="text-2xl font-bold {{ $stat['color'] }}">{{ number_format($stat['value']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $stat['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Tabs ─────────────────────────────────────────────────────────── --}}
    @php $activeTab = $this->data['tab'] ?? 'activity'; @endphp
    <div class="flex gap-1 border-b border-gray-200 dark:border-gray-700 mb-5">
        <button
            wire:click="setTab('activity')"
            class="px-4 py-2 text-sm font-medium rounded-t-lg transition
                   {{ $activeTab === 'activity'
                       ? 'bg-white dark:bg-gray-900 border border-b-white dark:border-gray-700 dark:border-b-gray-900 text-primary-600 dark:text-primary-400'
                       : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}"
        >
            <span class="flex items-center gap-1.5">
                <x-heroicon-o-list-bullet class="h-4 w-4" />
                Activity Log
            </span>
        </button>
        <button
            wire:click="setTab('error')"
            class="px-4 py-2 text-sm font-medium rounded-t-lg transition
                   {{ $activeTab === 'error'
                       ? 'bg-white dark:bg-gray-900 border border-b-white dark:border-gray-700 dark:border-b-gray-900 text-danger-600 dark:text-danger-400'
                       : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}"
        >
            <span class="flex items-center gap-1.5">
                <x-heroicon-o-exclamation-triangle class="h-4 w-4" />
                Error Logs
            </span>
        </button>
    </div>

    {{-- ── Filters — re-keyed per tab so Livewire swaps the form correctly ── --}}
    <form wire:submit.prevent class="mb-5" wire:key="filter-form-{{ $activeTab }}">
        {{ $this->form }}
    </form>

    {{-- ── Activity Log Tab ─────────────────────────────────────────────── --}}
    @if ($activeTab === 'activity')
        @php $logs = $this->getActivityLogs(); @endphp

        <div class="relative">
            <div
                wire:loading
                wire:target="updatedData,data.log_name,data.event,data.search,data.date_from,data.date_to,setTab"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/70 dark:bg-gray-900/70 backdrop-blur-sm"
            >
                <div class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <svg class="h-5 w-5 animate-spin text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Loading…
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900">

                {{-- Result count + active filters summary --}}
                <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                    <span><span class="font-semibold text-gray-700 dark:text-gray-200">{{ number_format($logs->total()) }}</span> entries found</span>
                    @if ($this->data['search'] ?? null)
                        <span>· Search: <em class="text-gray-700 dark:text-gray-200">{{ $this->data['search'] }}</em></span>
                    @endif
                    @if ($this->data['log_name'] ?? null)
                        <span>· Channel: <em class="text-gray-700 dark:text-gray-200">{{ ucfirst($this->data['log_name']) }}</em></span>
                    @endif
                    @if ($this->data['event'] ?? null)
                        <span>· Event: <em class="text-gray-700 dark:text-gray-200">{{ ucfirst($this->data['event']) }}</em></span>
                    @endif
                </div>

                @if ($logs->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                        <x-heroicon-o-clipboard-document-list class="h-12 w-12 mb-3 opacity-40" />
                        <p class="text-sm">No log entries found for the selected filters.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                    <th class="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Timestamp</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Channel</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Event</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Description</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Caused By</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Subject</th>
                                    <th class="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Properties</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach ($logs as $log)
                                @php
                                    $channelColors = [
                                        'email'       => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                        'application' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                        'auth'        => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                                        'scholar'     => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
                                    ];
                                    $eventColors = [
                                        'created' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        'updated' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
                                        'deleted' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                    ];
                                    $channelClass = $channelColors[$log->log_name] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
                                    $eventClass   = $eventColors[$log->event]     ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
                                    $props        = $log->properties ?? collect();
                                    $displayProps = $props->except(['attributes', 'old'])->toArray();
                                    $changedAttrs = $props->get('attributes', []);
                                    $oldAttrs     = $props->get('old', []);

                                    // Highlight search term helper
                                    $hl = function (string $text) use ($log): string {
                                        $s = trim(request()->input('search', ''));
                                        if ($s === '') return e($text);
                                        return preg_replace(
                                            '/(' . preg_quote(e($s), '/') . ')/iu',
                                            '<mark class="bg-yellow-200 dark:bg-yellow-700 rounded px-0.5">$1</mark>',
                                            e($text)
                                        );
                                    };
                                    $search = $this->data['search'] ?? '';
                                    $hlFn = fn(string $t): string => $search !== ''
                                        ? preg_replace(
                                            '/(' . preg_quote($search, '/') . ')/iu',
                                            '<mark class="bg-yellow-200 dark:bg-yellow-700 rounded px-0.5">$1</mark>',
                                            e($t))
                                        : e($t);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400 text-xs">
                                        {{ $log->created_at->format('d M Y') }}<br>
                                        <span class="text-gray-400 dark:text-gray-500">{{ $log->created_at->format('H:i:s') }}</span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $channelClass }}">
                                            {{ ucfirst($log->log_name ?? '—') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($log->event)
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $eventClass }}">
                                                {{ ucfirst($log->event) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                                        {!! $hlFn($log->description) !!}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600 dark:text-gray-300">
                                        @if ($log->causer)
                                            <span class="font-medium">{!! $hlFn($log->causer->name) !!}</span><br>
                                            <span class="text-gray-400 dark:text-gray-500">{!! $hlFn($log->causer->email) !!}</span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">System</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600 dark:text-gray-300">
                                        @if ($log->subject)
                                            {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 max-w-xs">
                                        @if (!empty($changedAttrs))
                                            @foreach ($changedAttrs as $key => $newVal)
                                                <div class="flex items-center gap-1 flex-wrap">
                                                    <span class="font-medium text-gray-600 dark:text-gray-300">{{ $key }}:</span>
                                                    @if (isset($oldAttrs[$key]))
                                                        <span class="line-through text-red-400">{{ is_array($oldAttrs[$key]) ? json_encode($oldAttrs[$key]) : $oldAttrs[$key] }}</span>
                                                        <span class="text-gray-400">→</span>
                                                    @endif
                                                    <span class="text-green-600 dark:text-green-400">{{ is_array($newVal) ? json_encode($newVal) : $newVal }}</span>
                                                </div>
                                            @endforeach
                                        @endif
                                        @foreach ($displayProps as $key => $val)
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-300">{{ $key }}:</span>
                                                {!! $hlFn(is_array($val) ? implode(', ', $val) : (string) $val) !!}
                                            </div>
                                        @endforeach
                                        @if (empty($changedAttrs) && empty($displayProps))
                                            <span class="text-gray-300 dark:text-gray-600">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ── Error Log Tab ────────────────────────────────────────────────── --}}
    @if ($activeTab === 'error')
        @php
            $errors      = $this->getErrorLogs();
            $errorLimit  = (int) ($this->data['error_limit'] ?? 50);
            $errorSearch = trim($this->data['search'] ?? '');

            $errorHlFn = fn(string $t): string => $errorSearch !== ''
                ? preg_replace(
                    '/(' . preg_quote($errorSearch, '/') . ')/iu',
                    '<mark class="bg-yellow-200 dark:bg-yellow-700 rounded px-0.5">$1</mark>',
                    e($t))
                : e($t);
        @endphp

        <div class="relative">
            <div
                wire:loading
                wire:target="updatedData,data.search,data.date_from,data.date_to,data.error_limit,setTab"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/70 dark:bg-gray-900/70 backdrop-blur-sm"
            >
                <div class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                    <svg class="h-5 w-5 animate-spin text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Loading…
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900">
                @if (empty($errors))
                    <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                        <x-heroicon-o-check-circle class="h-12 w-12 mb-3 opacity-40 text-green-400" />
                        <p class="text-sm">No error log entries found.</p>
                    </div>
                @else
                    {{-- Header bar with count + source info --}}
                    <div class="px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                        <span>
                            Showing <span class="font-semibold text-gray-700 dark:text-gray-200">{{ count($errors) }}</span>
                            most recent entries (limit: {{ $errorLimit }})
                        </span>
                        @if ($errorSearch !== '')
                            <span>· Search: <em class="text-gray-700 dark:text-gray-200">{{ $errorSearch }}</em></span>
                        @endif
                        <span class="ml-auto font-mono">storage/logs/laravel.log</span>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($errors as $entry)
                        @php
                            $levelColors = [
                                'error'     => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                'critical'  => 'bg-red-200 text-red-800 dark:bg-red-900/60 dark:text-red-200',
                                'alert'     => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
                                'emergency' => 'bg-red-300 text-red-900 dark:bg-red-900/80 dark:text-red-100',
                                'warning'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                'notice'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                'info'      => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
                                'debug'     => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                            ];
                            $levelClass = $levelColors[$entry['level']] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                             x-data="{ expanded: false }">
                            <div class="flex items-start gap-3 flex-wrap">
                                <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap mt-0.5 w-36 shrink-0">
                                    {{ \Carbon\Carbon::parse($entry['timestamp'])->format('d M Y H:i:s') }}
                                </span>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $levelClass }} shrink-0">
                                    {{ strtoupper($entry['level']) }}
                                </span>
                                <p class="text-sm text-gray-800 dark:text-gray-200 flex-1 min-w-0 break-words">
                                    {!! $errorHlFn($entry['message']) !!}
                                </p>
                                @if (!empty(trim($entry['context'])))
                                    <button
                                        @click="expanded = !expanded"
                                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline shrink-0"
                                    >
                                        <span x-show="!expanded">Show trace</span>
                                        <span x-show="expanded">Hide trace</span>
                                    </button>
                                @endif
                            </div>
                            @if (!empty(trim($entry['context'])))
                                <div x-show="expanded" x-collapse class="mt-2">
                                    <pre class="text-xs bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3 overflow-x-auto whitespace-pre-wrap break-words text-gray-600 dark:text-gray-300">{{ trim($entry['context']) }}</pre>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

</x-filament-panels::page>
