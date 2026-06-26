function countWords(text) {
    const normalized = (text || '').trim();
    if (!normalized) return 0;
    return normalized.split(/\s+/).length;
}

export default function StepReview({ data }) {
    const pi   = data.personal_info;
    const di   = data.disability_info;
    const dep  = data.dependants_info;
    const fi   = data.financial_info;
    const gi   = data.guardian_info;
    const decl = data.declaration_info;
    const docs = data.documents;

    const isMarried = ['Married', 'Cohabiting / living with a partner'].includes(pi.marital_status);
    const hasDisability = pi.has_disability === 'yes';

    const nokList = pi.next_of_kin || [];

    const disabilityTypes = [
        ['difficulty_walking',      'Difficulty walking'],
        ['difficulty_seeing',       'Difficulty seeing'],
        ['difficulty_hearing',      'Difficulty hearing'],
        ['difficulty_communicating','Difficulty communicating'],
        ['difficulty_picking',      'Difficulty picking objects with hands'],
        ['difficulty_self_care',    'Difficulty self-care'],
        ['difficulty_emotions',     'Difficulty controlling emotions'],
    ];

    const familyDisability = [
        di.family_disability_father   && 'Father',
        di.family_disability_mother   && 'Mother',
        di.family_disability_siblings && 'Sibling(s)',
    ].filter(Boolean);

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="rounded-md border border-emerald-200 bg-emerald-50 p-4">
                <h3 className="text-base font-semibold text-emerald-900">Review Your Application</h3>
                <p className="mt-1 text-sm text-emerald-700">
                    Please review all information carefully before submitting. You can go back to any step to make changes.
                </p>
            </div>

            {/* ── Section A: Personal Information ─────────────────────── */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3 border-b pb-2">1. Personal Information</h4>
                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                    <SummaryItem label="Surname" value={pi.surname} />
                    <SummaryItem label="Other Name(s)" value={pi.other_names} />
                    <SummaryItem label="Date of Birth" value={pi.date_of_birth} />
                    <SummaryItem label="National Identification Number (NIN)" value={pi.nin} />
                    <SummaryItem label="Telephone" value={pi.phone} />
                    <SummaryItem label="Email" value={pi.email} />
                    <SummaryItem label="Marital Status" value={pi.marital_status} />
                    <SummaryItem
                        label="Disability"
                        value={
                            pi.has_disability === 'yes'
                                ? `Yes${pi.disability_specify ? ` – ${pi.disability_specify}` : ''}`
                                : pi.has_disability === 'no' ? 'No' : null
                        }
                    />
                    <SummaryItem
                        label="Ugandan National"
                        value={pi.is_ugandan === 'yes' ? 'Yes' : pi.is_ugandan === 'no' ? 'No' : null}
                    />
                    {pi.is_ugandan === 'no' && (
                        <SummaryItem label="Nationality" value={pi.non_ugandan_explanation} />
                    )}
                </dl>

                {/* Next of Kin */}
                {nokList.length > 0 && (
                    <div className="mt-4">
                        <p className="text-sm font-medium text-gray-700 mb-2">5. Next of Kin</p>
                        <div className="overflow-x-auto">
                            <table className="min-w-full border border-gray-200 text-sm">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {['#', 'Name', 'Relationship', 'Telephone'].map((h) => (
                                            <th key={h} className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-600">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody>
                                    {nokList.map((nok, i) => (
                                        <tr key={i} className={i % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                                            <td className="border border-gray-200 px-3 py-2 text-center text-gray-500">{i + 1}</td>
                                            <td className="border border-gray-200 px-3 py-2">{nok.name || <span className="text-gray-400 italic">Not provided</span>}</td>
                                            <td className="border border-gray-200 px-3 py-2">{nok.relationship || <span className="text-gray-400 italic">Not provided</span>}</td>
                                            <td className="border border-gray-200 px-3 py-2">{nok.telephone || <span className="text-gray-400 italic">Not provided</span>}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}

                {/* Place of Birth / Origin / Residence */}
                <div className="mt-4 space-y-3">
                    {[
                        { label: '7. Place of Birth',      prefix: 'birth' },
                        { label: '8. Place of Origin',     prefix: 'origin' },
                        { label: '9. Place of Residence',  prefix: 'residence' },
                    ].map(({ label, prefix }) => (
                        <div key={prefix}>
                            <p className="text-sm font-medium text-gray-700 mb-1">{label}</p>
                            <dl className="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-2 text-sm text-gray-700">
                                <SummaryItem label="Village/Parish/Sub-county" value={pi[`${prefix}_village`]} />
                                <SummaryItem label="District"                  value={pi[`${prefix}_district`]} />
                                <SummaryItem label="Region"                    value={pi[`${prefix}_region`]} />
                                <SummaryItem label="Country"                   value={pi[`${prefix}_country`]} />
                            </dl>
                        </div>
                    ))}
                </div>
            </div>

            {/* ── Education ───────────────────────────────────────────── */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3 border-b pb-2">Information on Education</h4>
                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700 mb-4">
                    <SummaryItem label="11. Academic Programme of Study" value={pi.academic_programme} />
                    <SummaryItem label="13. Institution"                 value={pi.institution} />
                    <SummaryItem label="Teaching Subject 1"              value={pi.teaching_subjects_1} />
                    <SummaryItem label="Teaching Subject 2"              value={pi.teaching_subjects_2} />
                    <SummaryItem label="Student Admission Number"        value={pi.student_admission_number} />
                </dl>

                {/* Q14 – Schools attended */}
                <p className="text-sm font-medium text-gray-700 mb-2">14. Schools Attended</p>
                <div className="overflow-x-auto mb-4">
                    <table className="min-w-full border border-gray-200 text-sm">
                        <thead className="bg-gray-50">
                            <tr>
                                {['Level', 'Name of School', 'District/Country', 'Dates of Attendance', 'Responsible Person'].map((h) => (
                                    <th key={h} className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-600">{h}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {[
                                ['Primary School',         'primary_school'],
                                ["O'Level",                'olevel_school'],
                                ["A'Level",                'alevel_school'],
                                ['University/Institution', 'university'],
                            ].map(([label, prefix], i) => (
                                <tr key={prefix} className={i % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                                    <td className="border border-gray-200 px-3 py-2 font-medium text-gray-600 whitespace-nowrap">{label}</td>
                                    {['name', 'district', 'dates', 'responsible'].map((col) => (
                                        <td key={col} className="border border-gray-200 px-3 py-2">
                                            {pi[`${prefix}_${col}`] || <span className="text-gray-400 italic">—</span>}
                                        </td>
                                    ))}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {/* Q15 – Mode of admission */}
                <p className="text-sm font-medium text-gray-700 mb-2">15. Mode of Admission to University</p>
                <div className="overflow-x-auto">
                    <table className="min-w-full border border-gray-200 text-sm">
                        <thead className="bg-gray-50">
                            <tr>
                                {['Mode', 'School/Institution', 'Year', 'Index/Reg. Number', 'Points / CGPA'].map((h) => (
                                    <th key={h} className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-600">{h}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {[
                                ["A' Level",    'alevel'],
                                ['Diploma',     'diploma'],
                                ['HEAC',        'heac'],
                                ['Mature Entry','mature'],
                            ].map(([label, prefix], i) => (
                                <tr key={prefix} className={i % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                                    <td className="border border-gray-200 px-3 py-2 font-medium text-gray-600 whitespace-nowrap">{label}</td>
                                    {['school_exam', 'year', 'index', 'points'].map((col) => (
                                        <td key={col} className="border border-gray-200 px-3 py-2">
                                            {pi[`${prefix}_${col}`] || <span className="text-gray-400 italic">—</span>}
                                        </td>
                                    ))}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* ── Disability Information ───────────────────────────────── */}
            {hasDisability && (
                <div className="rounded-md border border-gray-200 p-4">
                    <h4 className="text-sm font-semibold text-gray-900 mb-3 border-b pb-2">Disability Information</h4>
                    <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                        <div className="md:col-span-2">
                            <dt className="font-medium text-gray-500">16. Forms of Disability</dt>
                            <dd className="mt-1">
                                {disabilityTypes.filter(([field]) => di[field]).map(([, label]) => label).join(', ')
                                    || <span className="text-gray-400 italic">None ticked</span>}
                            </dd>
                        </div>
                        <SummaryItem label="17. Functionality Level" value={di.functionality_level} />
                        <div>
                            <dt className="font-medium text-gray-500">18. Family Members with Disability</dt>
                            <dd className="mt-1">
                                {familyDisability.length > 0 ? familyDisability.join(', ') : <span className="text-gray-400 italic">None indicated</span>}
                            </dd>
                        </div>
                        {di.family_disability_siblings && (
                            <>
                                <SummaryItem label="Female Siblings with Disability" value={di.siblings_female_count} />
                                <SummaryItem label="Male Siblings with Disability"   value={di.siblings_male_count} />
                            </>
                        )}
                        {di.assistive_support && (
                            <div className="md:col-span-2">
                                <dt className="font-medium text-gray-500">Assistive Support Required</dt>
                                <dd className="mt-1 whitespace-pre-wrap bg-gray-50 p-2 rounded text-sm">{di.assistive_support}</dd>
                            </div>
                        )}
                    </dl>
                </div>
            )}

            {/* ── Dependants Information ───────────────────────────────── */}
            {(isMarried || dep.num_children) && (
                <div className="rounded-md border border-gray-200 p-4">
                    <h4 className="text-sm font-semibold text-gray-900 mb-3 border-b pb-2">Dependants Information</h4>
                    {isMarried && (
                        <>
                            <p className="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">19a. Spouse / Partner</p>
                            <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700 mb-4">
                                <SummaryItem label="Spouse Surname"       value={dep.spouse_surname} />
                                <SummaryItem label="Spouse Other Name(s)" value={dep.spouse_other_names} />
                                <SummaryItem label="Level of Education"   value={dep.spouse_education_level} />
                                <SummaryItem label="Occupation"           value={dep.spouse_occupation} />
                                {dep.marriage_balance_plan && (
                                    <div className="md:col-span-2">
                                        <dt className="font-medium text-gray-500">Marriage &amp; School Balance Plan</dt>
                                        <dd className="mt-1 whitespace-pre-wrap bg-gray-50 p-2 rounded text-sm">{dep.marriage_balance_plan}</dd>
                                    </div>
                                )}
                            </dl>
                        </>
                    )}
                    <p className="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">19b. Children</p>
                    <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                        <SummaryItem label="Number of Children"     value={dep.num_children} />
                        <SummaryItem label="Age of Oldest Child"    value={dep.oldest_child_age} />
                        <SummaryItem label="Age of Youngest Child"  value={dep.youngest_child_age} />
                        {dep.childcare_plan && (
                            <div className="md:col-span-2">
                                <dt className="font-medium text-gray-500">Childcare Plan</dt>
                                <dd className="mt-1 whitespace-pre-wrap bg-gray-50 p-2 rounded text-sm">{dep.childcare_plan}</dd>
                            </div>
                        )}
                        {dep.spouse_support && (
                            <div className="md:col-span-2">
                                <dt className="font-medium text-gray-500">Support from Spouse</dt>
                                <dd className="mt-1 whitespace-pre-wrap bg-gray-50 p-2 rounded text-sm">{dep.spouse_support}</dd>
                            </div>
                        )}
                        {dep.non_financial_support_needed && (
                            <div className="md:col-span-2">
                                <dt className="font-medium text-gray-500">Non-Financial Support Needed</dt>
                                <dd className="mt-1 whitespace-pre-wrap bg-gray-50 p-2 rounded text-sm">{dep.non_financial_support_needed}</dd>
                            </div>
                        )}
                    </dl>
                </div>
            )}

            {/* ── Financial Information ────────────────────────────────── */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3 border-b pb-2">Financial Information</h4>
                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                    <SummaryItem label="Estimated Annual Household Income (UGX)" value={fi?.household_income} />
                    <SummaryItem label="Number of Dependents"                    value={fi?.number_of_dependents} />
                    <SummaryItem label="Primary Source of Household Income"      value={fi?.income_source} />
                    <SummaryItem label="Other Financial Support for Studies"     value={fi?.other_financial_support} />
                </dl>
            </div>

            {/* ── Motivation Essay ─────────────────────────────────────── */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3 border-b pb-2">Motivation Essay</h4>
                <p className="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded">
                    {data.essay.motivation || <span className="text-gray-400 italic">Not provided</span>}
                </p>
                <p className="mt-1 text-xs text-gray-500">Word count: {countWords(data.essay.motivation)}</p>
            </div>

            {/* ── Uploaded Documents ───────────────────────────────────── */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3 border-b pb-2">Uploaded Documents</h4>
                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                    {[
                        ['exam_results',         'Examination Results'],
                        ['national_id',          'National ID / Identification Document'],
                        ['birth_certificate',    'Birth Certificate'],
                        ['admission_letter',     'Admission Letter'],
                        ['recommendation_lc1',   'Recommendation Letter (LC1)'],
                        ['recommendation_school','Recommendation Letter (School)'],
                        ['refugee_number',       'Refugee Number Document'],
                    ].map(([key, label]) => (
                        <div key={key}>
                            <dt className="font-medium text-gray-500">{label}</dt>
                            <dd className="mt-1">
                                {docs[key]
                                    ? <span className="text-green-600">✓ {typeof docs[key] === 'string' ? 'Uploaded' : docs[key].name}</span>
                                    : <span className="text-gray-400 italic">Not uploaded</span>}
                            </dd>
                        </div>
                    ))}
                </dl>
            </div>

            {/* ── Section C: Guardian / Parent ─────────────────────────── */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3 border-b pb-2">Parent / Legal Guardian Information</h4>
                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                    <SummaryItem label="21. Surname"            value={gi.guardian_surname} />
                    <SummaryItem label="Other Name(s)"          value={gi.guardian_other_names} />
                    <SummaryItem label="22. Address"            value={gi.guardian_address} />
                    <SummaryItem label="Telephone"              value={gi.guardian_telephone} />
                    <SummaryItem label="23. District of Residence" value={gi.guardian_district} />
                    <SummaryItem label="Region of Residence"    value={gi.guardian_region} />
                    <SummaryItem label="24. Occupation"         value={gi.guardian_occupation} />
                    <SummaryItem label="Relationship to Applicant" value={gi.guardian_relation} />
                </dl>
            </div>

            {/* ── Section D: Criminal Declaration ─────────────────────── */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3 border-b pb-2">Criminal Offence Declaration</h4>
                <dl className="grid grid-cols-1 gap-y-3 text-sm text-gray-700">
                    <SummaryItem
                        label="25. Ever charged or convicted of a criminal offence?"
                        value={
                            decl?.criminal_offence === 'yes' ? 'Yes'
                            : decl?.criminal_offence === 'no' ? 'No'
                            : null
                        }
                    />
                    {decl?.criminal_offence === 'yes' && (
                        <div>
                            <dt className="font-medium text-gray-500">Details of Charge / Conviction</dt>
                            <dd className="mt-1 whitespace-pre-wrap bg-gray-50 p-2 rounded text-sm">
                                {decl.criminal_details || <span className="text-gray-400 italic">Not provided</span>}
                            </dd>
                        </div>
                    )}
                </dl>
            </div>

            {/* ── How did you hear about the scholarship ───────────────── */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3 border-b pb-2">How Did You Hear About the Scholarship?</h4>
                <dl className="grid grid-cols-1 gap-y-3 text-sm text-gray-700">
                    <SummaryItem
                        label="How you heard about this scholarship"
                        value={(() => {
                            const sourceLabels = {
                                organization_website: 'Organization website',
                                social_media:         'Social media (e.g., WhatsApp, Facebook, Twitter, Instagram)',
                                referral:             'Referral from a friend or colleague',
                                advertisement:        'Advertisement (TV, radio, newspaper)',
                                professional_network: 'Professional network or industry contacts',
                                email_newsletter:     'Email newsletter or scholarship alert',
                                walk_in:              'Walk-in / Direct visit to the organization',
                                other:                'Other',
                            };
                            const label = sourceLabels[pi.hearing_source];
                            if (!label) return null;
                            if (pi.hearing_source === 'other' && pi.hearing_source_other) {
                                return `Other – ${pi.hearing_source_other}`;
                            }
                            return label;
                        })()}
                    />
                </dl>
            </div>

            {/* ── Final Declaration ────────────────────────────────────── */}
            <div className="rounded-md border border-amber-300 bg-amber-50 p-5 text-sm text-amber-900">
                <p className="font-bold text-base mb-3">DECLARATION</p>
                <p className="mb-3 leading-relaxed">
                    I, the undersigned applicant, hereby solemnly declare and affirm the following in respect of this application
                    for the Leaders in Teaching (LiT) Female STEM Student Teachers' Scholarship:
                </p>
                <ol className="list-decimal pl-5 space-y-2 text-sm leading-relaxed mb-4">
                    <li>
                        All information furnished in this application form is, to the best of my knowledge and belief,
                        true, complete, and accurate in every material respect.
                    </li>
                    <li>
                        I have not wilfully omitted, concealed, or misrepresented any fact that is, or may be,
                        relevant to the assessment of my application or my eligibility for this scholarship.
                    </li>
                    <li>
                        I understand and accept that any deliberate misrepresentation, falsification, or omission
                        of material information shall render this application null and void, and any award made
                        on the basis of such misrepresentation shall be immediately withdrawn and must be fully
                        refunded. I further understand that I may be subject to disciplinary or legal action.
                    </li>
                    <li>
                        I consent to the verification of any information provided in this application by the
                        LiT Scholarship Programme or its authorised representatives.
                    </li>
                    <li>
                        I have read, understood, and agree to comply with all the terms, conditions, and
                        obligations of the LiT Scholarship Programme.
                    </li>
                </ol>
                <p className="font-semibold text-amber-800 border-t border-amber-300 pt-3">
                    By clicking "Submit Application", I confirm that I have read and understood the above declaration
                    in its entirety, and I accept it as legally binding. Submission of this application constitutes
                    my formal acceptance of this declaration.
                </p>
            </div>
        </div>
    );
}

// Helper component for labelled summary rows
function SummaryItem({ label, value }) {
    return (
        <div>
            <dt className="font-medium text-gray-500">{label}</dt>
            <dd className="mt-1">{value !== '' && value !== null && value !== undefined
                ? value
                : <span className="text-gray-400 italic">Not provided</span>}
            </dd>
        </div>
    );
}
