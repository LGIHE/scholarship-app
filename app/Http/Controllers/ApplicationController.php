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

        return Inertia::render('Application/Form', [
            'application' => $application,
        ]);
    }

    /**
     * Save application as draft.
     */
    public function draft(Request $request)
    {
        // Parse JSON fields
        $personalInfo    = json_decode($request->input('personal_info', '{}'), true) ?? [];
        $disabilityInfo  = json_decode($request->input('disability_info', '{}'), true) ?? [];
        $dependantsInfo  = json_decode($request->input('dependants_info', '{}'), true) ?? [];
        $financialInfo   = json_decode($request->input('financial_info', '{}'), true) ?? [];
        $essay           = json_decode($request->input('essay', '{}'), true) ?? [];
        $guardianInfo    = json_decode($request->input('guardian_info', '{}'), true) ?? [];
        $declarationInfo = json_decode($request->input('declaration_info', '{}'), true) ?? [];

        // Find existing draft
        $application = Application::where('user_id', $request->user()->id)
            ->where('status', 'draft')
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

        $application = Application::updateOrCreate(
            ['user_id' => $request->user()->id, 'status' => 'draft'],
            [
                'personal_info'    => $personalInfo,
                'disability_info'  => $disabilityInfo,
                'dependants_info'  => $dependantsInfo,
                'financial_info'   => $financialInfo,
                'essay'            => $essay,
                'guardian_info'    => $guardianInfo,
                'declaration_info' => $declarationInfo,
                'documents'        => $documentPaths,
            ]
        );

        return response()->json(['message' => 'Draft saved successfully', 'application' => $application]);
    }

    /**
     * Submit the final application.
     */
    public function submit(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Application submission attempt', [
            'user_id' => $request->user()->id,
        ]);

        // Get existing draft for previously uploaded documents
        $existingDraft = Application::where('user_id', $request->user()->id)
            ->where('status', 'draft')
            ->first();

        $existingDocuments = $existingDraft ? ($existingDraft->documents ?? []) : [];

        // Build validation rules
        $validationRules = [
            'personal_info'                     => 'required|array',
            'personal_info.surname'             => 'required|string|max:255',
            'personal_info.other_names'         => 'required|string|max:255',
            'personal_info.date_of_birth'       => 'required|date',
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
        ];

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

        $application = Application::updateOrCreate(
            ['user_id' => $request->user()->id, 'status' => 'draft'],
            [
                'personal_info'    => $request->input('personal_info', []),
                'disability_info'  => $request->input('disability_info', []),
                'dependants_info'  => $request->input('dependants_info', []),
                'financial_info'   => $request->input('financial_info', []),
                'guardian_info'    => $request->input('guardian_info', []),
                'declaration_info' => $request->input('declaration_info', []),
                'essay'            => $request->input('essay', []),
                'documents'        => $documentPaths,
                'status'           => 'submitted',
            ]
        );

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
