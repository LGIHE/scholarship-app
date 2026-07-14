<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Combines all 9 breakdown report types into a single Excel workbook,
 * one sheet per breakdown, in the following order:
 *
 *   1. By Country
 *   2. By Region
 *   3. By Subregion
 *   4. By District
 *   5. By University / Institution
 *   6. By Nationality
 *   7. By Disability
 *   8. By Refugee Status
 *   9. By Entry Level
 */
class GeneralBreakdownExport implements WithMultipleSheets
{
    protected array $filters;

    /** Ordered list of breakdown types to include (mirrors the PDF section order). */
    private const BREAKDOWN_ORDER = [
        'breakdown_by_country',
        'breakdown_by_region',
        'breakdown_by_subregion',
        'breakdown_by_district',
        'breakdown_by_university',
        'breakdown_by_nationality',
        'breakdown_by_disability',
        'breakdown_by_refugee',
        'breakdown_by_entry_level',
    ];

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Returns one BreakdownReportExport sheet per breakdown type.
     *
     * @return BreakdownReportExport[]
     */
    public function sheets(): array
    {
        return array_map(
            fn (string $type) => new BreakdownReportExport($type, $this->filters),
            self::BREAKDOWN_ORDER
        );
    }
}
