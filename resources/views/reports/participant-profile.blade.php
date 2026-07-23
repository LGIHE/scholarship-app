<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Participant Profile - {{ $personalInfo['surname'] ?? '' }} {{ $personalInfo['other_names'] ?? '' }}</title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.4;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #1d4ed8;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #1d4ed8;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            color: #374151;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 10px;
            color: #6b7280;
        }

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-header {
            background: #1d4ed8;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .field-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .field-row {
            display: table-row;
        }

        .field-label {
            display: table-cell;
            font-weight: bold;
            color: #374151;
            padding: 4px 8px 4px 0;
            width: 35%;
            vertical-align: top;
        }

        .field-value {
            display: table-cell;
            padding: 4px 0;
            border-bottom: 1px dotted #e5e7eb;
            vertical-align: top;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-submitted   { background: #dbeafe; color: #1d4ed8; }
        .status-approved    { background: #dcfce7; color: #15803d; }
        .status-rejected    { background: #fee2e2; color: #b91c1c; }
        .status-under_review { background: #fef9c3; color: #a16207; }
        .status-draft       { background: #f3f4f6; color: #374151; }

        .document-status {
            font-size: 9px;
            color: #059669;
            font-weight: bold;
        }

        .document-missing {
            color: #dc2626;
        }

        .essay-content {
            background: #f9fafb;
            border-left: 4px solid #1d4ed8;
            padding: 12px;
            margin: 8px 0;
            font-style: italic;
            font-size: 10px;
            line-height: 1.5;
        }

        .dependant-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px;
            margin: 5px 0;
            font-size: 10px;
        }

        .meta-info {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 9px;
            color: #6b7280;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        .no-data {
            color: #9ca3af;
            font-style: italic;
        }

        .highlight {
            background: #fef3c7;
            padding: 2px 4px;
            border-radius: 2px;
        }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>LIT Scholarship Programme</h1>
    <h2>Participant Application Profile</h2>
    <p>Generated on {{ now()->format('d M Y, H:i') }}</p>
</div>

{{-- Application Meta Information --}}
<div class="meta-info">
    <strong>Application ID:</strong> {{ $application->id }} &nbsp;&bull;&nbsp;
    <strong>Status:</strong> 
    <span class="status-badge status-{{ strtolower(str_replace(' ', '_', $application->status)) }}">
        {{ ucwords(str_replace('_', ' ', $application->status)) }}
    </span> &nbsp;&bull;&nbsp;
    <strong>Submitted:</strong> {{ $application->created_at ? $application->created_at->format('d M Y, H:i') : 'Not submitted' }} &nbsp;&bull;&nbsp;
    <strong>Cohort:</strong> {{ $application->cohort?->name ?? 'N/A' }}
</div>

{{-- Personal Information --}}
<div class="section">
    <div class="section-header">Personal Information</div>
    
    <div class="field-grid">
        <div class="field-row">
            <div class="field-label">Full Name:</div>
            <div class="field-value">
                <strong>{{ $personalInfo['surname'] ?? 'N/A' }}, {{ $personalInfo['other_names'] ?? 'N/A' }}</strong>
            </div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Date of Birth:</div>
            <div class="field-value">{{ $personalInfo['date_of_birth'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">National ID (NIN):</div>
            <div class="field-value">{{ $personalInfo['nin'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Gender:</div>
            <div class="field-value">{{ ucfirst($personalInfo['gender'] ?? 'Not specified') }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Nationality:</div>
            <div class="field-value">
                @if(($personalInfo['is_ugandan'] ?? null) === 'yes')
                    <span class="highlight">Ugandan</span>
                @elseif(($personalInfo['is_ugandan'] ?? null) === 'no')
                    Non-Ugandan: {{ $personalInfo['non_ugandan_explanation'] ?? 'Not specified' }}
                @else
                    Not specified
                @endif
            </div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Marital Status:</div>
            <div class="field-value">{{ ucfirst($personalInfo['marital_status'] ?? 'Not provided') }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Telephone:</div>
            <div class="field-value">{{ $personalInfo['phone'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Personal Email:</div>
            <div class="field-value">{{ $personalInfo['email'] ?? ($application->user->email ?? 'Not provided') }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">District:</div>
            <div class="field-value">{{ $personalInfo['district'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Sub-county:</div>
            <div class="field-value">{{ $personalInfo['subcounty'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Village:</div>
            <div class="field-value">{{ $personalInfo['village'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">How did you hear about us:</div>
            <div class="field-value">
                {{ isset($personalInfo['hearing_source']) && $personalInfo['hearing_source'] ? ucwords(str_replace('_', ' ', $personalInfo['hearing_source'])) : 'Not provided' }}
                @if(isset($personalInfo['hearing_source']) && $personalInfo['hearing_source'] === 'other' && !empty($personalInfo['hearing_source_other']))
                    <br><em>({{ $personalInfo['hearing_source_other'] }})</em>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Academic Information --}}
<div class="section">
    <div class="section-header">Academic Information</div>
    
    <div class="field-grid">
        <div class="field-row">
            <div class="field-label">Institution/University:</div>
            <div class="field-value"><strong>{{ $personalInfo['institution'] ?? 'Not provided' }}</strong></div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Academic Programme:</div>
            <div class="field-value">{{ $personalInfo['course'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Year of Study:</div>
            <div class="field-value">{{ $personalInfo['year'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Entry Level:</div>
            <div class="field-value">{{ $personalInfo['entry_level'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Teaching Subjects:</div>
            <div class="field-value">
                @if(!empty($personalInfo['subject_1'] ?? null))
                    1. {{ $personalInfo['subject_1'] }}<br>
                @endif
                @if(!empty($personalInfo['subject_2'] ?? null))
                    2. {{ $personalInfo['subject_2'] }}<br>
                @endif
                @if(!empty($personalInfo['subject_3'] ?? null))
                    3. {{ $personalInfo['subject_3'] }}
                @endif
                @if(empty($personalInfo['subject_1'] ?? null) && empty($personalInfo['subject_2'] ?? null) && empty($personalInfo['subject_3'] ?? null))
                    Not provided
                @endif
            </div>
        </div>
        
        <div class="field-row">
            <div class="field-label">CGPA:</div>
            <div class="field-value">{{ $personalInfo['cgpa'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Previous School:</div>
            <div class="field-value">{{ $personalInfo['previous_school'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Graduation Year:</div>
            <div class="field-value">{{ $personalInfo['graduation_year'] ?? 'Not provided' }}</div>
        </div>
    </div>
</div>

{{-- Guardian Information --}}
@if(!empty($guardianInfo))
<div class="section">
    <div class="section-header">Guardian Information</div>
    
    <div class="field-grid">
        <div class="field-row">
            <div class="field-label">Guardian Name:</div>
            <div class="field-value">{{ $guardianInfo['name'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Relationship:</div>
            <div class="field-value">{{ $guardianInfo['relationship'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Guardian Phone:</div>
            <div class="field-value">{{ $guardianInfo['phone'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Guardian Email:</div>
            <div class="field-value">{{ $guardianInfo['email'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Guardian Address:</div>
            <div class="field-value">{{ $guardianInfo['address'] ?? 'Not provided' }}</div>
        </div>
    </div>
</div>
@endif

{{-- Financial Information --}}
@if(!empty($financialInfo))
<div class="section">
    <div class="section-header">Financial Information</div>
    
    <div class="field-grid">
        <div class="field-row">
            <div class="field-label">Family Income Source:</div>
            <div class="field-value">{{ $financialInfo['income_source'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Monthly Income Range:</div>
            <div class="field-value">{{ $financialInfo['income_range'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Financial Challenges:</div>
            <div class="field-value">{{ $financialInfo['challenges'] ?? 'Not provided' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Other Sources of Support:</div>
            <div class="field-value">{{ $financialInfo['other_support'] ?? 'Not provided' }}</div>
        </div>
    </div>
</div>
@endif

{{-- Disability Information --}}
@if(!empty($disabilityInfo))
<div class="section">
    <div class="section-header">Disability Information</div>
    
    <div class="field-grid">
        <div class="field-row">
            <div class="field-label">Has Disability:</div>
            <div class="field-value">{{ ($disabilityInfo['has_disability'] ?? null) === 'yes' ? 'Yes' : 'No' }}</div>
        </div>
        
        @if(($disabilityInfo['has_disability'] ?? null) === 'yes')
        <div class="field-row">
            <div class="field-label">Type of Disability:</div>
            <div class="field-value">{{ $disabilityInfo['disability_type'] ?? 'Not specified' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Support Needed:</div>
            <div class="field-value">{{ $disabilityInfo['support_needed'] ?? 'Not specified' }}</div>
        </div>
        @endif
    </div>
</div>
@endif

{{-- Dependants Information --}}
@if(!empty($dependantsInfo) && !empty($dependantsInfo['dependants']))
<div class="section">
    <div class="section-header">Dependants Information</div>
    
    <div class="field-grid">
        <div class="field-row">
            <div class="field-label">Number of Dependants:</div>
            <div class="field-value">{{ count($dependantsInfo['dependants']) }}</div>
        </div>
    </div>
    
    @foreach($dependantsInfo['dependants'] as $index => $dependant)
    <div class="dependant-item">
        <strong>Dependant {{ $index + 1 }}:</strong>
        {{ $dependant['name'] ?? 'N/A' }} 
        ({{ $dependant['relationship'] ?? 'N/A' }}, 
        Age: {{ $dependant['age'] ?? 'N/A' }})
    </div>
    @endforeach
</div>
@endif

{{-- Essay/Personal Statement --}}
@if(!empty($essay))
<div class="section page-break">
    <div class="section-header">Personal Statement / Essay</div>
    
    @if(!empty($essay['personal_statement']))
    <h4 style="margin: 10px 0 5px 0; color: #374151;">Personal Statement:</h4>
    <div class="essay-content">
        {{ $essay['personal_statement'] }}
    </div>
    @endif
    
    @if(!empty($essay['why_teaching']))
    <h4 style="margin: 10px 0 5px 0; color: #374151;">Why Teaching?</h4>
    <div class="essay-content">
        {{ $essay['why_teaching'] }}
    </div>
    @endif
    
    @if(!empty($essay['future_goals']))
    <h4 style="margin: 10px 0 5px 0; color: #374151;">Future Goals:</h4>
    <div class="essay-content">
        {{ $essay['future_goals'] }}
    </div>
    @endif
</div>
@endif

{{-- Document Upload Status --}}
<div class="section">
    <div class="section-header">Document Upload Status</div>
    
    <div class="field-grid">
        @php
            $documentLabels = [
                'exam_results' => 'Exam Results',
                'national_id' => 'National ID',
                'birth_certificate' => 'Birth Certificate', 
                'admission_letter' => 'Admission Letter',
                'recommendation_lc1' => 'LC1 Recommendation',
                'recommendation_school' => 'School Recommendation',
                'refugee_number' => 'Refugee Number'
            ];
        @endphp
        
        @foreach($documentLabels as $field => $label)
        <div class="field-row">
            <div class="field-label">{{ $label }}:</div>
            <div class="field-value">
                @if(!empty($documents[$field]))
                    <span class="document-status">✓ Uploaded</span>
                @else
                    <span class="document-status document-missing">✗ Not uploaded</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Declaration Information --}}
@if(!empty($declarationInfo))
<div class="section">
    <div class="section-header">Declaration</div>
    
    <div class="field-grid">
        <div class="field-row">
            <div class="field-label">Declaration Accepted:</div>
            <div class="field-value">{{ ($declarationInfo['accepted'] ?? null) === 'yes' ? 'Yes' : 'No' }}</div>
        </div>
        
        <div class="field-row">
            <div class="field-label">Declaration Date:</div>
            <div class="field-value">{{ $declarationInfo['date'] ?? 'Not provided' }}</div>
        </div>
    </div>
</div>
@endif

<div class="footer">
    LIT Scholarship Management System &bull; Participant Profile &bull; Generated {{ now()->format('d M Y, H:i') }} &bull; {{ now()->format('Y') }}
</div>

</body>
</html>