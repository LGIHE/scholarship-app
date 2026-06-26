<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    /**
     * Show the multi-step application form.
     */
    public function form(Request $request): Response
    {
        $application = Application::where('user_id', $request->user()->id)
            ->where('status', 'draft')
            ->latest()
            ->first();

        if (! $application) {
            $application = Application::where('user_id', $request->user()->id)
                ->latest()
                ->first();
        }

        $deadline = \Carbon\Carbon::parse(config('scholarship.application_deadline'))->setTime(23, 59, 59);

        return Inertia::render('Application/Form', [
            'application'      => $application,
            'deadlinePassed'   => now()->greaterThan($deadline),
            'applicationDeadline' => $deadline->toDateString(),
        ]);
    }

    /**
     * Check whether the application deadline has passed.
     * The deadline expires at 23:59:59 on the configured date.
     */
    private function isDeadlinePassed(): bool
    {
        $deadline = \Carbon\Carbon::parse(config('scholarship.application_deadline'))->setTime(23, 59, 59);
        return now()->greaterThan($deadline);
    }

    /**
     * Save application as draft.
     */
    public function draft(Request $request)
    {
        if ($this->isDeadlinePassed()) {
            return response()->json([
                'message' => 'The application deadline has passed. No further edits are allowed.',
            ], 403);
        }

        // Parse JSON fields
        $personalInfo    = json_decode($request->input('personal_info', '{}'), true) ?? [];
        $disabilityInfo  = json_decode($request->input('disability_info', '{}'), true) ?? [];
        $dependantsInfo  = json_decode($request->input('dependants_info', '{}'), true) ?? [];
        $financialInfo   = json_decode($request->input('financial_info', '{}'), true) ?? [];
        $essay           = json_decode($request->input('essay', '{}'), true) ?? [];
        $guardianInfo    = json_decode($request->input('guardian_info', '{}'), true) ?? [];
        $declarationInfo = json_decode($request->input('declaration_info', '{}'), true) ?? [];

        // Find existing draft or submitted application (submitted can still be edited before deadline)
        $application = Application::where('user_id', $request->user()->id)
            ->whereIn('status', ['draft', 'submitted'])
            ->latest()
            ->first();

        // Preserve existing documents
        $existingDocuments = $application ? ($application->documents ?? []) : [];
        $documentPaths = $existingDocuments;

        // Generate applicant name for file naming
        $surname    = strtolower(str_replace(' ', '_', $personalInfo['surname'] ?? 'applicant'));
        $otherNames = strtolower(str_replace(' ', '_', $personalInfo['other_names'] ?? ''));
        $applicantName = $surname . ($otherNames ? '_' . $otherNames : '');

        // Handle new document uploads
        $docFields = ['exam_results', 'national_id', 'birth_certificate', 'admission_letter',
                      'recommendation_lc1', 'recommendation_school', 'refugee_number'];

        foreach ($docFields as $field) {
            if ($request->hasFile("documents.{$field}")) {
                if (isset($existingDocuments[$field])) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($existingDocuments[$field]);
                }
                $file = $request->file("documents.{$field}");
                $ext  = $file->getClientOriginalExtension();
                $documentPaths[$field] = $file->storeAs(
                    'applications/documents',
                    "{$applicantName}_{$field}.{$ext}",
                    'public'
                );
            }
        }

        $updateData = [
            'personal_info'    => $personalInfo,
            'disability_info'  => $disabilityInfo,
            'dependants_info'  => $dependantsInfo,
            'financial_info'   => $financialInfo,
            'essay'            => $essay,
            'guardian_info'    => $guardianInfo,
            'declaration_info' => $declarationInfo,
            'documents'        => $documentPaths,
        ];

        if ($application) {
            $application->update($updateData);
        } else {
            $application = Application::create(array_merge($updateData, [
                'user_id' => $request->user()->id,
                'status'  => 'draft',
            ]));
        }

        return response()->json(['message' => 'Draft saved successfully', 'application' => $application]);
    }

    /**
     * Submit the final application.
     */
    public function submit(Request $request)
    {
        if ($this->isDeadlinePassed()) {
            return redirect()->route('portal')->withErrors([
                'deadline' => 'The application deadline has passed. Submissions are no longer accepted.',
            ]);
        }

        \Illuminate\Support\Facades\Log::info('Application submission attempt', [
            'user_id' => $request->user()->id,
        ]);

        // Get existing draft or submitted application for previously uploaded documents
        $existingDraft = Application::where('user_id', $request->user()->id)
            ->whereIn('status', ['draft', 'submitted'])
            ->latest()
            ->first();

        $existingDocuments = $existingDraft ? ($existingDraft->documents ?? []) : [];

        // Determine nationality for conditional validation
        $isUgandan = $request->input('personal_info.is_ugandan') === 'yes';

        // Build validation rules
        $validationRules = [
            'personal_info'                     => 'required|array',
            'personal_info.surname'             => 'required|string|max:255',
            'personal_info.other_names'         => 'required|string|max:255',
            'personal_info.date_of_birth'       => 'required|date',
            // NIN required only for Ugandans; non-Ugandans must supply at least one alternative ID
            'personal_info.nin'                 => $isUgandan ? 'required|string|min:4|max:14' : 'nullable|string|max:14',
            'personal_info.passport_number'     => !$isUgandan ? 'nullable|string|max:50' : 'nullable',
            'personal_info.foreign_id_number'   => !$isUgandan ? 'nullable|string|max:100' : 'nullable',
            'personal_info.refugee_card_number' => !$isUgandan ? 'nullable|string|max:100' : 'nullable',
            'personal_info.non_ugandan_explanation' => !$isUgandan ? 'required|string|max:500' : 'nullable',
            'personal_info.phone'               => 'required|string|max:30',
            'personal_info.marital_status'      => 'required|string|max:100',
            'personal_info.is_ugandan'          => 'required|string|in:yes,no',
            'personal_info.academic_programme'  => 'required|string|max:255',
            'personal_info.institution'         => 'required|string|max:255',
            'guardian_info'                     => 'required|array',
            'guardian_info.guardian_surname'    => 'required|string|max:255',
            'guardian_info.guardian_telephone'  => 'required|string|max:30',
            'guardian_info.guardian_relation'   => 'required|string|max:100',
            'essay'                             => 'required|array',
            'essay.motivation'                  => 'required|string|min:50',
            'personal_info.hearing_source'      => 'required|string|in:organization_website,social_media,referral,advertisement,professional_network,email_newsletter,walk_in,other',
            'personal_info.hearing_source_other' => 'required_if:personal_info.hearing_source,other|nullable|string|max:500',
        ];

        // Non-Ugandans must supply at least one form of ID
        if (!$isUgandan) {
            $hasId = $request->filled('personal_info.passport_number')
                || $request->filled('personal_info.foreign_id_number')
                || $request->filled('personal_info.refugee_card_number');
            if (!$hasId) {
                return redirect()->back()->withErrors([
                    'personal_info.passport_number' => 'Please provide at least one identification document (Passport, National ID, or Refugee Card).',
                ])->withInput();
            }
        }

        // Required documents: only validate if not already uploaded
        if ($request->hasFile('documents.exam_results') || ! isset($existingDocuments['exam_results'])) {
            $validationRules['documents.exam_results'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }
        if ($request->hasFile('documents.national_id') || ! isset($existingDocuments['national_id'])) {
            $validationRules['documents.national_id'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        // Optional documents
        foreach (['birth_certificate','admission_letter','recommendation_lc1','recommendation_school','refugee_number'] as $field) {
            if ($request->hasFile("documents.{$field}")) {
                $validationRules["documents.{$field}"] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
            }
        }

        $request->validate($validationRules, [], [
            'personal_info.surname'            => 'surname',
            'personal_info.other_names'        => 'other name(s)',
            'personal_info.date_of_birth'      => 'date of birth',
            'personal_info.nin'                => 'National Identification Number (NIN)',
            'personal_info.passport_number'    => 'passport number',
            'personal_info.foreign_id_number'  => 'national ID number',
            'personal_info.refugee_card_number'=> 'refugee card number',
            'personal_info.non_ugandan_explanation' => 'nationality',
            'personal_info.phone'              => 'telephone number',
            'personal_info.marital_status'     => 'marital status',
            'personal_info.is_ugandan'         => 'Ugandan nationality',
            'personal_info.academic_programme' => 'academic programme',
            'personal_info.institution'        => 'institution',
            'guardian_info.guardian_surname'   => 'guardian surname',
            'guardian_info.guardian_telephone' => 'guardian telephone',
            'guardian_info.guardian_relation'  => 'guardian relationship',
            'essay.motivation'                 => 'motivation essay',
            'documents.exam_results'           => 'examination results',
            'documents.national_id'            => 'national ID',
            'personal_info.hearing_source'     => 'how you heard about the scholarship',
            'personal_info.hearing_source_other' => 'other source specification',
        ]);

        // Generate applicant name for file naming
        $surname    = strtolower(str_replace(' ', '_', $request->input('personal_info.surname', 'applicant')));
        $otherNames = strtolower(str_replace(' ', '_', $request->input('personal_info.other_names', '')));
        $applicantName = $surname . ($otherNames ? '_' . $otherNames : '');

        // Handle document uploads
        $documentPaths = $existingDocuments;
        $docFields = ['exam_results','national_id','birth_certificate','admission_letter',
                      'recommendation_lc1','recommendation_school','refugee_number'];

        foreach ($docFields as $field) {
            if ($request->hasFile("documents.{$field}")) {
                if (isset($existingDocuments[$field])) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($existingDocuments[$field]);
                }
                $file = $request->file("documents.{$field}");
                $ext  = $file->getClientOriginalExtension();
                $documentPaths[$field] = $file->storeAs(
                    'applications/documents',
                    "{$applicantName}_{$field}.{$ext}",
                    'public'
                );
            }
        }

        $submitData = [
            'personal_info'    => $request->input('personal_info', []),
            'disability_info'  => $request->input('disability_info', []),
            'dependants_info'  => $request->input('dependants_info', []),
            'financial_info'   => $request->input('financial_info', []),
            'guardian_info'    => $request->input('guardian_info', []),
            'declaration_info' => $request->input('declaration_info', []),
            'essay'            => $request->input('essay', []),
            'documents'        => $documentPaths,
            'status'           => 'submitted',
        ];

        if ($existingDraft) {
            $existingDraft->update($submitData);
            $application = $existingDraft;
        } else {
            $application = Application::create(array_merge($submitData, [
                'user_id' => $request->user()->id,
            ]));
        }

        $scoringService = new \App\Services\ScoringService();
        $scoringService->score($application);

        \Illuminate\Support\Facades\Log::info('Application submitted successfully', [
            'application_id' => $application->id,
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($request->user())->send(new \App\Mail\ApplicationReceived($application));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send application email: ' . $e->getMessage());
        }

        return redirect()->route('portal')->with('success', 'Application submitted successfully.');
    }
}
