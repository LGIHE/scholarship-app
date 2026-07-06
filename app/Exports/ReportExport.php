<?php

namespace App\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected string $reportType;
    protected array  $filters;

    public function __construct(string $reportType, array $filters = [])
    {
        $this->reportType = $reportType;
        $this->filters    = $filters;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Sheet title
    // ─────────────────────────────────────────────────────────────────────────

    public function title(): string
    {
        return match ($this->reportType) {
            'applications_summary'  => 'Applications Summary',
            'applicant_details'     => 'Applicant Details',
            'scoring_report'        => 'Scoring Report',
            'district_report'       => 'District Report',
            'university_report'     => 'University Report',
            'gender_report'         => 'Gender Report',
            'financial_report'      => 'Financial Report',
            'approved_scholars'     => 'Approved Scholars',
            default                 => 'Report',
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Build query based on filters
    // ─────────────────────────────────────────────────────────────────────────

    public function collection(): Collection
    {
        $query = Application::with('user');

        // Status filter
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        } elseif ($this->reportType === 'approved_scholars') {
            $query->where('status', 'approved');
        } else {
            // Default: exclude drafts (only real submissions)
            $query->whereNotIn('status', ['draft']);
        }

        // Date range
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        // Gender (via NIN prefix)
        if (!empty($this->filters['gender'])) {
            $prefix = $this->filters['gender'] === 'female' ? 'CF' : 'CM';
            $query->where('personal_info->nin', 'like', $prefix . '%');
        }

        // Nationality
        if (!empty($this->filters['nationality'])) {
            if ($this->filters['nationality'] === 'ugandan') {
                $query->where('personal_info->is_ugandan', 'yes');
            } else {
                $query->where('personal_info->is_ugandan', 'no');
            }
        }

        return $query->get();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Headings per report type
    // ─────────────────────────────────────────────────────────────────────────

    public function headings(): array
    {
        return match ($this->reportType) {
            'applications_summary' => [
                'ID', 'Status', 'Applicant Name', 'Email', 'Nationality',
                'Gender', 'District', 'University', 'Programme', 'Submitted On',
            ],
            'applicant_details' => [
                'ID', 'Status', 'Surname', 'Other Names', 'Date of Birth', 'NIN',
                'Phone', 'Personal Email', 'Account Email', 'Marital Status',
                'Ugandan National', 'Has Disability', 'Residence District',
                'Residence Region', 'Submitted On',
            ],
            'scoring_report' => [
                'ID', 'Applicant Name', 'Status',
                'Financial Need (/30)', 'Academic Merit (/25)', 'Demographics (/15)',
                'Commitment (/15)', 'Essay Quality (/15)', 'Total Score (/100)',
            ],
            'district_report' => [
                'ID', 'Applicant Name', 'Status',
                'Birth District', 'Origin District', 'Residence District', 'Residence Region',
                'University', 'Programme',
            ],
            'university_report' => [
                'ID', 'Applicant Name', 'Status',
                'University / Institution', 'Academic Programme',
                'Teaching Subject 1', 'Teaching Subject 2',
                'Student Admission Number',
            ],
            'gender_report' => [
                'ID', 'Applicant Name', 'Status', 'Gender',
                'Date of Birth', 'Marital Status', 'Has Disability',
                'Residence District', 'University',
            ],
            'financial_report' => [
                'ID', 'Applicant Name', 'Status',
                'Annual Household Income (UGX)', 'No. of Dependents',
                'Primary Income Source', 'Other Financial Support',
                'Financial Need Score (/30)',
            ],
            'approved_scholars' => [
                'ID', 'Applicant Name', 'Account Email',
                'NIN', 'Phone', 'University', 'Programme',
                'Teaching Subject 1', 'Teaching Subject 2',
                'Admission Number', 'Total Score (/100)', 'Approved On',
            ],
            default => ['ID', 'Status', 'Applicant Name', 'Submitted On'],
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Row mapping per report type
    // ─────────────────────────────────────────────────────────────────────────

    public function map($app): array
    {
        $p  = $app->personal_info    ?? [];
        $fi = $app->financial_info   ?? [];
        $sc = $app->scoring_breakdown ?? [];

        $fullName = trim(($p['surname'] ?? '') . ' ' . ($p['other_names'] ?? ''))
            ?: ($app->user?->name ?? '—');

        $nin    = trim((string) ($p['nin'] ?? ''));
        $prefix = strtoupper(substr($nin, 0, 2));
        $gender = match ($prefix) {
            'CF'    => 'Female',
            'CM'    => 'Male',
            default => 'Unknown',
        };

        $nationality = ($p['is_ugandan'] ?? null) === 'yes'
            ? 'Ugandan'
            : (trim((string) ($p['non_ugandan_explanation'] ?? '')) ?: 'Non-Ugandan');

        return match ($this->reportType) {
            'applications_summary' => [
                $app->id,
                ucwords(str_replace('_', ' ', $app->status ?? '')),
                $fullName,
                $app->user?->email,
                $nationality,
                $gender,
                $p['residence_district'] ?? '',
                $p['institution'] ?? '',
                $p['academic_programme'] ?? '',
                $app->created_at?->format('Y-m-d'),
            ],
            'applicant_details' => [
                $app->id,
                ucwords(str_replace('_', ' ', $app->status ?? '')),
                $p['surname'] ?? '',
                $p['other_names'] ?? '',
                $p['date_of_birth'] ?? '',
                $p['nin'] ?? '',
                $p['phone'] ?? '',
                $p['email'] ?? '',
                $app->user?->email,
                $p['marital_status'] ?? '',
                ($p['is_ugandan'] ?? null) === 'yes' ? 'Yes' : 'No',
                ($p['has_disability'] ?? null) === 'yes' ? 'Yes' : 'No',
                $p['residence_district'] ?? '',
                $p['residence_region'] ?? '',
                $app->created_at?->format('Y-m-d'),
            ],
            'scoring_report' => [
                $app->id,
                $fullName,
                ucwords(str_replace('_', ' ', $app->status ?? '')),
                $sc['financial_need'] ?? 0,
                $sc['academic_merit']  ?? 0,
                $sc['demographics']    ?? 0,
                $sc['commitment']      ?? 0,
                $sc['essay_quality']   ?? 0,
                $sc['total']           ?? 0,
            ],
            'district_report' => [
                $app->id,
                $fullName,
                ucwords(str_replace('_', ' ', $app->status ?? '')),
                $p['birth_district']     ?? '',
                $p['origin_district']    ?? '',
                $p['residence_district'] ?? '',
                $p['residence_region']   ?? '',
                $p['institution']        ?? '',
                $p['academic_programme'] ?? '',
            ],
            'university_report' => [
                $app->id,
                $fullName,
                ucwords(str_replace('_', ' ', $app->status ?? '')),
                $p['institution']              ?? '',
                $p['academic_programme']       ?? '',
                $p['teaching_subjects_1']      ?? '',
                $p['teaching_subjects_2']      ?? '',
                $p['student_admission_number'] ?? '',
            ],
            'gender_report' => [
                $app->id,
                $fullName,
                ucwords(str_replace('_', ' ', $app->status ?? '')),
                $gender,
                $p['date_of_birth'] ?? '',
                $p['marital_status'] ?? '',
                ($p['has_disability'] ?? null) === 'yes' ? 'Yes' : 'No',
                $p['residence_district'] ?? '',
                $p['institution'] ?? '',
            ],
            'financial_report' => [
                $app->id,
                $fullName,
                ucwords(str_replace('_', ' ', $app->status ?? '')),
                $fi['household_income']       ?? '',
                $fi['number_of_dependents']   ?? '',
                $fi['income_source']          ?? '',
                $fi['other_financial_support'] ?? '',
                $sc['financial_need']         ?? 0,
            ],
            'approved_scholars' => [
                $app->id,
                $fullName,
                $app->user?->email,
                $p['nin'] ?? '',
                $p['phone'] ?? '',
                $p['institution'] ?? '',
                $p['academic_programme'] ?? '',
                $p['teaching_subjects_1'] ?? '',
                $p['teaching_subjects_2'] ?? '',
                $p['student_admission_number'] ?? '',
                $sc['total'] ?? 0,
                $app->updated_at?->format('Y-m-d'),
            ],
            default => [
                $app->id,
                ucwords(str_replace('_', ' ', $app->status ?? '')),
                $fullName,
                $app->created_at?->format('Y-m-d'),
            ],
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Styles
    // ─────────────────────────────────────────────────────────────────────────

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
