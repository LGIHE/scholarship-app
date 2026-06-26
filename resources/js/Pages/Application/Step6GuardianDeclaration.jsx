import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import { RequiredLabel, RadioField } from './FormComponents';

const HEARING_SOURCE_OPTIONS = [
    { value: 'organization_website', label: 'Organization website' },
    { value: 'social_media',         label: 'Social media (e.g., WhatsApp, Facebook, Twitter, Instagram)' },
    { value: 'referral',             label: 'Referral from a friend or colleague' },
    { value: 'advertisement',        label: 'Advertisement (TV, radio, newspaper)' },
    { value: 'professional_network', label: 'Professional network or industry contacts' },
    { value: 'email_newsletter',     label: 'Email newsletter or scholarship alert' },
    { value: 'walk_in',              label: 'Walk-in / Direct visit to the organization' },
    { value: 'other',                label: 'Other' },
];

export default function StepSectionCD({ data, errors, stepErrors, updateSection, isLocked }) {
    const gi   = data.guardian_info;
    const decl = data.declaration_info;
    const pi   = data.personal_info;

    return (
        <div className="space-y-6">
            {/* ── How did you hear about us ───────────────────────────────── */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                    How Did You Hear About the Scholarship?
                </h4>
                <div className="space-y-3">
                    <div>
                        <RequiredLabel
                            htmlFor="hearing_source"
                            value="How did you hear about this scholarship?"
                            required
                        />
                        <div className="mt-2 space-y-2">
                            {HEARING_SOURCE_OPTIONS.map((opt) => (
                                <label
                                    key={opt.value}
                                    className={`flex items-start gap-3 cursor-pointer rounded-md border px-4 py-2.5 transition-colors ${
                                        isLocked ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'
                                    } ${
                                        pi.hearing_source === opt.value
                                            ? 'border-emerald-500 bg-emerald-50'
                                            : 'border-gray-200 bg-white hover:border-emerald-300 hover:bg-emerald-50/30'
                                    }`}
                                >
                                    <input
                                        type="radio"
                                        name="hearing_source"
                                        value={opt.value}
                                        checked={pi.hearing_source === opt.value}
                                        onChange={() => updateSection('personal_info', 'hearing_source', opt.value)}
                                        disabled={isLocked}
                                        className="mt-0.5 h-4 w-4 shrink-0 border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                    />
                                    <span className="text-sm text-gray-700">{opt.label}</span>
                                </label>
                            ))}
                        </div>
                        <InputError
                            message={errors['personal_info.hearing_source'] || stepErrors['personal_info.hearing_source']}
                            className="mt-2"
                        />
                    </div>

                    {pi.hearing_source === 'other' && (
                        <div>
                            <RequiredLabel
                                htmlFor="hearing_source_other"
                                value="Please specify"
                                required
                            />
                            <TextInput
                                id="hearing_source_other"
                                className="mt-1 block w-full"
                                value={pi.hearing_source_other || ''}
                                onChange={(e) => updateSection('personal_info', 'hearing_source_other', e.target.value)}
                                disabled={isLocked}
                                maxLength={500}
                                placeholder="Please describe how you heard about the scholarship..."
                            />
                            <InputError
                                message={errors['personal_info.hearing_source_other'] || stepErrors['personal_info.hearing_source_other']}
                                className="mt-2"
                            />
                        </div>
                    )}
                </div>
            </div>

            {/* ── Section C ──────────────────────────────────────────────── */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                    To Be Completed by Parent/Legal Guardian
                </h4>
                <p className="text-xs text-gray-500 mb-4 italic">
                    Person so far responsible for financing the education of the applicant.
                </p>
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <RequiredLabel htmlFor="guardian_surname" value="21. Surname" required />
                        <TextInput id="guardian_surname" className="mt-1 block w-full uppercase"
                            value={gi.guardian_surname}
                            onChange={(e) => updateSection('guardian_info', 'guardian_surname', e.target.value)}
                            disabled={isLocked} required />
                        <InputError message={errors['guardian_info.guardian_surname'] || stepErrors['guardian_info.guardian_surname']} className="mt-2" />
                    </div>
                    <div>
                        <InputLabel htmlFor="guardian_other_names" value="Other Name(s)" />
                        <TextInput id="guardian_other_names" className="mt-1 block w-full uppercase"
                            value={gi.guardian_other_names}
                            onChange={(e) => updateSection('guardian_info', 'guardian_other_names', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div className="md:col-span-2">
                        <InputLabel htmlFor="guardian_address" value="22. Address" />
                        <TextInput id="guardian_address" className="mt-1 block w-full"
                            value={gi.guardian_address}
                            onChange={(e) => updateSection('guardian_info', 'guardian_address', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div>
                        <RequiredLabel htmlFor="guardian_telephone" value="Telephone" required />
                        <TextInput id="guardian_telephone" className="mt-1 block w-full"
                            value={gi.guardian_telephone}
                            onChange={(e) => updateSection('guardian_info', 'guardian_telephone', e.target.value)}
                            disabled={isLocked} required />
                        <InputError message={errors['guardian_info.guardian_telephone'] || stepErrors['guardian_info.guardian_telephone']} className="mt-2" />
                    </div>
                    <div>
                        <InputLabel htmlFor="guardian_district" value="23. District of Residence" />
                        <TextInput id="guardian_district" className="mt-1 block w-full uppercase"
                            value={gi.guardian_district}
                            onChange={(e) => updateSection('guardian_info', 'guardian_district', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div>
                        <InputLabel htmlFor="guardian_region" value="Region of Residence" />
                        <TextInput id="guardian_region" className="mt-1 block w-full uppercase"
                            value={gi.guardian_region}
                            onChange={(e) => updateSection('guardian_info', 'guardian_region', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div>
                        <InputLabel htmlFor="guardian_occupation" value="24. Occupation" />
                        <TextInput id="guardian_occupation" className="mt-1 block w-full"
                            value={gi.guardian_occupation}
                            onChange={(e) => updateSection('guardian_info', 'guardian_occupation', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div>
                        <RequiredLabel htmlFor="guardian_relation" value="Relationship with Applicant" required />
                        <TextInput id="guardian_relation" className="mt-1 block w-full"
                            value={gi.guardian_relation}
                            onChange={(e) => updateSection('guardian_info', 'guardian_relation', e.target.value)}
                            disabled={isLocked} required />
                        <InputError message={errors['guardian_info.guardian_relation'] || stepErrors['guardian_info.guardian_relation']} className="mt-2" />
                    </div>
                </div>
            </div>

            {/* ── Section D ──────────────────────────────────────────────── */}
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                    Criminal Offence Declaration
                </h4>
                <div className="space-y-4">
                    <div>
                        <RequiredLabel
                            htmlFor="criminal_offence"
                            value="25. Have you ever been Charged and/or Convicted of a criminal offence?"
                            required
                        />
                        <div className="mt-2 flex gap-6">
                            <RadioField name="criminal_offence" value="yes" label="YES"
                                checked={decl.criminal_offence === 'yes'}
                                onChange={(v) => updateSection('declaration_info', 'criminal_offence', v)}
                                disabled={isLocked} />
                            <RadioField name="criminal_offence" value="no" label="NO"
                                checked={decl.criminal_offence === 'no'}
                                onChange={(v) => updateSection('declaration_info', 'criminal_offence', v)}
                                disabled={isLocked} />
                        </div>
                        <InputError message={errors['declaration_info.criminal_offence']} className="mt-2" />
                    </div>
                    {decl.criminal_offence === 'yes' && (
                        <div>
                            <InputLabel htmlFor="criminal_details"
                                value="If so, please state the Charge/Conviction and elaborate on the circumstances and outcome." />
                            <textarea
                                id="criminal_details"
                                rows={5}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                value={decl.criminal_details}
                                onChange={(e) => updateSection('declaration_info', 'criminal_details', e.target.value)}
                                disabled={isLocked}
                                placeholder="Describe the charge/conviction, circumstances and outcome..." />
                        </div>
                    )}
                </div>
            </div>

            {/* ── Declaration notice ─────────────────────────────────────── */}
            <div className="rounded-md border border-amber-300 bg-amber-50 p-5 text-sm text-amber-900">
                <p className="font-bold text-base mb-3">DECLARATION</p>
                <p className="mb-3 leading-relaxed">
                    The accuracy of the information you provide is fundamental to a fair and transparent assessment of
                    your eligibility for student financial aid. Please read the following declaration carefully before
                    proceeding to the final review and submission of your application.
                </p>
                <p className="mb-3 leading-relaxed italic">
                    I hereby solemnly declare that all information provided in this application is, to the best of my
                    knowledge and belief, true, complete, and accurate in every material respect. I have not wilfully
                    omitted, concealed, or misrepresented any fact relevant to this application.
                </p>
                <div className="rounded border border-amber-300 bg-amber-100 p-3 text-xs text-amber-800 leading-relaxed">
                    <strong>Important Notice:</strong> Any deliberate misrepresentation, falsification, or omission of
                    material information in this application shall render the application null and void. Any scholarship
                    award made on the basis of such misrepresentation shall be immediately withdrawn and must be fully
                    refunded by the applicant. The applicant may further be subject to disciplinary or legal proceedings.
                    By submitting this application, you confirm your acceptance of this declaration in full.
                </div>
            </div>
        </div>
    );
}
