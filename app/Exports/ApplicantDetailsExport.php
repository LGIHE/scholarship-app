<?php

namespace App\Exports;

use App\Models\Application;
use App\Support\ApprovedCriteria;
use App\Console\Commands\NormaliseInstitutions;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

/**
 * Specialized export class for the "Applicant Details" report type.
 * 
 * This export ensures clean data by applying strict filtering criteria:
 * 
 * 1. Gender: Only female applicants (either NIN prefix "CF" or gender field = "female")
 * 2. Universities: Only the 14 specified approved institutions
 * 3. Course: Only "Bachelor of Science with Education" 
 * 4. Subjects: Must have at least one of: Biology, Chemistry, Physics, Mathematics, Agriculture, Computer Studies/ICT/IT
 * 5. Status: Only submitted applications
 * 6. Date filtering: Optional filtering by submission date range
 * 
 * This addresses the requirements to ensure the "Applicant Details" report
 * returns only clean, properly filtered data that meets all eligibility criteria.
 */
class ApplicantDetailsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * Approved universities/institutions for the applicant details report
     */
    private const APPROVED_UNIVERSITIES = [
        'Makerere University',
        'Kyambogo University', 
        'Busitema University',
        'Islamic University in Uganda',
        'Gulu University',
        'Muni University',
        'Mountains of the Moon University',
        'Mbarara University of Science and Technology',
        'Uganda Martyrs University',
        'Kabale University',
        'UNITE Kabale Campus',
        'UNITE Kaliro Campus',
        'UNITE Mubende Campus',
        'UNITE Muni Campus',
    ];

    /** @var string|null  Date string (Y-m-d) — inclusive lower bound on created_at */
    protected ?string $dateFrom;

    /** @var string|null  Date string (Y-m-d) — inclusive upper bound on created_at */
    protected ?string $dateTo;

    /**
     * @param string|null $dateFrom  Submitted-from date (Y-m-d), inclusive.
     * @param string|null $dateTo    Submitted-to date (Y-m-d), inclusive (end of day).
     */
    public function __construct(?string $dateFrom = null, ?string $dateTo = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function collection(): Collection
    {
        $query = Application::with('user')
            ->where('status', 'submitted'); // Only submitted applications

        // Apply date filters if provided
        if ($this->dateFrom !== null) {
            try {
                $query->where('created_at', '>=',
                    \Carbon\Carbon::parse($this->dateFrom, config('app.timezone'))->startOfDay()->utc()
                );
            } catch (\Exception $e) {
                // If date parsing fails, ignore the filter
            }
        }

        if ($this->dateTo !== null) {
            try {
                // "Submitted By" means applications submitted ON or BEFORE this date
                // So we use the end of the specified date
                $query->where('created_at', '<=',
                    \Carbon\Carbon::parse($this->dateTo, config('app.timezone'))->endOfDay()->utc()
                );
            } catch (\Exception $e) {
                // If date parsing fails, ignore the filter
            }
        }

        $applications = $query->get();

        // Filter applications based on eligibility criteria
        return $applications->filter(function ($app) {
            $personalInfo = $app->personal_info ?? [];
            
            // Must be female (either explicit gender or NIN prefix CF)
            $isFemale = $this->isFemaleApplicant($personalInfo);
            if (!$isFemale) {
                return false;
            }

            // Must have approved course (Bachelor of Science with Education)
            $hasApprovedCourse = ApprovedCriteria::hasApprovedCourse($personalInfo);
            if (!$hasApprovedCourse) {
                return false;
            }

            // Must have at least one approved subject
            $hasApprovedSubject = ApprovedCriteria::hasApprovedSubject($personalInfo);
            if (!$hasApprovedSubject) {
                return false;
            }

            // Must be from an approved university/institution
            $hasApprovedUniversity = $this->hasApprovedUniversity($personalInfo);
            if (!$hasApprovedUniversity) {
                return false;
            }

            return true;
        });
    }

    /**
     * Check if the applicant is female (either by NIN prefix CF or explicit gender)
     */
    private function isFemaleApplicant(array $personalInfo): bool
    {
        // Check NIN prefix first (CF indicates female)
        $nin = trim((string) ($personalInfo['nin'] ?? ''));
        if (strlen($nin) >= 2) {
            $prefix = strtoupper(substr($nin, 0, 2));
            if ($prefix === 'CF') {
                return true;
            }
        }

        // Check explicit gender field (handle various possible field names)
        $genderFields = ['gender', 'sex', 'applicant_gender'];
        foreach ($genderFields as $field) {
            $gender = strtolower(trim((string) ($personalInfo[$field] ?? '')));
            if ($gender === 'female' || $gender === 'f') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the applicant is from an approved university/institution
     */
    private function hasApprovedUniversity(array $personalInfo): bool
    {
        $institution = trim((string) ($personalInfo['institution'] ?? ''));
        if ($institution === '') {
            return false;
        }

        $normalizedInstitution = strtolower($institution);
        
        // Define keyword mappings for more accurate matching
        $universityKeywords = [
            'makerere' => ['Makerere University'],
            'kyambogo' => ['Kyambogo University'],
            'busitema' => ['Busitema University'],
            'islamic' => ['Islamic University in Uganda'],
            'iuiu' => ['Islamic University in Uganda'],
            'gulu' => ['Gulu University'],
            'muni' => ['Muni University', 'UNITE Muni Campus'],
            'mountains of the moon' => ['Mountains of the Moon University'],
            'mountain of the moon' => ['Mountains of the Moon University'],
            'mmu' => ['Mountains of the Moon University'],
            'mbarara' => ['Mbarara University of Science and Technology'],
            'must' => ['Mbarara University of Science and Technology'],
            'uganda martyrs' => ['Uganda Martyrs University'],
            'umu' => ['Uganda Martyrs University'],
            'kabale university' => ['Kabale University'],
            'kabale' => ['Kabale University', 'UNITE Kabale Campus'],
            'unite kabale' => ['UNITE Kabale Campus'],
            'unite kaliro' => ['UNITE Kaliro Campus'],
            'kaliro' => ['UNITE Kaliro Campus'],
            'unite mubende' => ['UNITE Mubende Campus'],
            'mubende' => ['UNITE Mubende Campus'],
            'unite muni' => ['UNITE Muni Campus'],
        ];

        // Check for keyword matches
        foreach ($universityKeywords as $keyword => $universities) {
            if (str_contains($normalizedInstitution, $keyword)) {
                // Found a match, verify it's in our approved list
                foreach ($universities as $uni) {
                    if (in_array($uni, self::APPROVED_UNIVERSITIES)) {
                        return true;
                    }
                }
            }
        }

        // Direct name matching (case-insensitive)
        foreach (self::APPROVED_UNIVERSITIES as $approvedUni) {
            $normalizedApproved = strtolower($approvedUni);
            
            if ($normalizedInstitution === $normalizedApproved || 
                str_contains($normalizedInstitution, $normalizedApproved) ||
                str_contains($normalizedApproved, $normalizedInstitution)) {
                return true;
            }
        }

        return false;
    }

    public function headings(): array
    {
        return [
            'Application ID',
            'Status',
            'Submitted On',
            'Account Name',
            'Account Email',
            'Surname',
            'Other Names',
            'Date of Birth',
            'NIN',
            'Telephone',
            'Personal Email',
            'Academic Programme',
            'Institution',
            'Teaching Subject 1',
            'Teaching Subject 2',
            'Residence District',
            'Residence Region',
            'Birth District',
            'Origin District',
        ];
    }

    public function map($app): array
    {
        $p = $app->personal_info ?? [];

        return [
            $app->id,
            ucwords(str_replace('_', ' ', $app->status ?? '')),
            $app->created_at?->format('Y-m-d H:i:s'),
            $app->user?->name,
            $app->user?->email,
            $p['surname'] ?? '',
            $p['other_names'] ?? '',
            $p['date_of_birth'] ?? '',
            $p['nin'] ?? '',
            $p['phone'] ?? '',
            $p['email'] ?? '',
            $p['academic_programme'] ?? '',
            $p['institution'] ?? '',
            $p['teaching_subjects_1'] ?? '',
            $p['teaching_subjects_2'] ?? '',
            $p['residence_district'] ?? '',
            $p['residence_region'] ?? '',
            $p['birth_district'] ?? '',
            $p['origin_district'] ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}