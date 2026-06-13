import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import { RequiredLabel, RadioField } from './FormComponents';

export default function StepSectionCD({ data, errors, stepErrors, updateSection, isLocked }) {
    const gi   = data.guardian_info;
    const decl = data.declaration_info;

    return (
        <div className="space-y-6">
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
            <div className="rounded-md border border-gray-300 bg-gray-50 p-4 text-sm text-gray-700">
                <p className="font-semibold mb-2">Declaration</p>
                <p className="mb-2">
                    It is important that your eligibility for student financial aid be based upon accurate information.
                </p>
                <p className="mb-2 italic">I do hereby declare that all the information given above is true.</p>
                <p className="text-xs text-gray-500">
                    <strong>Note:</strong> Misrepresentation in any material form renders the application null and void. Any award made based on misrepresentation shall be withdrawn or refunded by the applicant, and he/she may be prosecuted. The truth, rather than lies, will get you Financial Aid.
                </p>
            </div>
        </div>
    );
}
