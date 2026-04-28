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
        $payload = $this->preparePayload($request);

        $application = Application::updateOrCreate(
            ['user_id' => $request->user()->id, 'status' => 'draft'],
            [
                'personal_info' => $payload['personal_info'],
                'financial_info' => $payload['financial_info'],
                'guardian_info' => $payload['guardian_info'],
                'essay' => $payload['essay'],
                'documents' => $payload['documents'] ?? [],
            ]
        );

        return response()->json(['message' => 'Draft saved successfully', 'application' => $application]);
    }

    /**
     * Submit the final application.
     */
    public function submit(Request $request)
    {
        // Log the request for debugging
        \Illuminate\Support\Facades\Log::info('Application submission attempt', [
            'user_id' => $request->user()->id,
            'has_documents' => $request->has('documents'),
            'all_keys' => array_keys($request->all()),
            'documents_keys' => $request->has('documents') ? array_keys($request->input('documents', [])) : [],
        ]);

        $request->validate([
            'personal_info' => 'required|array',
            'personal_info.first_name' => 'required|string|max:255',
            'personal_info.last_name' => 'required|string|max:255',
            'personal_info.gender' => 'required|string|max:100',
            'personal_info.has_disability' => 'required|string|in:yes,no,prefer_not_to_answer',
            'personal_info.disability_details' => 'required_if:personal_info.has_disability,yes|nullable|string|max:500',
            'personal_info.refugee_or_displaced' => 'required|string|in:yes,no,prefer_not_to_answer',
            'personal_info.refugee_details' => 'required_if:personal_info.refugee_or_displaced,yes|nullable|string|max:500',
            'personal_info.residence_area' => 'required|string|in:rural,urban',
            'personal_info.university' => 'required|string|max:255',
            'personal_info.program_of_study' => 'required|string|max:255',
            'personal_info.cgpa' => 'nullable|numeric|between:0,5',
            'personal_info.high_school' => 'required|string|max:255',
            'financial_info' => 'required|array',
            'financial_info.household_income' => 'required|numeric|min:0',
            'financial_info.number_of_dependents' => 'required|integer|min:0',
            'financial_info.estimated_tuition' => 'required|numeric|min:0',
            'financial_info.estimated_living_expenses' => 'required|numeric|min:0',
            'financial_info.income_sources' => 'required|string',
            'financial_info.funding_gap' => 'required|numeric|min:0',
            'guardian_info' => 'required|array',
            'guardian_info.guardian_name' => 'required|string|max:255',
            'guardian_info.guardian_phone' => 'required|string|max:30',
            'guardian_info.guardian_relation' => 'required|string|max:100',
            'essay' => 'required|array',
            'essay.personal_statement' => 'required|string|min:100',
            'essay.commitment' => 'required|string|min:100',
            'documents' => 'required|array',
            'documents.academic_documents' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.national_id' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.admission_form' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.provisional_results' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [], [
            // Custom attribute names for better error messages
            'personal_info.first_name' => 'first name',
            'personal_info.last_name' => 'last name',
            'personal_info.gender' => 'gender',
            'personal_info.has_disability' => 'disability status',
            'personal_info.disability_details' => 'disability details',
            'personal_info.refugee_or_displaced' => 'refugee/displaced status',
            'personal_info.refugee_details' => 'refugee/displaced details',
            'personal_info.residence_area' => 'residence area',
            'personal_info.university' => 'university',
            'personal_info.program_of_study' => 'program of study',
            'personal_info.cgpa' => 'CGPA',
            'personal_info.high_school' => 'high school',
            'financial_info.household_income' => 'household income',
            'financial_info.number_of_dependents' => 'number of dependents',
            'financial_info.estimated_tuition' => 'estimated tuition',
            'financial_info.estimated_living_expenses' => 'estimated living expenses',
            'financial_info.income_sources' => 'income sources',
            'financial_info.funding_gap' => 'funding gap',
            'guardian_info.guardian_name' => 'guardian name',
            'guardian_info.guardian_phone' => 'guardian phone',
            'guardian_info.guardian_relation' => 'guardian relation',
            'essay.personal_statement' => 'personal statement',
            'essay.commitment' => 'teaching commitment',
            'documents.academic_documents' => 'academic documents',
            'documents.national_id' => 'national ID',
            'documents.admission_form' => 'admission form',
            'documents.provisional_results' => 'provisional results',
        ]);

        $payload = $this->preparePayload($request);
        
        // Generate applicant name for file naming
        $firstName = strtolower(str_replace(' ', '_', $payload['personal_info']['first_name'] ?? 'applicant'));
        $lastName = strtolower(str_replace(' ', '_', $payload['personal_info']['last_name'] ?? ''));
        $applicantName = $firstName . ($lastName ? '_' . $lastName : '');
        
        // Handle document uploads with custom naming
        $documentPaths = [];
        
        if ($request->hasFile('documents.academic_documents')) {
            $file = $request->file('documents.academic_documents');
            $extension = $file->getClientOriginalExtension();
            $filename = $applicantName . '_academic_documents.' . $extension;
            $documentPaths['academic_documents'] = $file->storeAs('applications/documents', $filename, 'public');
        }
        
        if ($request->hasFile('documents.national_id')) {
            $file = $request->file('documents.national_id');
            $extension = $file->getClientOriginalExtension();
            $filename = $applicantName . '_national_id.' . $extension;
            $documentPaths['national_id'] = $file->storeAs('applications/documents', $filename, 'public');
        }
        
        if ($request->hasFile('documents.admission_form')) {
            $file = $request->file('documents.admission_form');
            $extension = $file->getClientOriginalExtension();
            $filename = $applicantName . '_admission_form.' . $extension;
            $documentPaths['admission_form'] = $file->storeAs('applications/documents', $filename, 'public');
        }
        
        if ($request->hasFile('documents.provisional_results')) {
            $file = $request->file('documents.provisional_results');
            $extension = $file->getClientOriginalExtension();
            $filename = $applicantName . '_provisional_results.' . $extension;
            $documentPaths['provisional_results'] = $file->storeAs('applications/documents', $filename, 'public');
        }

        $application = Application::updateOrCreate(
            ['user_id' => $request->user()->id, 'status' => 'draft'],
            [
                'personal_info' => $payload['personal_info'],
                'financial_info' => $payload['financial_info'],
                'guardian_info' => $payload['guardian_info'],
                'essay' => $payload['essay'],
                'documents' => $documentPaths,
                'status' => 'submitted',
            ]
        );

        $scoringService = new \App\Services\ScoringService();
        $scoringService->score($application);

        \Illuminate\Support\Facades\Log::info('Application submitted successfully', [
            'application_id' => $application->id,
            'status' => $application->status,
            'documents_uploaded' => count($documentPaths),
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($request->user())->send(new \App\Mail\ApplicationReceived($application));
        } catch (\Exception $e) {
            // Log email error but don't fail the submission
            \Illuminate\Support\Facades\Log::error('Failed to send application email: ' . $e->getMessage());
        }

        return redirect()->route('portal')->with('success', 'Application submitted successfully.');
    }

    /**
     * Normalize section payload for scoring and persistence.
     *
     * @return array<string, array>
     */
    private function preparePayload(Request $request): array
    {
        $personalInfo = $request->input('personal_info', []);
        $financialInfo = $request->input('financial_info', []);

        if (! array_key_exists('gpa', $personalInfo) && array_key_exists('cgpa', $personalInfo)) {
            $personalInfo['gpa'] = $personalInfo['cgpa'];
        }

        if (! array_key_exists('funding_gap', $financialInfo)) {
            $totalExpenses = (float) ($financialInfo['estimated_tuition'] ?? 0)
                + (float) ($financialInfo['estimated_living_expenses'] ?? 0)
                + (float) ($financialInfo['other_expenses'] ?? 0);

            $availableFunding = (float) ($financialInfo['household_income'] ?? 0)
                + (float) ($financialInfo['existing_support'] ?? 0);

            $financialInfo['funding_gap'] = max(0, $totalExpenses - $availableFunding);
        }

        return [
            'personal_info' => $personalInfo,
            'financial_info' => $financialInfo,
            'guardian_info' => $request->input('guardian_info', []),
            'essay' => $request->input('essay', []),
        ];
    }
}
