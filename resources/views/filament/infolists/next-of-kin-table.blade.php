@php
    $nokList = collect($getState() ?? [])
        ->filter(fn ($row) => !empty($row['name']) || !empty($row['relationship']) || !empty($row['telephone']));
@endphp

@if ($nokList->isEmpty())
    <p class="text-sm text-gray-400 italic">No next of kin information provided.</p>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">#</th>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">Relationship</th>
                    <th class="border border-gray-200 px-4 py-2 text-left font-semibold text-gray-600">Telephone</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($nokList as $i => $row)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="border border-gray-200 px-4 py-2 text-gray-500">{{ $loop->iteration }}</td>
                        <td class="border border-gray-200 px-4 py-2 text-gray-800">{{ $row['name'] ?? '—' }}</td>
                        <td class="border border-gray-200 px-4 py-2 text-gray-800">{{ $row['relationship'] ?? '—' }}</td>
                        <td class="border border-gray-200 px-4 py-2 text-gray-800">{{ $row['telephone'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
