function countWords(text) {
    const normalized = (text || '').trim();
    if (!normalized) return 0;
    return normalized.split(/\s+/).length;
}

export default function StepReview({ data }) {
    const pi   = data.personal_info;
    const gi   = data.guardian_info;
    const docs = data.documents;

    return (
        <div className="space-y-6">
            <div className="rounded-md border border-emerald-200 bg-emerald-50 p-4">
                <h3 className="text-base font-semibold text-emerald-900">Review Your Application</h3>
                <p className="mt-1 text-sm text-emerald-700">
                    Please review all information carefully before submitting. You can go back to any step to make changes.
                </p>
            </div>

            {/* Personal Info */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3">Personal Information</h4>
                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                    <SummaryItem label="Full Name" value={[pi.surname, pi.other_names].filter(Boolean).join(', ')} />
                    <SummaryItem label="Date of Birth" value={pi.date_of_birth} />
                    <SummaryItem label="NIN" value={pi.nin} />
                    <SummaryItem label="Phone" value={pi.phone} />
                    <SummaryItem label="Email" value={pi.email} />
                    <SummaryItem label="Marital Status" value={pi.marital_status} />
                    <SummaryItem label="Ugandan National"
                        value={pi.is_ugandan === 'yes' ? 'Yes' : pi.is_ugandan === 'no' ? 'No' : null} />
                    <SummaryItem label="Disability"
                        value={
                            pi.has_disability === 'yes'
                                ? `Yes – ${pi.disability_specify || 'specified in Step 2'}`
                                : pi.has_disability === 'no' ? 'No' : null
                        } />
                    <SummaryItem label="Place of Residence"
                        value={[pi.residence_village, pi.residence_district, pi.residence_region, pi.residence_country].filter(Boolean).join(', ')} />
                </dl>
            </div>

            {/* Education */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3">Information on Education</h4>
                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                    <SummaryItem label="Academic Programme" value={pi.academic_programme} />
                    <SummaryItem label="Institution" value={pi.institution} />
                    <SummaryItem label="Teaching Subjects"
                        value={[pi.teaching_subjects_1, pi.teaching_subjects_2].filter(Boolean).join(', ')} />
                    <SummaryItem label="Student Admission No." value={pi.student_admission_number} />
                </dl>
            </div>

            {/* Motivation */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3">Motivation</h4>
                <p className="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded">
                    {data.essay.motivation || <span className="text-gray-400 italic">Not provided</span>}
                </p>
                <p className="mt-1 text-xs text-gray-500">Word count: {countWords(data.essay.motivation)}</p>
            </div>

            {/* Documents */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3">Uploaded Documents</h4>
                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                    {[
                        ['exam_results',        'Examination Results'],
                        ['national_id',         'National ID'],
                        ['birth_certificate',   'Birth Certificate'],
                        ['admission_letter',    'Admission Letter'],
                        ['recommendation_lc1',  'Recommendation (LC1)'],
                        ['recommendation_school','Recommendation (School)'],
                        ['refugee_number',      'Refugee Number'],
                    ].map(([key, label]) => (
                        <div key={key}>
                            <dt className="font-medium text-gray-500">{label}</dt>
                            <dd className="mt-1">
                                {docs[key]
                                    ? <span className="text-green-600">✓ {typeof docs[key] === 'string' ? 'Uploaded' : docs[key].name}</span>
                                    : <span className="text-gray-400">Not uploaded</span>}
                            </dd>
                        </div>
                    ))}
                </dl>
            </div>

            {/* Guardian */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="text-sm font-semibold text-gray-900 mb-3">Guardian/Parent</h4>
                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                    <SummaryItem label="Name"
                        value={[gi.guardian_surname, gi.guardian_other_names].filter(Boolean).join(', ')} />
                    <SummaryItem label="Telephone" value={gi.guardian_telephone} />
                    <SummaryItem label="District/Region"
                        value={[gi.guardian_district, gi.guardian_region].filter(Boolean).join(', ')} />
                    <SummaryItem label="Occupation" value={gi.guardian_occupation} />
                    <SummaryItem label="Relationship" value={gi.guardian_relation} />
                </dl>
            </div>

            {/* Final declaration */}
            <div className="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                <p className="font-semibold">By submitting this application, I declare that:</p>
                <ul className="list-disc pl-5 mt-2 space-y-1 text-sm">
                    <li>All information provided is true and accurate to the best of my knowledge.</li>
                    <li>I understand that misrepresentation renders the application null and void.</li>
                    <li>I have read and agree to the terms of the LiT Scholarship Programme.</li>
                </ul>
            </div>
        </div>
    );
}

// Small helper for the summary grid items
function SummaryItem({ label, value }) {
    return (
        <div>
            <dt className="font-medium text-gray-500">{label}</dt>
            <dd className="mt-1">{value || <span className="text-gray-400 italic">Not provided</span>}</dd>
        </div>
    );
}
