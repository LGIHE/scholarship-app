<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>General Breakdown Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            padding: 20px;
        }

        /* ── Cover header (first page only) ──────────────────────────────── */
        .cover-header {
            text-align: center;
            margin-bottom: 24px;
            border-bottom: 3px solid #1d4ed8;
            padding-bottom: 14px;
        }

        .cover-header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #1d4ed8;
            margin-bottom: 4px;
        }

        .cover-header p {
            font-size: 9px;
            color: #6b7280;
        }

        /* ── Per-section header ──────────────────────────────────────────── */
        .section-header {
            margin-bottom: 10px;
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 6px;
        }

        .section-header h2 {
            font-size: 13px;
            font-weight: bold;
            color: #1d4ed8;
        }

        .section-header p {
            font-size: 8px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── Filters bar ─────────────────────────────────────────────────── */
        .filters-bar {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 10px;
            font-size: 9px;
            color: #4b5563;
        }

        .filters-bar strong {
            color: #374151;
        }

        /* ── Meta row ────────────────────────────────────────────────────── */
        .meta-row {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9px;
            color: #4b5563;
        }

        /* ── Table ───────────────────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
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

        .total-row td {
            font-weight: bold;
            background-color: #eff6ff !important;
            color: #1d4ed8;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-size: 11px;
        }

        /* ── Footer ──────────────────────────────────────────────────────── */
        .footer {
            margin-top: 14px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }

        /* ── Page break between sections ─────────────────────────────────── */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

{{-- ── Cover / document header ───────────────────────────────────────────── --}}
<div class="cover-header">
    <h1>LIT Scholarship Programme</h1>
    <p>General Breakdown Report</p>
</div>

<table class="meta-row" style="width:100%; margin-bottom: 16px; font-size: 9px; color: #4b5563;">
    <tr>
        <td>Generated: {{ now()->format('d M Y, H:i') }}</td>
        <td style="text-align:right">Sections: {{ count($sections) }}</td>
    </tr>
</table>

@if (!empty($filterSummary))
<div class="filters-bar">
    <strong>Filters applied:</strong> {{ $filterSummary }}
</div>
@endif

{{-- ── One section per breakdown type ────────────────────────────────────── --}}
@foreach ($sections as $index => $section)

    {{-- Force a new page before every section except the first --}}
    @if ($index > 0)
        <div class="page-break"></div>
    @endif

    {{-- Section heading --}}
    <div class="section-header">
        <h2>{{ $section['title'] }}</h2>
        <p>{{ count($section['rows']) }} {{ $section['is_total_row_included'] ? count($section['rows']) - 1 . ' group(s) + totals row' : 'group(s)' }}</p>
    </div>

    {{-- Table --}}
    @if (empty($section['rows']))
        <div class="no-data">No records found for the selected filters.</div>
    @else
        <table>
            <thead>
                <tr>
                    @foreach ($section['headings'] as $heading)
                        <th>{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($section['rows'] as $rowIndex => $row)
                    @php
                        $isTotalsRow = in_array('TOTAL', $row, true);
                    @endphp
                    <tr @if($isTotalsRow) class="total-row" @endif>
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

@endforeach

{{-- ── Document footer ────────────────────────────────────────────────────── --}}
<div class="footer">
    LIT Scholarship Programme &mdash; General Breakdown Report &mdash; {{ now()->format('d M Y') }}
</div>

</body>
</html>
