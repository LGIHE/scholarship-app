<?php

namespace App\Http\Controllers;

use App\Models\AcademicProgress;
use App\Models\Scholar;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AcademicProgressController extends Controller
{
    /**
     * Show the academic progress update form.
     */
    public function index(Request $request): Response
    {
        $scholar = Scholar::where('user_id', $request->user()->id)->first();
        
        if (!$scholar) {
            return Inertia::render('AcademicProgress/Index', [
                'scholar' => null,
                'progressRecords' => [],
            ]);
        }

        $progressRecords = AcademicProgress::where('scholar_id', $scholar->id)
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return Inertia::render('AcademicProgress/Index', [
            'scholar' => $scholar,
            'progressRecords' => $progressRecords,
        ]);
    }

    /**
     * Store a new academic progress record.
     */
    public function store(Request $request)
    {
        $scholar = Scholar::where('user_id', $request->user()->id)->firstOrFail();

        $validated = $request->validate([
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|string|max:20',
            'gpa' => 'required|numeric|between:0,5',
            'cgpa' => 'required|numeric|between:0,5',
            'courses_taken' => 'nullable|string|max:1000',
            'achievements' => 'nullable|string|max:1000',
            'challenges' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['scholar_id'] = $scholar->id;

        AcademicProgress::create($validated);

        return redirect()->route('academic-progress.index')
            ->with('success', 'Academic progress updated successfully.');
    }

    /**
     * Update an existing academic progress record.
     */
    public function update(Request $request, AcademicProgress $academicProgress)
    {
        $scholar = Scholar::where('user_id', $request->user()->id)->firstOrFail();

        // Ensure the progress record belongs to the authenticated scholar
        if ($academicProgress->scholar_id !== $scholar->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|string|max:20',
            'gpa' => 'required|numeric|between:0,5',
            'cgpa' => 'required|numeric|between:0,5',
            'courses_taken' => 'nullable|string|max:1000',
            'achievements' => 'nullable|string|max:1000',
            'challenges' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $academicProgress->update($validated);

        return redirect()->route('academic-progress.index')
            ->with('success', 'Academic progress updated successfully.');
    }

    /**
     * Delete an academic progress record.
     */
    public function destroy(Request $request, AcademicProgress $academicProgress)
    {
        $scholar = Scholar::where('user_id', $request->user()->id)->firstOrFail();

        // Ensure the progress record belongs to the authenticated scholar
        if ($academicProgress->scholar_id !== $scholar->id) {
            abort(403, 'Unauthorized action.');
        }

        $academicProgress->delete();

        return redirect()->route('academic-progress.index')
            ->with('success', 'Academic progress record deleted successfully.');
    }
}
