<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $personalInfo['surname'] ?? '' }} {{ $personalInfo['other_names'] ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; padding: 20px; line-height: 1.4; }
        h1 { font-size: 14px; margin-bottom: 5px; color: #1d4ed8; }
        h2 { font-size: 11px; margin: 15px 0 8px 0; padding-bottom: 3px; border-bottom: 1px solid #ddd; color: #374151; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        td { padding: 4px; border-bottom: 1px dotted #e5e7eb; vertical-align: top; }
        td:first-child { font-weight: bold; width: 35%; color: #4b5563; }
        .meta { font-size: 8px; color: #6b7280; margin-bottom: 15px; }
        .footer { margin-top: 20px; text-align: center; font-size: 7px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>

<h1>{{ $personalInfo['surname'] ?? 'N/A' }}, {{ $personalInfo['other_names'] ?? 'N/A' }}</h1>
<div class="meta">
    Application ID: {{ $application->id }} | 
    Status: {{ ucwords(str_replace('_', ' ', $application->status)) }} | 
    Submitted: {{ $application->created_at ? $application->created_at->format('d M Y') : 'N/A' }}
</div>

<h2>Personal Information</h2>
<table>
    <tr><td>Date of Birth</td><td>{{ $personalInfo['date_of_birth'] ?? 'Not provided' }}</td></tr>
    <tr><td>NIN</td><td>{{ $personalInfo['nin'] ?? 'Not provided' }}</td></tr>
    <tr><td>Gender</td><td>{{ ucfirst($personalInfo['gender'] ?? 'Not specified') }}</td></tr>
    <tr><td>Nationality</td><td>
        @if(($personalInfo['is_ugandan'] ?? null) === 'yes')
            Ugandan
        @elseif(($personalInfo['is_ugandan'] ?? null) === 'no')
            Non-Ugandan: {{ $personalInfo['non_ugandan_explanation'] ?? 'Not specified' }}
        @else
            Not specified
        @endif
    </td></tr>
    <tr><td>Marital Status</td><td>{{ ucfirst($personalInfo['marital_status'] ?? 'Not provided') }}</td></tr>
    <tr><td>Telephone</td><td>{{ $personalInfo['phone'] ?? 'Not provided' }}</td></tr>
    <tr><td>Email</td><td>{{ $personalInfo['email'] ?? ($application->user->email ?? 'Not provided') }}</td></tr>
    <tr><td>District</td><td>{{ $personalInfo['district'] ?? 'Not provided' }}</td></tr>
    <tr><td>Sub-county</td><td>{{ $personalInfo['subcounty'] ?? 'Not provided' }}</td></tr>
    <tr><td>Village</td><td>{{ $personalInfo['village'] ?? 'Not provided' }}</td></tr>
</table>

<h2>Academic Information</h2>
<table>
    <tr><td>Institution</td><td>{{ $personalInfo['institution'] ?? 'Not provided' }}</td></tr>
    <tr><td>Programme</td><td>{{ $personalInfo['course'] ?? ($personalInfo['academic_programme'] ?? 'Not provided') }}</td></tr>
    <tr><td>Year of Study</td><td>{{ $personalInfo['year'] ?? 'Not provided' }}</td></tr>
    <tr><td>Entry Level</td><td>{{ $personalInfo['entry_level'] ?? 'Not provided' }}</td></tr>
    <tr><td>Teaching Subjects</td><td>
        @php
            $subjects = [];
            if (!empty($personalInfo['subject_1'] ?? null)) $subjects[] = $personalInfo['subject_1'];
            if (!empty($personalInfo['subject_2'] ?? null)) $subjects[] = $personalInfo['subject_2'];
            if (!empty($personalInfo['subject_3'] ?? null)) $subjects[] = $personalInfo['subject_3'];
            if (!empty($personalInfo['teaching_subjects_1'] ?? null)) $subjects[] = $personalInfo['teaching_subjects_1'];
            if (!empty($personalInfo['teaching_subjects_2'] ?? null)) $subjects[] = $personalInfo['teaching_subjects_2'];
        @endphp
        {{ !empty($subjects) ? implode(', ', array_unique($subjects)) : 'Not provided' }}
    </td></tr>
    <tr><td>CGPA</td><td>{{ $personalInfo['cgpa'] ?? 'Not provided' }}</td></tr>
</table>

@if(!empty($guardianInfo))
<h2>Guardian Information</h2>
<table>
    <tr><td>Name</td><td>{{ $guardianInfo['name'] ?? 'Not provided' }}</td></tr>
    <tr><td>Relationship</td><td>{{ $guardianInfo['relationship'] ?? 'Not provided' }}</td></tr>
    <tr><td>Phone</td><td>{{ $guardianInfo['phone'] ?? 'Not provided' }}</td></tr>
    <tr><td>Email</td><td>{{ $guardianInfo['email'] ?? 'Not provided' }}</td></tr>
</table>
@endif

@if(!empty($financialInfo))
<h2>Financial Information</h2>
<table>
    <tr><td>Income Source</td><td>{{ $financialInfo['income_source'] ?? 'Not provided' }}</td></tr>
    <tr><td>Income Range</td><td>{{ $financialInfo['income_range'] ?? 'Not provided' }}</td></tr>
    <tr><td>Challenges</td><td>{{ $financialInfo['challenges'] ?? 'Not provided' }}</td></tr>
</table>
@endif

@if(!empty($disabilityInfo) && ($disabilityInfo['has_disability'] ?? null) === 'yes')
<h2>Disability Information</h2>
<table>
    <tr><td>Type</td><td>{{ $disabilityInfo['disability_type'] ?? 'Not specified' }}</td></tr>
    <tr><td>Support Needed</td><td>{{ $disabilityInfo['support_needed'] ?? 'Not specified' }}</td></tr>
</table>
@endif

@if(!empty($dependantsInfo['dependants']))
<h2>Dependants</h2>
<table>
    <tr><td>Number of Dependants</td><td>{{ count($dependantsInfo['dependants']) }}</td></tr>
</table>
@endif

@if(!empty($essay['personal_statement']) || !empty($essay['why_teaching']))
<h2>Essays</h2>
@if(!empty($essay['personal_statement']))
<p style="margin: 5px 0; font-size: 9px;"><strong>Personal Statement:</strong><br>{{ Str::limit($essay['personal_statement'], 300) }}</p>
@endif
@if(!empty($essay['why_teaching']))
<p style="margin: 5px 0; font-size: 9px;"><strong>Why Teaching:</strong><br>{{ Str::limit($essay['why_teaching'], 300) }}</p>
@endif
@endif

<h2>Document Status</h2>
<table>
    @foreach(['exam_results' => 'Exam Results', 'national_id' => 'National ID', 'birth_certificate' => 'Birth Certificate', 'admission_letter' => 'Admission Letter', 'recommendation_lc1' => 'LC1 Recommendation', 'recommendation_school' => 'School Recommendation'] as $field => $label)
    <tr><td>{{ $label }}</td><td>{{ !empty($documents[$field]) ? '✓ Uploaded' : '✗ Not uploaded' }}</td></tr>
    @endforeach
</table>

<div class="footer">
    LIT Scholarship Management System | {{ now()->format('d M Y, H:i') }}
</div>

</body>
</html>