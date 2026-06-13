import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import { CheckboxField, RadioField } from './FormComponents';

export default function StepSectionB2({ data, updateSection, isLocked }) {
    const di = data.disability_info;

    return (
        <div className="space-y-6">
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                    For Students with Disabilities
                </h4>
                <p className="text-sm text-gray-500 mb-4 italic">
                    Complete this section only if you indicated that you have a disability. If you have no disability, you may proceed to the next section.
                </p>

                {/* Q16 – Disability types */}
                <h5 className="font-semibold text-gray-700 mb-3">16. Specify the form of disability you have (Tick where applicable)</h5>
                <div className="grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-3 mb-6">
                    {[
                        ['difficulty_walking', 'Difficulty walking'],
                        ['difficulty_seeing', 'Difficulty seeing'],
                        ['difficulty_hearing', 'Difficulty hearing'],
                        ['difficulty_communicating', 'Difficulty communicating'],
                        ['difficulty_picking', 'Difficulty picking objects with hands'],
                        ['difficulty_self_care', 'Difficulty self-care'],
                        ['difficulty_emotions', 'Difficulty controlling emotions'],
                    ].map(([field, label]) => (
                        <CheckboxField key={field} id={field} label={label}
                            checked={di[field]}
                            onChange={(v) => updateSection('disability_info', field, v)}
                            disabled={isLocked} />
                    ))}
                </div>

                {/* Q17 – Functionality level */}
                <h5 className="font-semibold text-gray-700 mb-3">17. Level of functionality based on difficulty ticked</h5>
                <div className="flex flex-wrap gap-4 mb-6">
                    {['Some difficulty', 'A lot of difficulty', 'Cannot do at all'].map((level) => (
                        <RadioField key={level} name="functionality_level" value={level} label={level}
                            checked={di.functionality_level === level}
                            onChange={(v) => updateSection('disability_info', 'functionality_level', v)}
                            disabled={isLocked} />
                    ))}
                </div>

                {/* Q18 – Family members with disability */}
                <h5 className="font-semibold text-gray-700 mb-3">18. Indicate any other member of your family with disabilities</h5>
                <div className="flex flex-wrap gap-4 mb-3">
                    <CheckboxField id="family_father" label="Father"
                        checked={di.family_disability_father}
                        onChange={(v) => updateSection('disability_info', 'family_disability_father', v)}
                        disabled={isLocked} />
                    <CheckboxField id="family_mother" label="Mother"
                        checked={di.family_disability_mother}
                        onChange={(v) => updateSection('disability_info', 'family_disability_mother', v)}
                        disabled={isLocked} />
                    <CheckboxField id="family_siblings" label="Sibling(s)"
                        checked={di.family_disability_siblings}
                        onChange={(v) => updateSection('disability_info', 'family_disability_siblings', v)}
                        disabled={isLocked} />
                </div>
                {di.family_disability_siblings && (
                    <div className="grid grid-cols-2 gap-4 mb-6 max-w-xs">
                        <div>
                            <InputLabel htmlFor="siblings_female" value="No. of Female Siblings" />
                            <TextInput id="siblings_female" type="number" min="0" className="mt-1 block w-full"
                                value={di.siblings_female_count}
                                onChange={(e) => updateSection('disability_info', 'siblings_female_count', e.target.value)}
                                disabled={isLocked} />
                        </div>
                        <div>
                            <InputLabel htmlFor="siblings_male" value="No. of Male Siblings" />
                            <TextInput id="siblings_male" type="number" min="0" className="mt-1 block w-full"
                                value={di.siblings_male_count}
                                onChange={(e) => updateSection('disability_info', 'siblings_male_count', e.target.value)}
                                disabled={isLocked} />
                        </div>
                    </div>
                )}

                {/* Assistive support */}
                <div>
                    <h5 className="font-semibold text-gray-700 mb-2">
                        18. Indicate the kind of assistive support/reasonable accommodation you may require to aid safe participation while studying
                    </h5>
                    <textarea rows={4}
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                        value={di.assistive_support}
                        onChange={(e) => updateSection('disability_info', 'assistive_support', e.target.value)}
                        disabled={isLocked}
                        placeholder="Describe any assistive support or reasonable accommodation needed..." />
                </div>
            </div>
        </div>
    );
}
