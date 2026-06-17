@php
    $pi = $getState() ?? [];

    $rows = [
        ['Primary School',         $pi['primary_school_name'] ?? '',  $pi['primary_school_district'] ?? '',  $pi['primary_school_dates'] ?? '',  $pi['primary_school_responsible'] ?? ''],
        ["O'Level",                $pi['olevel_school_name'] ?? '',   $pi['olevel_school_district'] ?? '',   $pi['olevel_school_dates'] ?? '',   $pi['olevel_school_responsible'] ?? ''],
        ["A'Level",                $pi['alevel_school_name'] ?? '',   $pi['alevel_school_district'] ?? '',   $pi['alevel_school_dates'] ?? '',   $pi['alevel_school_responsible'] ?? ''],
        ['University/Institution', $pi['university_name'] ?? '',      $pi['university_district'] ?? '',      $pi['university_dates'] ?? '',      $pi['university_responsible'] ?? ''],
    ];

    // Only keep rows where at least one data column is non-empty
    $rows = array_filter($rows, fn($row) => !empty($row[1]) || !empty($row[2]) || !empty($row[3]) || !empty($row[4]));
@endphp

@if (empty($rows))
    <p class="text-sm text-gray-400 italic">No schools attended information provided.</p>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">Level</th>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">Name of School</th>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">District / Country</th>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">Dates of Attendance</th>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">Responsible Person</th>
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
