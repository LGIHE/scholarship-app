<?php

namespace App\Exports;

use App\Models\Application;
use Illuminate\Support\Collection;
use ZipArchive;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Export class for generating participant profile ZIP files.
 * 
 * Creates a ZIP archive containing individual folders for each participant,
 * with each folder containing:
 * - Application_Profile.pdf (comprehensive participant information)
 * - All uploaded documents (exam results, NID, birth certificate, etc.)
 * 
 * Respects all filters from the Reports page.
 */
class ParticipantProfileExport
{
    /** @var array All filters from the Reports page */
    protected array $filters;

    /**
     * @param array $filters All filters from Reports page (cohort_id, status, gender, nationality, date_from, date_to)
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
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
     * Get filtered applications based on all Reports page filters
     */
    protected function getFilteredApplications(): Collection
    {
        $query = Application::with(['user', 'cohort']);

        // Apply status filter (default to submitted if not specified)
        $status = $this->filters['status'] ?? 'submitted';
        if ($status) {
            $query->where('status', $status);
        } else {
            // If no status specified, exclude drafts
            $query->whereNotIn('status', ['draft']);
        }

        // Apply cohort filter
        if (!empty($this->filters['cohort_id'])) {
            $query->where('cohort_id', $this->filters['cohort_id']);
        }

        // Apply date filters (Submitted From / Submitted By)
        if (!empty($this->filters['date_from'])) {
            try {
                $query->where('created_at', '>=',
                    \Carbon\Carbon::parse($this->filters['date_from'], config('app.timezone'))->startOfDay()->utc()
                );
            } catch (\Exception $e) {
                \Log::warning("ParticipantProfileExport: Invalid date_from format: " . $this->filters['date_from']);
            }
        }

        if (!empty($this->filters['date_to'])) {
            try {
                $query->where('created_at', '<=',
                    \Carbon\Carbon::parse($this->filters['date_to'], config('app.timezone'))->endOfDay()->utc()
                );
            } catch (\Exception $e) {
                \Log::warning("ParticipantProfileExport: Invalid date_to format: " . $this->filters['date_to']);
            }
        }

        $applications = $query->get();

        // Apply gender filter if specified
        if (!empty($this->filters['gender'])) {
            $applications = $applications->filter(function ($app) {
                $personalInfo = $app->personal_info ?? [];
                $gender = $this->filters['gender']; // 'female' or 'male'
                
                if ($gender === 'female') {
                    return $this->isFemaleApplicant($personalInfo);
                } elseif ($gender === 'male') {
                    return !$this->isFemaleApplicant($personalInfo);
                }
                
                return true;
            });
        }

        // Apply nationality filter if specified
        if (!empty($this->filters['nationality'])) {
            $applications = $applications->filter(function ($app) {
                $personalInfo = $app->personal_info ?? [];
                $nationality = $this->filters['nationality']; // 'ugandan' or 'non_ugandan'
                
                $isUgandan = ($personalInfo['is_ugandan'] ?? null) === 'yes';
                
                if ($nationality === 'ugandan') {
                    return $isUgandan;
                } elseif ($nationality === 'non_ugandan') {
                    return !$isUgandan;
                }
                
                return true;
            });
        }

        return $applications;
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
     */
    protected function isFemaleApplicant(array $personalInfo): bool
    {
        // Check NIN prefix first (CF indicates female, CM indicates male)
        $nin = trim((string) ($personalInfo['nin'] ?? ''));
        if (strlen($nin) >= 2) {
            $prefix = strtoupper(substr($nin, 0, 2));
            if ($prefix === 'CF') {
                return true;
            }
            if ($prefix === 'CM') {
                return false;
            }
        }

        // Check explicit gender field
        $genderFields = ['gender', 'sex', 'applicant_gender'];
        foreach ($genderFields as $field) {
            $gender = strtolower(trim((string) ($personalInfo[$field] ?? '')));
            if ($gender === 'female' || $gender === 'f') {
                return true;
            }
            if ($gender === 'male' || $gender === 'm') {
                return false;
            }
        }

        // Default to unknown/unfiltered
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