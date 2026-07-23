<?php

namespace App\Exports;

use App\Models\Application;
use App\Helpers\ApprovedCriteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Export class for generating participant profile ZIP files.
 * 
 * Creates a ZIP archive containing individual folders for each participant,
 * with each folder containing:
 * - Application_Profile.pdf (comprehensive participant information)
 * - All uploaded documents (exam results, NID, birth certificate, etc.)
 */
class ParticipantProfileExport
{
    /** @var string|null Date string (Y-m-d) — inclusive lower bound on created_at */
    protected ?string $dateFrom;

    /** @var string|null Date string (Y-m-d) — inclusive upper bound on created_at */
    protected ?string $dateTo;

    /** @var int|null Cohort ID filter */
    protected ?int $cohortId;

    /**
     * @param string|null $dateFrom Submitted-from date (Y-m-d), inclusive.
     * @param string|null $dateTo   Submitted-to date (Y-m-d), inclusive (end of day).
     * @param int|null    $cohortId Optional cohort filter
     */
    public function __construct(?string $dateFrom = null, ?string $dateTo = null, ?int $cohortId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->cohortId = $cohortId;
    }

    /**
     * Generate and return the ZIP file path containing participant profiles
     */
    public function generateZip(): string
    {
        try {
            $applications = $this->getFilteredApplications();
            
            \Log::info("ParticipantProfileExport: Found {$applications->count()} filtered applications");
            
            if ($applications->isEmpty()) {
                \Log::warning("ParticipantProfileExport: No applications found matching criteria");
                throw new \Exception("No participants found matching the selected criteria.");
            }
            
            // Create temporary directory for ZIP creation
            $tmpDir = sys_get_temp_dir() . '/participant_profiles_' . uniqid();
            \Log::info("ParticipantProfileExport: Creating temp directory: {$tmpDir}");
            
            if (!mkdir($tmpDir, 0755, true)) {
                throw new \Exception("Failed to create temporary directory: {$tmpDir}");
            }
            
            $zipPath = $tmpDir . '/participant_profiles.zip';
            $zip = new ZipArchive();
            
            $zipResult = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            if ($zipResult !== TRUE) {
                throw new \Exception("Cannot create ZIP file: {$zipPath}. ZipArchive error code: {$zipResult}");
            }

            \Log::info("ParticipantProfileExport: ZIP file created, processing {$applications->count()} applications");

            foreach ($applications as $index => $application) {
                try {
                    $currentNum = $index + 1;
                    $totalNum = $applications->count();
                    \Log::info("ParticipantProfileExport: Processing application {$application->id} ({$currentNum}/{$totalNum})");
                    $this->addParticipantToZip($zip, $application, $tmpDir);
                } catch (\Exception $e) {
                    \Log::error("ParticipantProfileExport: Failed to process application {$application->id}: " . $e->getMessage());
                    // Continue with other applications instead of failing completely
                }
            }

            $zip->close();
            
            if (!file_exists($zipPath)) {
                throw new \Exception("ZIP file was not created at expected path: {$zipPath}");
            }
            
            $fileSize = filesize($zipPath);
            \Log::info("ParticipantProfileExport: ZIP file created successfully. Size: {$fileSize} bytes");
            
            return $zipPath;
            
        } catch (\Exception $e) {
            \Log::error("ParticipantProfileExport: generateZip failed: " . $e->getMessage());
            \Log::error("ParticipantProfileExport: Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Get filtered applications based on the same criteria as ApplicantDetailsExport
     */
    protected function getFilteredApplications(): Collection
    {
        $query = Application::with(['user', 'cohort'])
            ->where('status', 'submitted'); // Only submitted applications

        // Apply cohort filter if provided
        if ($this->cohortId !== null) {
            $query->where('cohort_id', $this->cohortId);
        }

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
                $query->where('created_at', '<=',
                    \Carbon\Carbon::parse($this->dateTo, config('app.timezone'))->endOfDay()->utc()
                );
            } catch (\Exception $e) {
                // If date parsing fails, ignore the filter
            }
        }

        $applications = $query->get();

        // Apply the same filtering logic as ApplicantDetailsExport
        return $applications->filter(function ($app) {
            $personalInfo = $app->personal_info ?? [];
            
            // Must be female (either explicit gender or NIN prefix CF)
            if (!$this->isFemaleApplicant($personalInfo)) {
                return false;
            }

            // Must have approved course (Bachelor of Science with Education)
            if (!ApprovedCriteria::hasApprovedCourse($personalInfo)) {
                return false;
            }

            // Must have at least one approved subject
            if (!ApprovedCriteria::hasApprovedSubject($personalInfo)) {
                return false;
            }

            // Must be from an approved university/institution
            if (!$this->hasApprovedUniversity($personalInfo)) {
                return false;
            }

            return true;
        });
    }

    /**
     * Add a single participant's folder and files to the ZIP
     */
    protected function addParticipantToZip(ZipArchive $zip, Application $application, string $tmpDir): void
    {
        $personalInfo = $application->personal_info ?? [];
        $documents = $application->documents ?? [];
        
        // Generate participant folder name
        $surname = $this->sanitizeFilename($personalInfo['surname'] ?? 'Unknown');
        $otherNames = $this->sanitizeFilename($personalInfo['other_names'] ?? '');
        $folderName = $surname . ($otherNames ? '_' . $otherNames : '');
        
        \Log::info("ParticipantProfileExport: Processing participant folder: {$folderName} (Application ID: {$application->id})");
        
        // Ensure unique folder names in case of duplicates
        $originalFolderName = $folderName;
        $counter = 1;
        while ($this->folderExistsInZip($zip, $folderName)) {
            $folderName = $originalFolderName . '_' . $counter;
            $counter++;
        }

        // Generate PDF profile
        try {
            \Log::info("ParticipantProfileExport: Generating PDF for {$folderName}");
            $pdfPath = $this->generateParticipantPDF($application, $tmpDir, $folderName);
            if ($pdfPath && file_exists($pdfPath)) {
                $addResult = $zip->addFile($pdfPath, $folderName . '/Application_Profile.pdf');
                if (!$addResult) {
                    \Log::error("ParticipantProfileExport: Failed to add PDF to ZIP for {$folderName}");
                } else {
                    \Log::info("ParticipantProfileExport: PDF added to ZIP for {$folderName}");
                }
            } else {
                \Log::warning("ParticipantProfileExport: No PDF generated for {$folderName}");
            }
        } catch (\Exception $e) {
            \Log::error("ParticipantProfileExport: PDF generation failed for {$folderName}: " . $e->getMessage());
        }

        // Add uploaded documents
        try {
            $this->addDocumentsToZip($zip, $documents, $folderName);
        } catch (\Exception $e) {
            \Log::error("ParticipantProfileExport: Document addition failed for {$folderName}: " . $e->getMessage());
        }
    }

    /**
     * Generate PDF profile for a participant
     */
    protected function generateParticipantPDF(Application $application, string $tmpDir, string $folderName): ?string
    {
        try {
            \Log::info("ParticipantProfileExport: Starting PDF generation for {$folderName}");
            
            $pdfData = [
                'application' => $application,
                'personalInfo' => $application->personal_info ?? [],
                'disabilityInfo' => $application->disability_info ?? [],
                'dependantsInfo' => $application->dependants_info ?? [],
                'financialInfo' => $application->financial_info ?? [],
                'guardianInfo' => $application->guardian_info ?? [],
                'declarationInfo' => $application->declaration_info ?? [],
                'essay' => $application->essay ?? [],
                'documents' => $application->documents ?? [],
            ];

            $pdf = Pdf::loadView('reports.participant-profile', $pdfData)
                ->setPaper('a4', 'portrait');

            $pdfPath = $tmpDir . '/' . $folderName . '_profile.pdf';
            \Log::info("ParticipantProfileExport: Saving PDF to {$pdfPath}");
            
            $pdf->save($pdfPath);
            
            if (file_exists($pdfPath)) {
                $fileSize = filesize($pdfPath);
                \Log::info("ParticipantProfileExport: PDF saved successfully for {$folderName}. Size: {$fileSize} bytes");
                return $pdfPath;
            } else {
                \Log::error("ParticipantProfileExport: PDF file was not created at {$pdfPath}");
                return null;
            }
            
        } catch (\Exception $e) {
            \Log::error("ParticipantProfileExport: Failed to generate PDF for participant {$folderName}: " . $e->getMessage());
            \Log::error("ParticipantProfileExport: PDF generation stack trace: " . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Add participant's uploaded documents to ZIP
     */
    protected function addDocumentsToZip(ZipArchive $zip, array $documents, string $folderName): void
    {
        $documentLabels = [
            'exam_results' => 'Exam_Results',
            'national_id' => 'National_ID',
            'birth_certificate' => 'Birth_Certificate', 
            'admission_letter' => 'Admission_Letter',
            'recommendation_lc1' => 'Recommendation_LC1',
            'recommendation_school' => 'School_Recommendation',
            'refugee_number' => 'Refugee_Number'
        ];

        $addedCount = 0;
        foreach ($documents as $field => $filePath) {
            if (empty($filePath)) {
                continue;
            }

            $fullPath = storage_path('app/public/' . $filePath);
            
            if (file_exists($fullPath)) {
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                $documentName = $documentLabels[$field] ?? ucfirst($field);
                $zipFileName = $folderName . '/' . $documentName . '.' . $extension;
                
                $addResult = $zip->addFile($fullPath, $zipFileName);
                if ($addResult) {
                    $addedCount++;
                    \Log::info("ParticipantProfileExport: Added document {$field} for {$folderName}");
                } else {
                    \Log::error("ParticipantProfileExport: Failed to add document {$field} for {$folderName}");
                }
            } else {
                \Log::warning("ParticipantProfileExport: Document file not found: {$fullPath} for {$folderName}");
            }
        }
        
        \Log::info("ParticipantProfileExport: Added {$addedCount} documents for {$folderName}");
    }

    /**
     * Check if a folder name already exists in the ZIP
     */
    protected function folderExistsInZip(ZipArchive $zip, string $folderName): bool
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (strpos($filename, $folderName . '/') === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sanitize filename for safe usage in file system
     */
    protected function sanitizeFilename(string $filename): string
    {
        // Remove or replace problematic characters
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename);
        // Remove multiple consecutive underscores
        $filename = preg_replace('/_{2,}/', '_', $filename);
        // Trim underscores from start and end
        $filename = trim($filename, '_');
        
        return $filename ?: 'Participant';
    }

    /**
     * Check if the applicant is female (either by NIN prefix CF or explicit gender)
     * Same logic as ApplicantDetailsExport
     */
    protected function isFemaleApplicant(array $personalInfo): bool
    {
        // Check NIN prefix first (CF indicates female)
        $nin = trim((string) ($personalInfo['nin'] ?? ''));
        if (strlen($nin) >= 2) {
            $prefix = strtoupper(substr($nin, 0, 2));
            if ($prefix === 'CF') {
                return true;
            }
        }

        // Check explicit gender field
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
     * Same logic as ApplicantDetailsExport
     */
    protected function hasApprovedUniversity(array $personalInfo): bool
    {
        $approvedUniversities = [
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
                foreach ($universities as $uni) {
                    if (in_array($uni, $approvedUniversities)) {
                        return true;
                    }
                }
            }
        }

        // Direct name matching (case-insensitive)
        foreach ($approvedUniversities as $approvedUni) {
            $normalizedApproved = strtolower($approvedUni);
            
            if ($normalizedInstitution === $normalizedApproved || 
                str_contains($normalizedInstitution, $normalizedApproved) ||
                str_contains($normalizedApproved, $normalizedInstitution)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clean up temporary files
     */
    public function cleanup(string $zipPath): void
    {
        $tmpDir = dirname($zipPath);
        
        // Remove all files in temp directory
        $files = glob($tmpDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        // Remove temp directory
        if (is_dir($tmpDir)) {
            rmdir($tmpDir);
        }
    }
}