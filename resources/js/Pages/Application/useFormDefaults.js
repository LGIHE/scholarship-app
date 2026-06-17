import { useMemo } from 'react';

/**
 * Builds the default form data shape and merges in any existing application data.
 * Extracted here so Form.jsx stays focused on orchestration.
 */
export function useFormDefaults(auth, application) {
    const nameParts = (auth?.user?.name || '').trim().split(/\s+/).filter(Boolean);
    const defaultFirstName = nameParts[0] || '';
    const defaultLastName = nameParts.slice(1).join(' ');

    const defaults = useMemo(
        () => ({
            personal_info: {
                surname: defaultLastName,
                other_names: defaultFirstName,
                date_of_birth: '',
                nin: '',
                has_disability: '',
                disability_specify: '',
                phone: '',
                email: auth?.user?.email || '',
                marital_status: '',
                next_of_kin: [
                    { name: '', relationship: '', telephone: '' },
                    { name: '', relationship: '', telephone: '' },
                ],
                is_ugandan: '',
                non_ugandan_explanation: '',
                birth_village: '', birth_district: '', birth_region: '', birth_country: '',
                origin_village: '', origin_district: '', origin_region: '', origin_country: '',
                residence_village: '', residence_district: '', residence_region: '', residence_country: '',
                academic_programme: '',
                teaching_subjects_1: '',
                teaching_subjects_2: '',
                institution: '',
                student_admission_number: '',
                primary_school_name: '', primary_school_district: '', primary_school_dates: '', primary_school_responsible: '',
                olevel_school_name: '', olevel_school_district: '', olevel_school_dates: '', olevel_school_responsible: '',
                alevel_school_name: '', alevel_school_district: '', alevel_school_dates: '', alevel_school_responsible: '',
                university_name: '', university_district: '', university_dates: '', university_responsible: '',
                alevel_school_exam: '', alevel_year: '', alevel_index: '', alevel_points: '',
                diploma_school: '', diploma_year: '', diploma_index: '', diploma_cgpa: '',
                heac_school: '', heac_year: '', heac_index: '', heac_points: '',
                mature_school: '', mature_year: '', mature_index: '', mature_points: '',
            },
            disability_info: {
                difficulty_walking: false,
                difficulty_seeing: false,
                difficulty_hearing: false,
                difficulty_communicating: false,
                difficulty_picking: false,
                difficulty_self_care: false,
                difficulty_emotions: false,
                functionality_level: '',
                family_disability_father: false,
                family_disability_mother: false,
                family_disability_siblings: false,
                siblings_female_count: '',
                siblings_male_count: '',
                assistive_support: '',
            },
            dependants_info: {
                spouse_surname: '',
                spouse_other_names: '',
                spouse_education_level: '',
                spouse_occupation: '',
                marriage_balance_plan: '',
                num_children: '',
                oldest_child_age: '',
                youngest_child_age: '',
                childcare_plan: '',
                spouse_support: '',
                non_financial_support_needed: '',
            },
            financial_info: {
                household_income: '',
                number_of_dependents: '',
                income_source: '',
                other_financial_support: '',
            },
            essay: {
                motivation: '',
            },
            guardian_info: {
                guardian_surname: '',
                guardian_other_names: '',
                guardian_address: '',
                guardian_telephone: '',
                guardian_district: '',
                guardian_region: '',
                guardian_occupation: '',
                guardian_relation: '',
            },
            declaration_info: {
                criminal_offence: '',
                criminal_details: '',
            },
            documents: {
                exam_results: null,
                national_id: null,
                birth_certificate: null,
                admission_letter: null,
                recommendation_lc1: null,
                recommendation_school: null,
                refugee_number: null,
            },
        }),
        // eslint-disable-next-line react-hooks/exhaustive-deps
        [defaultFirstName, defaultLastName, auth?.user?.email],
    );

    const initialData = useMemo(
        () => ({
            personal_info:    { ...defaults.personal_info,    ...(application?.personal_info    || {}) },
            disability_info:  { ...defaults.disability_info,  ...(application?.disability_info  || {}) },
            dependants_info:  { ...defaults.dependants_info,  ...(application?.dependants_info  || {}) },
            financial_info:   { ...defaults.financial_info,   ...(application?.financial_info   || {}) },
            essay:            { ...defaults.essay,            ...(application?.essay            || {}) },
            guardian_info:    { ...defaults.guardian_info,    ...(application?.guardian_info    || {}) },
            declaration_info: { ...defaults.declaration_info, ...(application?.declaration_info || {}) },
            documents:        { ...defaults.documents,        ...(application?.documents        || {}) },
        }),
        [application, defaults],
    );

    return initialData;
}
