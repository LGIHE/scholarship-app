<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 12px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            color: #1d4ed8;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 9px;
            color: #6b7280;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 9px;
            color: #4b5563;
        }

        .filters-bar {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 9px;
        }

        .filters-bar strong {
            color: #374151;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        thead tr {
            background-color: #1d4ed8;
            color: #ffffff;
        }

        thead th {
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            white-space: nowrap;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tbody td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
            vertical-align: top;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-submitted   { background: #dbeafe; color: #1d4ed8; }
        .badge-approved    { background: #dcfce7; color: #15803d; }
        .badge-rejected    { background: #fee2e2; color: #b91c1c; }
        .badge-under_review { background: #fef9c3; color: #a16207; }
        .badge-draft       { background: #f3f4f6; color: #374151; }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }

        .total-row {
            font-weight: bold;
            background-color: #eff6ff !important;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-size: 12px;
        }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>LIT Scholarship Programme</h1>
    <p>{{ $title }}</p>
</div>

{{-- Meta info --}}
<table style="width:100%; margin-bottom: 16px; font-size: 9px; color: #4b5563;">
    <tr>
        <td>Generated: {{ now()->format('d M Y, H:i') }}</td>
        <td style="text-align:right">Total Records: {{ count($rows) }}</td>
    </tr>
</table>

{{-- Active filters --}}
@if (!empty($filterSummary))
<div class="filters-bar">
    <strong>Filters applied:</strong> {{ $filterSummary }}
</div>
@endif

{{-- Table --}}
@if (count($rows) === 0)
    <div class="no-data">No records found for the selected filters.</div>
@else
<table>
    <thead>
        <tr>
            @foreach ($headings as $heading)
                <th>{{ $heading }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
        <tr>
            @foreach ($row as $i => $cell)
                <td>
                    @php
                        $statusCells = ['Submitted','Approved','Rejected','Under Review','Draft'];
                        $statusSlug  = strtolower(str_replace(' ', '_', (string) $cell));
                    @endphp
                    @if (in_array((string) $cell, $statusCells))
                        <span class="badge badge-{{ $statusSlug }}">{{ $cell }}</span>
                    @else
                        {{ $cell }}
                    @endif
                </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="footer">
    LIT Scholarship Management System &bull; Confidential &bull; {{ now()->format('Y') }}
</div>

</body>
</html>
