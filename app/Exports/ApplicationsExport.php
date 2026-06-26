<?php

namespace App\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ApplicationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection(): Collection
    {
        return Application::with('user')->get();
    }

    public function headings(): array
    {
        return [
            // Meta
            'Application ID', 'Status', 'Submitted On', 'Last Updated',
            // Account
            'Account Name', 'Account Email',
            // Personal
            'Surname', 'Other Names', 'Date of Birth', 'NIN',
            'Telephone', 'Personal Email', 'Marital Status',
            'Ugandan National', 'Non-Ugandan Explanation',
            'Has Disability', 'Disability Details',
            // Next of Kin
            'Next of Kin 1 Name', 'Next of Kin 1 Relationship', 'Next of Kin 1 Telephone',
            'Next of Kin 2 Name', 'Next of Kin 2 Relationship', 'Next of Kin 2 Telephone',
            // Place of Birth
            'Birth Village/Parish', 'Birth District', 'Birth Region', 'Birth Country',
            // Place of Origin
            'Origin Village/Parish', 'Origin District', 'Origin Region', 'Origin Country',
            // Place of Residence
            'Residence Village/Parish', 'Residence District', 'Residence Region', 'Residence Country',
            // Academic
            'Academic Programme', 'Institution', 'Teaching Subject 1', 'Teaching Subject 2', 'Admission Number',
            // Schools Attended
            'Primary School Name', 'Primary School District', 'Primary School Dates', 'Primary School Responsible',
            "O'Level School Name", "O'Level School District", "O'Level School Dates", "O'Level School Responsible",
            "A'Level School Name", "A'Level School District", "A'Level School Dates", "A'Level School Responsible",
            'University Name', 'University District', 'University Dates', 'University Responsible',
            // Mode of Admission
            "A'Level Institution", "A'Level Year", "A'Level Index", "A'Level Points",
            'Diploma Institution', 'Diploma Year', 'Diploma Index', 'Diploma CGPA',
            'HEAC Institution', 'HEAC Year', 'HEAC Index', 'HEAC Points',
            'Mature Entry Institution', 'Mature Entry Year', 'Mature Entry Index', 'Mature Entry Points',
            // Disability
            'Functionality Level', 'Difficulty Walking', 'Difficulty Seeing', 'Difficulty Hearing',
            'Difficulty Communicating', 'Difficulty Picking Objects', 'Difficulty Self-Care',
            'Difficulty Controlling Emotions', 'Assistive Support Needed',
            // Dependants
            'Spouse Surname', 'Spouse Other Names', 'Spouse Education Level', 'Spouse Occupation',
            'Marriage/Studies Balance Plan', 'Number of Children', 'Age of Oldest Child',
            'Age of Youngest Child', 'Childcare Plan', 'Spouse Support', 'Non-Financial Support Needed',
            // Financial
            'Household Income (UGX/year)', 'Number of Financial Dependents', 'Primary Income Source', 'Other Financial Support',
            // Essay
            'Motivation Statement',
            // Guardian
            'Guardian Surname', 'Guardian Other Names', 'Guardian Telephone', 'Guardian Relation',
            'Guardian Occupation', 'Guardian District', 'Guardian Region', 'Guardian Address',
            // Declaration
            'Criminal Offence?', 'Criminal Details',
            // How heard
            'How They Heard About the Scholarship', 'Other Source (Specified)',
            // Scoring
            'Score – Financial Need', 'Score – Academic Merit', 'Score – Demographics',
            'Score – Commitment', 'Score – Essay Quality', 'Total Score',
        ];
    }

    public function map($app): array
    {
        $p  = $app->personal_info    ?? [];
        $di = $app->disability_info  ?? [];
        $de = $app->dependants_info  ?? [];
        $fi = $app->financial_info   ?? [];
        $g  = $app->guardian_info    ?? [];
        $e  = $app->essay            ?? [];
        $d  = $app->declaration_info ?? [];
        $sc = $app->scoring_breakdown ?? [];
        $nok = $p['next_of_kin'] ?? [[], []];

        $bool = fn ($v) => $v ? 'Yes' : 'No';

        return [
            // Meta
            $app->id,
            ucwords(str_replace('_', ' ', $app->status ?? '')),
            $app->created_at?->format('Y-m-d H:i:s'),
            $app->updated_at?->format('Y-m-d H:i:s'),
            // Account
            $app->user?->name,
            $app->user?->email,
            // Personal
            $p['surname'] ?? '', $p['other_names'] ?? '', $p['date_of_birth'] ?? '', $p['nin'] ?? '',
            $p['phone'] ?? '', $p['email'] ?? '', $p['marital_status'] ?? '',
            isset($p['is_ugandan']) ? ($p['is_ugandan'] === 'yes' ? 'Yes' : 'No') : '',
            $p['non_ugandan_explanation'] ?? '',
            isset($p['has_disability']) ? ($p['has_disability'] === 'yes' ? 'Yes' : 'No') : '',
            $p['disability_specify'] ?? '',
            // Next of Kin
            $nok[0]['name'] ?? '', $nok[0]['relationship'] ?? '', $nok[0]['telephone'] ?? '',
            $nok[1]['name'] ?? '', $nok[1]['relationship'] ?? '', $nok[1]['telephone'] ?? '',
            // Place of Birth
            $p['birth_village'] ?? '', $p['birth_district'] ?? '', $p['birth_region'] ?? '', $p['birth_country'] ?? '',
            // Place of Origin
            $p['origin_village'] ?? '', $p['origin_district'] ?? '', $p['origin_region'] ?? '', $p['origin_country'] ?? '',
            // Place of Residence
            $p['residence_village'] ?? '', $p['residence_district'] ?? '', $p['residence_region'] ?? '', $p['residence_country'] ?? '',
            // Academic
            $p['academic_programme'] ?? '', $p['institution'] ?? '',
            $p['teaching_subjects_1'] ?? '', $p['teaching_subjects_2'] ?? '', $p['student_admission_number'] ?? '',
            // Schools Attended
            $p['primary_school_name'] ?? '', $p['primary_school_district'] ?? '', $p['primary_school_dates'] ?? '', $p['primary_school_responsible'] ?? '',
            $p['olevel_school_name'] ?? '', $p['olevel_school_district'] ?? '', $p['olevel_school_dates'] ?? '', $p['olevel_school_responsible'] ?? '',
            $p['alevel_school_name'] ?? '', $p['alevel_school_district'] ?? '', $p['alevel_school_dates'] ?? '', $p['alevel_school_responsible'] ?? '',
            $p['university_name'] ?? '', $p['university_district'] ?? '', $p['university_dates'] ?? '', $p['university_responsible'] ?? '',
            // Mode of Admission
            $p['alevel_school_exam'] ?? '', $p['alevel_year'] ?? '', $p['alevel_index'] ?? '', $p['alevel_points'] ?? '',
            $p['diploma_school'] ?? '', $p['diploma_year'] ?? '', $p['diploma_index'] ?? '', $p['diploma_cgpa'] ?? '',
            $p['heac_school'] ?? '', $p['heac_year'] ?? '', $p['heac_index'] ?? '', $p['heac_points'] ?? '',
            $p['mature_school'] ?? '', $p['mature_year'] ?? '', $p['mature_index'] ?? '', $p['mature_points'] ?? '',
            // Disability
            $di['functionality_level'] ?? '',
            $bool($di['difficulty_walking'] ?? false),
            $bool($di['difficulty_seeing'] ?? false),
            $bool($di['difficulty_hearing'] ?? false),
            $bool($di['difficulty_communicating'] ?? false),
            $bool($di['difficulty_picking'] ?? false),
            $bool($di['difficulty_self_care'] ?? false),
            $bool($di['difficulty_emotions'] ?? false),
            $di['assistive_support'] ?? '',
            // Dependants
            $de['spouse_surname'] ?? '', $de['spouse_other_names'] ?? '',
            $de['spouse_education_level'] ?? '', $de['spouse_occupation'] ?? '',
            $de['marriage_balance_plan'] ?? '',
            $de['num_children'] ?? '', $de['oldest_child_age'] ?? '', $de['youngest_child_age'] ?? '',
            $de['childcare_plan'] ?? '', $de['spouse_support'] ?? '', $de['non_financial_support_needed'] ?? '',
            // Financial
            $fi['household_income'] ?? '', $fi['number_of_dependents'] ?? '',
            $fi['income_source'] ?? '', $fi['other_financial_support'] ?? '',
            // Essay
            strip_tags($e['motivation'] ?? ''),
            // Guardian
            $g['guardian_surname'] ?? '', $g['guardian_other_names'] ?? '',
            $g['guardian_telephone'] ?? '', $g['guardian_relation'] ?? '',
            $g['guardian_occupation'] ?? '', $g['guardian_district'] ?? '',
            $g['guardian_region'] ?? '', $g['guardian_address'] ?? '',
            // Declaration
            isset($d['criminal_offence']) ? ($d['criminal_offence'] === 'yes' ? 'Yes' : 'No') : '',
            $d['criminal_details'] ?? '',
            // How heard
            $p['hearing_source'] ?? '',
            $p['hearing_source_other'] ?? '',
            // Scoring
            $sc['financial_need'] ?? 0, $sc['academic_merit'] ?? 0,
            $sc['demographics'] ?? 0, $sc['commitment'] ?? 0,
            $sc['essay_quality'] ?? 0, $sc['total'] ?? 0,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
