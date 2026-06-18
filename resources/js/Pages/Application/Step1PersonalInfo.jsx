import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import { RequiredLabel, RadioField } from './FormComponents';

export default function StepSectionA({ data, errors, stepErrors, updateSection, updateNextOfKin, isLocked }) {
    const pi = data.personal_info;

    const nokList = pi.next_of_kin || [
        { name: '', relationship: '', telephone: '' },
        { name: '', relationship: '', telephone: '' },
    ];

    return (
        <div className="space-y-8">
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                    Applicant Background Information
                </h4>
                {/* <p className="text-xs text-gray-500 mb-4 italic">
                    Complete all questions using BLOCK letters only. Your application will not be processed if you leave any questions unanswered.
                </p> */}

                {/* ── 1. Personal Information ───────────────────────────── */}
                <h5 className="font-semibold text-gray-700 mb-3">1. Personal Information</h5>
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <RequiredLabel htmlFor="surname" value="Surname" required />
                        <TextInput id="surname" className="mt-1 block w-full uppercase"
                            value={pi.surname}
                            onChange={(e) => updateSection('personal_info', 'surname', e.target.value)}
                            disabled={isLocked} required />
                        <InputError message={errors['personal_info.surname'] || stepErrors['personal_info.surname']} className="mt-2" />
                    </div>
                    <div>
                        <RequiredLabel htmlFor="other_names" value="Other Name(s)" required />
                        <TextInput id="other_names" className="mt-1 block w-full uppercase"
                            value={pi.other_names}
                            onChange={(e) => updateSection('personal_info', 'other_names', e.target.value)}
                            disabled={isLocked} required />
                        <InputError message={errors['personal_info.other_names'] || stepErrors['personal_info.other_names']} className="mt-2" />
                    </div>
                    <div>
                        <RequiredLabel htmlFor="date_of_birth" value="Date of Birth (e.g. 20 May 1996)" required />
                        <TextInput id="date_of_birth" type="date" className="mt-1 block w-full"
                            value={pi.date_of_birth}
                            onChange={(e) => updateSection('personal_info', 'date_of_birth', e.target.value)}
                            disabled={isLocked} required />
                        <InputError message={errors['personal_info.date_of_birth'] || stepErrors['personal_info.date_of_birth']} className="mt-2" />
                    </div>
                </div>

                {/* ── 3. Disability (yes/no) ────────────────────────────── */}
                <div className="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <RequiredLabel htmlFor="has_disability_a" value="3. Do you have any Disability?" required />
                        <div className="mt-2 flex gap-6">
                            <RadioField name="has_disability_a" value="yes" label="YES"
                                checked={pi.has_disability === 'yes'}
                                onChange={(v) => updateSection('personal_info', 'has_disability', v)}
                                disabled={isLocked} />
                            <RadioField name="has_disability_a" value="no" label="NO"
                                checked={pi.has_disability === 'no'}
                                onChange={(v) => updateSection('personal_info', 'has_disability', v)}
                                disabled={isLocked} />
                        </div>
                        <InputError message={errors['personal_info.has_disability'] || stepErrors['personal_info.has_disability']} className="mt-2" />
                    </div>
                    {pi.has_disability === 'yes' && (
                        <div>
                            <InputLabel htmlFor="disability_specify" value="If yes, specify:" />
                            <TextInput id="disability_specify" className="mt-1 block w-full"
                                value={pi.disability_specify}
                                onChange={(e) => updateSection('personal_info', 'disability_specify', e.target.value)}
                                disabled={isLocked} />
                        </div>
                    )}
                </div>

                {/* ── 4. Contact & Marital Status ───────────────────────── */}
                <div className="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <RequiredLabel htmlFor="phone" value="4. Telephone No(s)" required />
                        <TextInput id="phone" className="mt-1 block w-full"
                            value={pi.phone}
                            onChange={(e) => updateSection('personal_info', 'phone', e.target.value)}
                            disabled={isLocked} required />
                        <InputError message={errors['personal_info.phone'] || stepErrors['personal_info.phone']} className="mt-2" />
                    </div>
                    <div>
                        <InputLabel htmlFor="email" value="Email" />
                        <TextInput id="email" type="email" className="mt-1 block w-full"
                            value={pi.email}
                            onChange={(e) => updateSection('personal_info', 'email', e.target.value)}
                            disabled={isLocked} />
                        <InputError message={errors['personal_info.email']} className="mt-2" />
                    </div>
                    <div>
                        <RequiredLabel htmlFor="marital_status" value="Marital Status" required />
                        <div className="mt-2 flex flex-wrap gap-4">
                            {['Single', 'Married', 'Cohabiting / living with a partner'].map((ms) => (
                                <RadioField key={ms} name="marital_status" value={ms} label={ms}
                                    checked={pi.marital_status === ms}
                                    onChange={(v) => updateSection('personal_info', 'marital_status', v)}
                                    disabled={isLocked} />
                            ))}
                        </div>
                        <InputError message={errors['personal_info.marital_status'] || stepErrors['personal_info.marital_status']} className="mt-2" />
                    </div>
                </div>

                {/* ── 5. Next of Kin ────────────────────────────────────── */}
                <div className="mt-6">
                    <h5 className="font-semibold text-gray-700 mb-3">5. Next of Kin</h5>
                    <div className="overflow-x-auto">
                        <table className="min-w-full border border-gray-200 text-sm">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">#</th>
                                    <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">Name</th>
                                    <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">Relationship</th>
                                    <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">Telephone</th>
                                </tr>
                            </thead>
                            <tbody>
                                {[0, 1].map((i) => (
                                    <tr key={i}>
                                        <td className="border border-gray-200 px-3 py-2 text-center font-medium text-gray-500">{i + 1}.</td>
                                        <td className="border border-gray-200 px-2 py-1">
                                            <TextInput className="block w-full border-0 shadow-none focus:ring-0"
                                                value={nokList[i]?.name || ''}
                                                onChange={(e) => updateNextOfKin(i, 'name', e.target.value)}
                                                disabled={isLocked} />
                                        </td>
                                        <td className="border border-gray-200 px-2 py-1">
                                            <TextInput className="block w-full border-0 shadow-none focus:ring-0"
                                                value={nokList[i]?.relationship || ''}
                                                onChange={(e) => updateNextOfKin(i, 'relationship', e.target.value)}
                                                disabled={isLocked} />
                                        </td>
                                        <td className="border border-gray-200 px-2 py-1">
                                            <TextInput className="block w-full border-0 shadow-none focus:ring-0"
                                                value={nokList[i]?.telephone || ''}
                                                onChange={(e) => updateNextOfKin(i, 'telephone', e.target.value)}
                                                disabled={isLocked} />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* ── Nationality & Address ─────────────────────────────── */}
                <div className="mt-6">
                    <h5 className="font-semibold text-gray-700 mb-3">Nationality and Address</h5>

                    {/* Q6 */}
                    <div className="mb-4">
                        <RequiredLabel htmlFor="is_ugandan" value="6. Are you a Ugandan?" required />
                        <div className="mt-2 flex gap-6">
                            <RadioField name="is_ugandan" value="yes" label="YES"
                                checked={pi.is_ugandan === 'yes'}
                                onChange={(v) => updateSection('personal_info', 'is_ugandan', v)}
                                disabled={isLocked} />
                            <RadioField name="is_ugandan" value="no" label="NO"
                                checked={pi.is_ugandan === 'no'}
                                onChange={(v) => updateSection('personal_info', 'is_ugandan', v)}
                                disabled={isLocked} />
                        </div>
                        <InputError message={errors['personal_info.is_ugandan'] || stepErrors['personal_info.is_ugandan']} className="mt-2" />
                    </div>
                    {/* NIN — shown only for Ugandans */}
                    {pi.is_ugandan === 'yes' && (
                        <div className="mb-4">
                            <RequiredLabel htmlFor="nin" value="National Identification Number (NIN)" required />
                            <TextInput id="nin" className="mt-1 block w-full uppercase tracking-widest"
                                minLength={4}
                                maxLength={14}
                                placeholder="e.g. CM9100012345ABCD"
                                value={pi.nin || ''}
                                onChange={(e) => updateSection('personal_info', 'nin', e.target.value)}
                                disabled={isLocked} required />
                            <InputError message={errors['personal_info.nin'] || stepErrors['personal_info.nin']} className="mt-2" />
                        </div>
                    )}

                    {/* Non-Ugandan identification — shown only when NOT Ugandan */}
                    {pi.is_ugandan === 'no' && (
                        <div className="mb-4 space-y-4">
                            <p className="text-sm text-gray-600 italic">
                                Please provide at least one of the following identification details:
                            </p>
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <InputLabel htmlFor="passport_number" value="Passport Number" />
                                    <TextInput id="passport_number" className="mt-1 block w-full uppercase tracking-widest"
                                        placeholder="e.g. A12345678"
                                        value={pi.passport_number || ''}
                                        onChange={(e) => updateSection('personal_info', 'passport_number', e.target.value)}
                                        disabled={isLocked} />
                                    <InputError message={errors['personal_info.passport_number'] || stepErrors['personal_info.passport_number']} className="mt-2" />
                                </div>
                                <div>
                                    <InputLabel htmlFor="foreign_id_number" value="National ID No. (from country of origin)" />
                                    <TextInput id="foreign_id_number" className="mt-1 block w-full uppercase tracking-widest"
                                        value={pi.foreign_id_number || ''}
                                        onChange={(e) => updateSection('personal_info', 'foreign_id_number', e.target.value)}
                                        disabled={isLocked} />
                                    <InputError message={errors['personal_info.foreign_id_number'] || stepErrors['personal_info.foreign_id_number']} className="mt-2" />
                                </div>
                                <div>
                                    <InputLabel htmlFor="refugee_card_number" value="Refugee Card Number" />
                                    <TextInput id="refugee_card_number" className="mt-1 block w-full uppercase tracking-widest"
                                        placeholder="e.g. UGA/2023/123456"
                                        value={pi.refugee_card_number || ''}
                                        onChange={(e) => updateSection('personal_info', 'refugee_card_number', e.target.value)}
                                        disabled={isLocked} />
                                    <InputError message={errors['personal_info.refugee_card_number'] || stepErrors['personal_info.refugee_card_number']} className="mt-2" />
                                </div>
                                <div>
                                    <InputLabel htmlFor="non_ugandan_explanation" value="Nationality / Explanation" />
                                    <TextInput id="non_ugandan_explanation" className="mt-1 block w-full"
                                        placeholder="e.g. Kenyan national, student visa"
                                        value={pi.non_ugandan_explanation || ''}
                                        onChange={(e) => updateSection('personal_info', 'non_ugandan_explanation', e.target.value)}
                                        disabled={isLocked} />
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Q7, Q8, Q9 – place grids */}
                    {[
                        { label: '7. Place of Birth', prefix: 'birth' },
                        { label: '8. Place of Origin', prefix: 'origin' },
                        { label: '9. Place of Residence', prefix: 'residence' },
                    ].map(({ label, prefix }) => (
                        <div key={prefix} className="mb-4">
                            <InputLabel value={label} className="font-medium" />
                            <div className="grid grid-cols-2 gap-3 mt-2 md:grid-cols-4">
                                {[
                                    [`${prefix}_village`, 'Village/Parish/Sub-county'],
                                    [`${prefix}_district`, 'District'],
                                    [`${prefix}_region`, 'Region'],
                                    [`${prefix}_country`, 'Country'],
                                ].map(([field, lbl]) => (
                                    <div key={field}>
                                        <InputLabel value={lbl} className="text-xs text-gray-500" />
                                        <TextInput className="mt-1 block w-full text-sm uppercase"
                                            value={pi[field] || ''}
                                            onChange={(e) => updateSection('personal_info', field, e.target.value)}
                                            disabled={isLocked} />
                                    </div>
                                ))}
                            </div>
                        </div>
                    ))}
                </div>

                {/* ── Information on Education ────────────────────────────── */}
                <div className="mt-6">
                    <h4 className="font-semibold text-gray-800 text-base border-b pb-2 mb-4">
                        Information on Education
                    </h4>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <RequiredLabel htmlFor="academic_programme" value="11. Academic Programme of Study" required />
                            <TextInput id="academic_programme" className="mt-1 block w-full uppercase"
                                value={pi.academic_programme}
                                onChange={(e) => updateSection('personal_info', 'academic_programme', e.target.value)}
                                disabled={isLocked} required />
                            <InputError message={errors['personal_info.academic_programme'] || stepErrors['personal_info.academic_programme']} className="mt-2" />
                        </div>
                        <div>
                            <RequiredLabel htmlFor="institution" value="13. Institution (University/UNITE Campus)" required />
                            <TextInput id="institution" className="mt-1 block w-full uppercase"
                                value={pi.institution}
                                onChange={(e) => updateSection('personal_info', 'institution', e.target.value)}
                                disabled={isLocked} required />
                            <InputError message={errors['personal_info.institution'] || stepErrors['personal_info.institution']} className="mt-2" />
                        </div>
                        <div>
                            <InputLabel htmlFor="teaching_subjects_1" value="Teaching Subject 1 of Interest" />
                            <TextInput id="teaching_subjects_1" className="mt-1 block w-full uppercase"
                                value={pi.teaching_subjects_1}
                                onChange={(e) => updateSection('personal_info', 'teaching_subjects_1', e.target.value)}
                                disabled={isLocked} />
                        </div>
                        <div>
                            <InputLabel htmlFor="teaching_subjects_2" value="Teaching Subject 2 of Interest" />
                            <TextInput id="teaching_subjects_2" className="mt-1 block w-full uppercase"
                                value={pi.teaching_subjects_2}
                                onChange={(e) => updateSection('personal_info', 'teaching_subjects_2', e.target.value)}
                                disabled={isLocked} />
                        </div>
                        <div>
                            <InputLabel htmlFor="student_admission_number" value="Student Admission Number" />
                            <TextInput id="student_admission_number" className="mt-1 block w-full uppercase tracking-widest"
                                value={pi.student_admission_number}
                                onChange={(e) => updateSection('personal_info', 'student_admission_number', e.target.value)}
                                disabled={isLocked} />
                        </div>
                    </div>

                    {/* Q14 – Schools attended */}
                    <div className="mt-6">
                        <h5 className="font-semibold text-gray-700 mb-3">14. Schools Attended</h5>
                        <div className="overflow-x-auto">
                            <table className="min-w-full border border-gray-200 text-sm">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {['Level', 'Name of School', 'District/Country', 'Dates of Attendance', 'Who was responsible for education & upkeep?'].map((h) => (
                                            <th key={h} className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody>
                                    {[
                                        ['Primary School', 'primary_school'],
                                        ["O'Level", 'olevel_school'],
                                        ["A'Level", 'alevel_school'],
                                        ['University / Institution', 'university'],
                                    ].map(([label, prefix]) => (
                                        <tr key={prefix}>
                                            <td className="border border-gray-200 px-3 py-2 font-medium text-gray-600 whitespace-nowrap">{label}</td>
                                            {['name', 'district', 'dates', 'responsible'].map((col) => (
                                                <td key={col} className="border border-gray-200 px-2 py-1">
                                                    <TextInput
                                                        className="block w-full border-0 shadow-none focus:ring-0 uppercase text-sm min-w-[120px]"
                                                        value={pi[`${prefix}_${col}`] || ''}
                                                        onChange={(e) => updateSection('personal_info', `${prefix}_${col}`, e.target.value)}
                                                        disabled={isLocked}
                                                        placeholder={col === 'dates' ? 'e.g. 2010-2013' : ''}
                                                    />
                                                </td>
                                            ))}
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* Q15 – Admission mode */}
                    <div className="mt-6">
                        <h5 className="font-semibold text-gray-700 mb-1">15. Mode of Admission to University</h5>
                        <p className="text-xs text-gray-500 mb-3 italic">
                            Use the aggregate that your admission into the University was based on. For Diploma holders provide the CGPA obtained.
                        </p>
                        <div className="overflow-x-auto">
                            <table className="min-w-full border border-gray-200 text-sm">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {['Mode', 'School/Institution', 'Year of Exam/Completion', 'Candidate Index/Reg. Number', 'Points Score / CGPA'].map((h) => (
                                            <th key={h} className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody>
                                    {[
                                        ["A' Level", 'alevel'],
                                        ['Diploma', 'diploma'],
                                        ['HEAC', 'heac'],
                                        ['Mature Entry', 'mature'],
                                    ].map(([label, prefix]) => (
                                        <tr key={prefix}>
                                            <td className="border border-gray-200 px-3 py-2 font-medium text-gray-600 whitespace-nowrap">{label}</td>
                                            {['school_exam', 'year', 'index', 'points'].map((col) => (
                                                <td key={col} className="border border-gray-200 px-2 py-1">
                                                    <TextInput
                                                        className="block w-full border-0 shadow-none focus:ring-0 uppercase text-sm min-w-[100px]"
                                                        value={pi[`${prefix}_${col}`] || ''}
                                                        onChange={(e) => updateSection('personal_info', `${prefix}_${col}`, e.target.value)}
                                                        disabled={isLocked} />
                                                </td>
                                            ))}
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
