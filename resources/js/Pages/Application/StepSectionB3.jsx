import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';

export default function StepSectionB3({ data, updateSection, isLocked }) {
    const dep = data.dependants_info;

    return (
        <div className="space-y-6">
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                    Section B3 – To Be Filled by Applicants with Dependants
                </h4>

                {/* 19a – Spouse */}
                <h5 className="font-semibold text-gray-700 mb-3">
                    19a. If married/cohabiting, provide the following information about your spouse/partner
                </h5>
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 mb-6">
                    <div>
                        <InputLabel htmlFor="spouse_surname" value="Spouse Surname" />
                        <TextInput id="spouse_surname" className="mt-1 block w-full uppercase"
                            value={dep.spouse_surname}
                            onChange={(e) => updateSection('dependants_info', 'spouse_surname', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div>
                        <InputLabel htmlFor="spouse_other_names" value="Spouse Other Name(s)" />
                        <TextInput id="spouse_other_names" className="mt-1 block w-full uppercase"
                            value={dep.spouse_other_names}
                            onChange={(e) => updateSection('dependants_info', 'spouse_other_names', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div>
                        <InputLabel htmlFor="spouse_education_level" value="Level of Education" />
                        <TextInput id="spouse_education_level" className="mt-1 block w-full"
                            value={dep.spouse_education_level}
                            onChange={(e) => updateSection('dependants_info', 'spouse_education_level', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div>
                        <InputLabel htmlFor="spouse_occupation" value="Occupation" />
                        <TextInput id="spouse_occupation" className="mt-1 block w-full"
                            value={dep.spouse_occupation}
                            onChange={(e) => updateSection('dependants_info', 'spouse_occupation', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div className="md:col-span-2">
                        <InputLabel htmlFor="marriage_balance_plan"
                            value="How do you plan to ensure that you strike a balance between marriage and school obligations?" />
                        <textarea rows={3}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            value={dep.marriage_balance_plan}
                            onChange={(e) => updateSection('dependants_info', 'marriage_balance_plan', e.target.value)}
                            disabled={isLocked} />
                    </div>
                </div>

                {/* 19b – Children */}
                <h5 className="font-semibold text-gray-700 mb-3">
                    19b. If you are a mother, provide the following information about your children
                </h5>
                <div className="grid grid-cols-1 gap-4 md:grid-cols-3 mb-4">
                    <div>
                        <InputLabel htmlFor="num_children" value="How many children do you have?" />
                        <TextInput id="num_children" type="number" min="0" className="mt-1 block w-full"
                            value={dep.num_children}
                            onChange={(e) => updateSection('dependants_info', 'num_children', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div>
                        <InputLabel htmlFor="oldest_child_age" value="Age of oldest child" />
                        <TextInput id="oldest_child_age" type="number" min="0" className="mt-1 block w-full"
                            value={dep.oldest_child_age}
                            onChange={(e) => updateSection('dependants_info', 'oldest_child_age', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div>
                        <InputLabel htmlFor="youngest_child_age" value="Age of youngest child" />
                        <TextInput id="youngest_child_age" type="number" min="0" className="mt-1 block w-full"
                            value={dep.youngest_child_age}
                            onChange={(e) => updateSection('dependants_info', 'youngest_child_age', e.target.value)}
                            disabled={isLocked} />
                    </div>
                </div>
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <InputLabel htmlFor="childcare_plan"
                            value="How do you plan to manage taking care of the children while pursuing your studies?" />
                        <textarea rows={3}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            value={dep.childcare_plan}
                            onChange={(e) => updateSection('dependants_info', 'childcare_plan', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div>
                        <InputLabel htmlFor="spouse_support" value="What kind of support do you get from your spouse?" />
                        <textarea rows={3}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            value={dep.spouse_support}
                            onChange={(e) => updateSection('dependants_info', 'spouse_support', e.target.value)}
                            disabled={isLocked} />
                    </div>
                    <div className="md:col-span-2">
                        <InputLabel htmlFor="non_financial_support_needed"
                            value="What kind of non-financial support do you need as a mother to enable you pursue your studies?" />
                        <textarea rows={3}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                            value={dep.non_financial_support_needed}
                            onChange={(e) => updateSection('dependants_info', 'non_financial_support_needed', e.target.value)}
                            disabled={isLocked} />
                    </div>
                </div>
            </div>
        </div>
    );
}
