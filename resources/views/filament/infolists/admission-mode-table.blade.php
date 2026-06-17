@php
    $pi = $getState() ?? [];

    $rows = [
        ["A' Level",    $pi['alevel_school_exam'] ?? '', $pi['alevel_year'] ?? '',   $pi['alevel_index'] ?? '',   $pi['alevel_points'] ?? ''],
        ['Diploma',     $pi['diploma_school'] ?? '',     $pi['diploma_year'] ?? '',   $pi['diploma_index'] ?? '',  $pi['diploma_cgpa'] ?? ''],
        ['HEAC',        $pi['heac_school'] ?? '',        $pi['heac_year'] ?? '',      $pi['heac_index'] ?? '',     $pi['heac_points'] ?? ''],
        ['Mature Entry',$pi['mature_school'] ?? '',      $pi['mature_year'] ?? '',    $pi['mature_index'] ?? '',   $pi['mature_points'] ?? ''],
    ];

    // Only keep rows where at least one data column is non-empty
    $rows = array_filter($rows, fn($row) => !empty($row[1]) || !empty($row[2]) || !empty($row[3]) || !empty($row[4]));
@endphp

@if (empty($rows))
    <p class="text-sm text-gray-400 italic">No mode of admission information provided.</p>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">Mode</th>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">School / Institution</th>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">Year</th>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">Index / Reg. Number</th>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">Points / CGPA</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($rows as $i => $row)
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="border border-gray-200 px-4 py-2 font-medium text-gray-700 whitespace-nowrap">{{ $row[0] }}</td>
                        <td class="border border-gray-200 px-4 py-2 text-gray-800">{{ $row[1] ?: '—' }}</td>
                        <td class="border border-gray-200 px-4 py-2 text-gray-800">{{ $row[2] ?: '—' }}</td>
                        <td class="border border-gray-200 px-4 py-2 text-gray-800">{{ $row[3] ?: '—' }}</td>
                        <td class="border border-gray-200 px-4 py-2 text-gray-800">{{ $row[4] ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
