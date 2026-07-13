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
    /**
     * All available exportable columns.
     * Key   = internal identifier (used in $selectedColumns)
     * Value = column heading label
     */
    public static function availableColumns(): array
    {
        return [
            // Meta
            'application_id'            => 'Application ID',
            'status'                     => 'Status',
            'submitted_on'               => 'Submitted On',
            'last_updated'               => 'Last Updated',
            // Account
            'account_name'               => 'Account Name',
            'account_email'              => 'Account Email',
            // Personal
            'surname'                    => 'Surname',
            'other_names'                => 'Other Names',
            'date_of_birth'              => 'Date of Birth',
            'nin'                        => 'NIN',
            'telephone'                  => 'Telephone',
            'personal_email'             => 'Personal Email',
            'marital_status'             => 'Marital Status',
            'ugandan_national'           => 'Ugandan National',
            'non_ugandan_explanation'    => 'Non-Ugandan Explanation',
            'has_disability'             => 'Has Disability',
            'disability_details'         => 'Disability Details',
            // Next of Kin
            'nok1_name'                  => 'Next of Kin 1 Name',
            'nok1_relationship'          => 'Next of Kin 1 Relationship',
            'nok1_telephone'             => 'Next of Kin 1 Telephone',
            'nok2_name'                  => 'Next of Kin 2 Name',
            'nok2_relationship'          => 'Next of Kin 2 Relationship',
            'nok2_telephone'             => 'Next of Kin 2 Telephone',
            // Place of Birth
            'birth_village'              => 'Birth Village/Parish',
            'birth_district'             => 'Birth District',
            'birth_region'               => 'Birth Region',
            'birth_country'              => 'Birth Country',
            // Place of Origin
            'origin_village'             => 'Origin Village/Parish',
            'origin_district'            => 'Origin District',
            'origin_region'              => 'Origin Region',
            'origin_country'             => 'Origin Country',
            // Place of Residence
            'residence_village'          => 'Residence Village/Parish',
            'residence_district'         => 'Residence District',
            'residence_region'           => 'Residence Region',
            'residence_country'          => 'Residence Country',
            // Academic
            'academic_programme'         => 'Academic Programme',
            'institution'                => 'Institution',
            'teaching_subject_1'         => 'Teaching Subject 1',
            'teaching_subject_2'         => 'Teaching Subject 2',
            'admission_number'           => 'Admission Number',
            // Schools Attended
            'primary_school_name'        => 'Primary School Name',
            'primary_school_district'    => 'Primary School District',
            'primary_school_dates'       => 'Primary School Dates',
            'primary_school_responsible' => 'Primary School Responsible',
            'olevel_school_name'         => "O'Level School Name",
            'olevel_school_district'     => "O'Level School District",
            'olevel_school_dates'        => "O'Level School Dates",
            'olevel_school_responsible'  => "O'Level School Responsible",
            'alevel_school_name'         => "A'Level School Name",
            'alevel_school_district'     => "A'Level School District",
            'alevel_school_dates'        => "A'Level School Dates",
            'alevel_school_responsible'  => "A'Level School Responsible",
            'university_name'            => 'University Name',
            'university_district'        => 'University District',
            'university_dates'           => 'University Dates',
            'university_responsible'     => 'University Responsible',
            // Mode of Admission
            'alevel_institution'         => "A'Level Institution",
            'alevel_year'                => "A'Level Year",
            'alevel_index'               => "A'Level Index",
            'alevel_points'              => "A'Level Points",
            'diploma_institution'        => 'Diploma Institution',
            'diploma_year'               => 'Diploma Year',
            'diploma_index'              => 'Diploma Index',
            'diploma_cgpa'               => 'Diploma CGPA',
            'heac_institution'           => 'HEAC Institution',
            'heac_year'                  => 'HEAC Year',
            'heac_index'                 => 'HEAC Index',
            'heac_points'                => 'HEAC Points',
            'mature_institution'         => 'Mature Entry Institution',
            'mature_year'                => 'Mature Entry Year',
            'mature_index'               => 'Mature Entry Index',
            'mature_points'              => 'Mature Entry Points',
            // Disability Info
            'functionality_level'        => 'Functionality Level',
            'difficulty_walking'         => 'Difficulty Walking',
            'difficulty_seeing'          => 'Difficulty Seeing',
            'difficulty_hearing'         => 'Difficulty Hearing',
            'difficulty_communicating'   => 'Difficulty Communicating',
            'difficulty_picking'         => 'Difficulty Picking Objects',
            'difficulty_self_care'       => 'Difficulty Self-Care',
            'difficulty_emotions'        => 'Difficulty Controlling Emotions',
            'assistive_support'          => 'Assistive Support Needed',
            // Dependants
            'spouse_surname'             => 'Spouse Surname',
            'spouse_other_names'         => 'Spouse Other Names',
            'spouse_education_level'     => 'Spouse Education Level',
            'spouse_occupation'          => 'Spouse Occupation',
            'marriage_balance_plan'      => 'Marriage/Studies Balance Plan',
            'num_children'               => 'Number of Children',
            'oldest_child_age'           => 'Age of Oldest Child',
            'youngest_child_age'         => 'Age of Youngest Child',
            'childcare_plan'             => 'Childcare Plan',
            'spouse_support'             => 'Spouse Support',
            'non_financial_support'      => 'Non-Financial Support Needed',
            // Financial
            'household_income'           => 'Household Income (UGX/year)',
            'financial_dependents'       => 'Number of Financial Dependents',
            'income_source'              => 'Primary Income Source',
            'other_financial_support'    => 'Other Financial Support',
            // Essay
            'motivation_statement'       => 'Motivation Statement',
            // Guardian
            'guardian_surname'           => 'Guardian Surname',
            'guardian_other_names'       => 'Guardian Other Names',
            'guardian_telephone'         => 'Guardian Telephone',
            'guardian_relation'          => 'Guardian Relation',
            'guardian_occupation'        => 'Guardian Occupation',
            'guardian_district'          => 'Guardian District',
            'guardian_region'            => 'Guardian Region',
            'guardian_address'           => 'Guardian Address',
            // Declaration
            'criminal_offence'           => 'Criminal Offence?',
            'criminal_details'           => 'Criminal Details',
            // How heard
            'hearing_source'             => 'How They Heard About the Scholarship',
            'hearing_source_other'       => 'Other Source (Specified)',
            // Scoring
            'score_financial_need'       => 'Score – Financial Need',
            'score_academic_merit'       => 'Score – Academic Merit',
            'score_demographics'         => 'Score – Demographics',
            'score_commitment'           => 'Score – Commitment',
            'score_essay_quality'        => 'Score – Essay Quality',
            'score_total'                => 'Total Score',
        ];
    }

    /** @var array<string> */
    protected array $selectedColumns;

    /**
     * @param array<string>|null $selectedColumns  Keys from availableColumns().
     *                                             Pass null to export every column.
     */
    public function __construct(?array $selectedColumns = null)
    {
        $this->selectedColumns = $selectedColumns ?? array_keys(static::availableColumns());
    }

    public function collection(): Collection
    {
        return Application::with('user')->get();
    }

    public function headings(): array
    {
        $all = static::availableColumns();

        return array_values(
            array_map(fn (string $key) => $all[$key] ?? $key, $this->selectedColumns)
        );
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

        /** All possible values, keyed by column identifier */
        $all = [
            'application_id'            => $app->id,
            'status'                     => ucwords(str_replace('_', ' ', $app->status ?? '')),
            'submitted_on'               => $app->created_at?->format('Y-m-d H:i:s'),
            'last_updated'               => $app->updated_at?->format('Y-m-d H:i:s'),
            'account_name'               => $app->user?->name,
            'account_email'              => $app->user?->email,
            'surname'                    => $p['surname'] ?? '',
            'other_names'                => $p['other_names'] ?? '',
            'date_of_birth'              => $p['date_of_birth'] ?? '',
            'nin'                        => $p['nin'] ?? '',
            'telephone'                  => $p['phone'] ?? '',
            'personal_email'             => $p['email'] ?? '',
            'marital_status'             => $p['marital_status'] ?? '',
            'ugandan_national'           => isset($p['is_ugandan']) ? ($p['is_ugandan'] === 'yes' ? 'Yes' : 'No') : '',
            'non_ugandan_explanation'    => $p['non_ugandan_explanation'] ?? '',
            'has_disability'             => isset($p['has_disability']) ? ($p['has_disability'] === 'yes' ? 'Yes' : 'No') : '',
            'disability_details'         => $p['disability_specify'] ?? '',
            'nok1_name'                  => $nok[0]['name'] ?? '',
            'nok1_relationship'          => $nok[0]['relationship'] ?? '',
            'nok1_telephone'             => $nok[0]['telephone'] ?? '',
            'nok2_name'                  => $nok[1]['name'] ?? '',
            'nok2_relationship'          => $nok[1]['relationship'] ?? '',
            'nok2_telephone'             => $nok[1]['telephone'] ?? '',
            'birth_village'              => $p['birth_village'] ?? '',
            'birth_district'             => $p['birth_district'] ?? '',
            'birth_region'               => $p['birth_region'] ?? '',
            'birth_country'              => $p['birth_country'] ?? '',
            'origin_village'             => $p['origin_village'] ?? '',
            'origin_district'            => $p['origin_district'] ?? '',
            'origin_region'              => $p['origin_region'] ?? '',
            'origin_country'             => $p['origin_country'] ?? '',
            'residence_village'          => $p['residence_village'] ?? '',
            'residence_district'         => $p['residence_district'] ?? '',
            'residence_region'           => $p['residence_region'] ?? '',
            'residence_country'          => $p['residence_country'] ?? '',
            'academic_programme'         => $p['academic_programme'] ?? '',
            'institution'                => $p['institution'] ?? '',
            'teaching_subject_1'         => $p['teaching_subjects_1'] ?? '',
            'teaching_subject_2'         => $p['teaching_subjects_2'] ?? '',
            'admission_number'           => $p['student_admission_number'] ?? '',
            'primary_school_name'        => $p['primary_school_name'] ?? '',
            'primary_school_district'    => $p['primary_school_district'] ?? '',
            'primary_school_dates'       => $p['primary_school_dates'] ?? '',
            'primary_school_responsible' => $p['primary_school_responsible'] ?? '',
            'olevel_school_name'         => $p['olevel_school_name'] ?? '',
            'olevel_school_district'     => $p['olevel_school_district'] ?? '',
            'olevel_school_dates'        => $p['olevel_school_dates'] ?? '',
            'olevel_school_responsible'  => $p['olevel_school_responsible'] ?? '',
            'alevel_school_name'         => $p['alevel_school_name'] ?? '',
            'alevel_school_district'     => $p['alevel_school_district'] ?? '',
            'alevel_school_dates'        => $p['alevel_school_dates'] ?? '',
            'alevel_school_responsible'  => $p['alevel_school_responsible'] ?? '',
            'university_name'            => $p['university_name'] ?? '',
            'university_district'        => $p['university_district'] ?? '',
            'university_dates'           => $p['university_dates'] ?? '',
            'university_responsible'     => $p['university_responsible'] ?? '',
            'alevel_institution'         => $p['alevel_school_exam'] ?? '',
            'alevel_year'                => $p['alevel_year'] ?? '',
            'alevel_index'               => $p['alevel_index'] ?? '',
            'alevel_points'              => $p['alevel_points'] ?? '',
            'diploma_institution'        => $p['diploma_school'] ?? '',
            'diploma_year'               => $p['diploma_year'] ?? '',
            'diploma_index'              => $p['diploma_index'] ?? '',
            'diploma_cgpa'               => $p['diploma_cgpa'] ?? '',
            'heac_institution'           => $p['heac_school'] ?? '',
            'heac_year'                  => $p['heac_year'] ?? '',
            'heac_index'                 => $p['heac_index'] ?? '',
            'heac_points'                => $p['heac_points'] ?? '',
            'mature_institution'         => $p['mature_school'] ?? '',
            'mature_year'                => $p['mature_year'] ?? '',
            'mature_index'               => $p['mature_index'] ?? '',
            'mature_points'              => $p['mature_points'] ?? '',
            'functionality_level'        => $di['functionality_level'] ?? '',
            'difficulty_walking'         => $bool($di['difficulty_walking'] ?? false),
            'difficulty_seeing'          => $bool($di['difficulty_seeing'] ?? false),
            'difficulty_hearing'         => $bool($di['difficulty_hearing'] ?? false),
            'difficulty_communicating'   => $bool($di['difficulty_communicating'] ?? false),
            'difficulty_picking'         => $bool($di['difficulty_picking'] ?? false),
            'difficulty_self_care'       => $bool($di['difficulty_self_care'] ?? false),
            'difficulty_emotions'        => $bool($di['difficulty_emotions'] ?? false),
            'assistive_support'          => $di['assistive_support'] ?? '',
            'spouse_surname'             => $de['spouse_surname'] ?? '',
            'spouse_other_names'         => $de['spouse_other_names'] ?? '',
            'spouse_education_level'     => $de['spouse_education_level'] ?? '',
            'spouse_occupation'          => $de['spouse_occupation'] ?? '',
            'marriage_balance_plan'      => $de['marriage_balance_plan'] ?? '',
            'num_children'               => $de['num_children'] ?? '',
            'oldest_child_age'           => $de['oldest_child_age'] ?? '',
            'youngest_child_age'         => $de['youngest_child_age'] ?? '',
            'childcare_plan'             => $de['childcare_plan'] ?? '',
            'spouse_support'             => $de['spouse_support'] ?? '',
            'non_financial_support'      => $de['non_financial_support_needed'] ?? '',
            'household_income'           => $fi['household_income'] ?? '',
            'financial_dependents'       => $fi['number_of_dependents'] ?? '',
            'income_source'              => $fi['income_source'] ?? '',
            'other_financial_support'    => $fi['other_financial_support'] ?? '',
            'motivation_statement'       => strip_tags($e['motivation'] ?? ''),
            'guardian_surname'           => $g['guardian_surname'] ?? '',
            'guardian_other_names'       => $g['guardian_other_names'] ?? '',
            'guardian_telephone'         => $g['guardian_telephone'] ?? '',
            'guardian_relation'          => $g['guardian_relation'] ?? '',
            'guardian_occupation'        => $g['guardian_occupation'] ?? '',
            'guardian_district'          => $g['guardian_district'] ?? '',
            'guardian_region'            => $g['guardian_region'] ?? '',
            'guardian_address'           => $g['guardian_address'] ?? '',
            'criminal_offence'           => isset($d['criminal_offence']) ? ($d['criminal_offence'] === 'yes' ? 'Yes' : 'No') : '',
            'criminal_details'           => $d['criminal_details'] ?? '',
            'hearing_source'             => $p['hearing_source'] ?? '',
            'hearing_source_other'       => $p['hearing_source_other'] ?? '',
            'score_financial_need'       => $sc['financial_need'] ?? 0,
            'score_academic_merit'       => $sc['academic_merit'] ?? 0,
            'score_demographics'         => $sc['demographics'] ?? 0,
            'score_commitment'           => $sc['commitment'] ?? 0,
            'score_essay_quality'        => $sc['essay_quality'] ?? 0,
            'score_total'                => $sc['total'] ?? 0,
        ];

        return array_values(
            array_map(fn (string $key) => $all[$key] ?? '', $this->selectedColumns)
        );
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
