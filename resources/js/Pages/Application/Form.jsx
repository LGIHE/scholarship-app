import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import { useEffect, useMemo, useRef, useState } from 'react';

import { useFormDefaults } from './useFormDefaults';
import Step1PersonalInfo        from './Step1PersonalInfo';
import Step2Disability          from './Step2Disability';
import Step3Dependants          from './Step3Dependants';
import Step4Motivation          from './Step4Motivation';
import Step5Documents           from './Step5Documents';
import Step6GuardianDeclaration from './Step6GuardianDeclaration';
import Step7Review              from './Step7Review';

const STEP_CONFIG = [
    { id: 1, title: 'Step 1',  description: 'Personal background & education info' },
    { id: 2, title: 'Step 2',  description: 'Disability information' },
    { id: 3, title: 'Step 3',  description: 'Dependants information' },
    { id: 4, title: 'Step 4',  description: 'Motivation essay' },
    { id: 5, title: 'Step 5',  description: 'Upload required documents' },
    { id: 6, title: 'Step 6',  description: 'Parent/guardian & declaration' },
    { id: 7, title: 'Step 7',  description: 'Summary and final submission' },
];

function countWords(text) {
    const normalized = (text || '').trim();
    if (!normalized) return 0;
    return normalized.split(/\s+/).length;
}

export default function Form() {
    const { auth, application } = usePage().props;

    const initialData = useFormDefaults(auth, application);
    const { data, setData, post, processing, errors } = useForm(initialData);

    const [activeStep, setActiveStep]   = useState(1);
    const [savingDraft, setSavingDraft] = useState(false);
    const [draftMessage, setDraftMessage] = useState('');
    const [stepErrors, setStepErrors]   = useState({});

    const initialRender = useRef(true);
    const hasChanged    = useRef(false);

    const isLocked = ['submitted', 'under_review', 'approved', 'rejected'].includes(application?.status);

    const statusLabels = {
        draft: 'Draft', submitted: 'Submitted', under_review: 'Under Review',
        approved: 'Approved', rejected: 'Rejected',
    };

    // ── Shared state updaters ──────────────────────────────────────────────────

    const updateSection = (section, field, value) => {
        hasChanged.current = true;
        setData(section, { ...data[section], [field]: value });
        const fieldKey = `${section}.${field}`;
        if (stepErrors[fieldKey]) {
            setStepErrors((prev) => { const n = { ...prev }; delete n[fieldKey]; return n; });
        }
    };

    const updateNextOfKin = (index, field, value) => {
        hasChanged.current = true;
        const updated = [
            ...(data.personal_info.next_of_kin || [
                { name: '', relationship: '', telephone: '' },
                { name: '', relationship: '', telephone: '' },
            ]),
        ];
        updated[index] = { ...updated[index], [field]: value };
        setData('personal_info', { ...data.personal_info, next_of_kin: updated });
    };

    // ── Draft saving ───────────────────────────────────────────────────────────

    const saveDraft = async (message = 'Draft saved successfully.') => {
        if (isLocked) return;
        setSavingDraft(true);
        setDraftMessage('Saving draft...');
        try {
            const formData = new FormData();
            formData.append('personal_info',    JSON.stringify(data.personal_info));
            formData.append('disability_info',  JSON.stringify(data.disability_info));
            formData.append('dependants_info',  JSON.stringify(data.dependants_info));
            formData.append('financial_info',   JSON.stringify(data.financial_info));
            formData.append('essay',            JSON.stringify(data.essay));
            formData.append('guardian_info',    JSON.stringify(data.guardian_info));
            formData.append('declaration_info', JSON.stringify(data.declaration_info));

            const docFields = ['exam_results','national_id','birth_certificate','admission_letter',
                               'recommendation_lc1','recommendation_school','refugee_number'];
            docFields.forEach((key) => {
                const doc = data.documents[key];
                if (doc && typeof doc !== 'string') formData.append(`documents[${key}]`, doc);
            });

            await window.axios.post(route('application.draft'), formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            setDraftMessage(message);
        } catch {
            setDraftMessage('Unable to save draft right now. Please try again.');
        } finally {
            setSavingDraft(false);
        }
    };

    // Auto-save 1.2 s after any change
    useEffect(() => {
        if (isLocked) return;
        if (initialRender.current) { initialRender.current = false; return; }
        if (!hasChanged.current) return;
        const timer = setTimeout(() => { void saveDraft('Draft auto-saved.'); }, 1200);
        return () => clearTimeout(timer);
    }, [
        data.essay, data.disability_info, data.dependants_info, data.financial_info,
        data.guardian_info, data.personal_info, data.declaration_info,
        data.documents, isLocked,
    ]);

    // ── Step validation ────────────────────────────────────────────────────────

    const validateStep = (step) => {
        const errs = {};
        if (step === 1) {
            if (!data.personal_info.surname?.trim())            errs['personal_info.surname']            = 'Surname is required';
            if (!data.personal_info.other_names?.trim())        errs['personal_info.other_names']        = 'Other name(s) are required';
            if (!data.personal_info.date_of_birth?.trim())      errs['personal_info.date_of_birth']      = 'Date of birth is required';
            if (!data.personal_info.nin?.trim())                errs['personal_info.nin']                = 'National Identification Number (NIN) is required';
            if (!data.personal_info.phone?.trim())              errs['personal_info.phone']              = 'Telephone number is required';
            if (!data.personal_info.marital_status)             errs['personal_info.marital_status']     = 'Marital status is required';
            if (!data.personal_info.is_ugandan)                 errs['personal_info.is_ugandan']         = 'Nationality is required';
            if (!data.personal_info.academic_programme?.trim()) errs['personal_info.academic_programme'] = 'Academic programme is required';
            if (!data.personal_info.institution?.trim())        errs['personal_info.institution']        = 'Institution is required';
        } else if (step === 2) {
            // Only required when applicant has indicated they have a disability
            if (data.personal_info.has_disability === 'yes') {
                const di = data.disability_info;
                const disabilityTypes = [
                    'difficulty_walking','difficulty_seeing','difficulty_hearing',
                    'difficulty_communicating','difficulty_picking','difficulty_self_care','difficulty_emotions',
                ];
                const anyDisabilityTicked = disabilityTypes.some((f) => di[f]);
                if (!anyDisabilityTicked) {
                    errs['disability_info.disability_type'] = 'Please tick at least one form of disability (Q16)';
                }
                if (!di.functionality_level) {
                    errs['disability_info.functionality_level'] = 'Functionality level is required (Q17)';
                }
            }
        } else if (step === 3) {
            const isMarried = ['Married', 'Cohabiting / living with a partner'].includes(data.personal_info.marital_status);
            if (isMarried) {
                const dep = data.dependants_info;
                if (!dep.spouse_surname?.trim())         errs['dependants_info.spouse_surname']        = 'Spouse surname is required (19a)';
                if (!dep.spouse_other_names?.trim())     errs['dependants_info.spouse_other_names']    = 'Spouse other name(s) are required (19a)';
                if (!dep.spouse_education_level?.trim()) errs['dependants_info.spouse_education_level']= 'Spouse level of education is required (19a)';
                if (!dep.spouse_occupation?.trim())      errs['dependants_info.spouse_occupation']     = 'Spouse occupation is required (19a)';
                if (!dep.marriage_balance_plan?.trim())  errs['dependants_info.marriage_balance_plan'] = 'Marriage/school balance plan is required (19a)';
            }
            // Financial info always required on step 3
            const fi = data.financial_info;
            if (fi.household_income === '' || fi.household_income === null || fi.household_income === undefined) {
                errs['financial_info.household_income']    = 'Estimated annual household income is required';
            }
            if (fi.number_of_dependents === '' || fi.number_of_dependents === null || fi.number_of_dependents === undefined) {
                errs['financial_info.number_of_dependents'] = 'Number of dependents is required';
            }
            if (!fi.income_source?.trim()) errs['financial_info.income_source']          = 'Primary source of household income is required';
            if (!fi.other_financial_support?.trim()) errs['financial_info.other_financial_support'] = 'Other financial support field is required (enter "None" if not applicable)';
        } else if (step === 4) {
            if (!data.essay.motivation?.trim() || countWords(data.essay.motivation) < 50) {
                errs['essay.motivation'] = 'Motivation essay is required (minimum 50 words, target 250 words)';
            }
        } else if (step === 5) {
            ['exam_results', 'national_id'].forEach((key) => {
                const doc = data.documents[key];
                if (!(doc && (typeof doc === 'string' || doc instanceof File))) {
                    errs[`documents.${key}`] = `${key.replace(/_/g, ' ')} is required`;
                }
            });
        } else if (step === 6) {
            if (!data.guardian_info.guardian_surname?.trim())   errs['guardian_info.guardian_surname']   = 'Guardian surname is required';
            if (!data.guardian_info.guardian_telephone?.trim()) errs['guardian_info.guardian_telephone'] = 'Guardian telephone is required';
            if (!data.guardian_info.guardian_relation?.trim())  errs['guardian_info.guardian_relation']  = 'Guardian relationship is required';
        }
        return errs;
    };

    const isStepComplete = (stepId) => {
        if (stepId === 7) return isLocked;
        return Object.keys(validateStep(stepId)).length === 0;
    };

    const stepsWithErrors = useMemo(() => {
        const steps = new Set();
        Object.keys(errors).forEach((key) => {
            if (key.startsWith('personal_info.') || key.startsWith('personal_info[')) steps.add(1);
            else if (key.startsWith('disability_info.'))                              steps.add(2);
            else if (key.startsWith('dependants_info.') || key.startsWith('financial_info.')) steps.add(3);
            else if (key.startsWith('essay.'))                                        steps.add(4);
            else if (key.startsWith('documents.'))                                    steps.add(5);
            else if (key.startsWith('guardian_info.') || key.startsWith('declaration_info.')) steps.add(6);
        });
        // Also include steps that have local stepErrors
        Object.keys(stepErrors).forEach((key) => {
            if (key.startsWith('personal_info.'))                                     steps.add(1);
            else if (key.startsWith('disability_info.'))                              steps.add(2);
            else if (key.startsWith('dependants_info.') || key.startsWith('financial_info.')) steps.add(3);
            else if (key.startsWith('essay.'))                                        steps.add(4);
            else if (key.startsWith('documents.'))                                    steps.add(5);
            else if (key.startsWith('guardian_info.') || key.startsWith('declaration_info.')) steps.add(6);
        });
        return steps;
    }, [errors, stepErrors]);

    const getStepStatusColor = (stepId) => {
        if (stepId === activeStep)       return 'border-emerald-500 bg-emerald-50';
        if (stepsWithErrors.has(stepId)) return 'border-red-300 bg-red-50 hover:border-red-400';

        // Step 2: orange if disability is flagged AND required fields are not yet complete
        if (stepId === 2 && data.personal_info.has_disability === 'yes' && !isStepComplete(2)) {
            return 'border-orange-400 bg-orange-50 hover:border-orange-500';
        }

        // Step 3: orange if married/cohabiting AND required fields are not yet complete
        if (stepId === 3 && ['Married', 'Cohabiting / living with a partner'].includes(data.personal_info.marital_status) && !isStepComplete(3)) {
            return 'border-orange-400 bg-orange-50 hover:border-orange-500';
        }

        if (isStepComplete(stepId)) return 'border-green-300 bg-green-50 hover:border-green-400';
        return 'border-gray-300 bg-gray-50 hover:border-gray-400';
    };

    // ── Navigation ─────────────────────────────────────────────────────────────

    const nextStep = () => {
        if (activeStep === STEP_CONFIG.length) return;
        const errs = validateStep(activeStep);
        setStepErrors(errs);
        if (Object.keys(errs).length > 0) {
            setDraftMessage('Please fill in all required fields before proceeding.');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }
        if (!isLocked) void saveDraft('Draft saved.');
        setActiveStep((c) => c + 1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const previousStep = () => {
        if (activeStep === 1) return;
        setActiveStep((c) => c - 1);
    };

    // ── Submission ─────────────────────────────────────────────────────────────

    const submit = (event) => {
        event.preventDefault();
        const submitData = { ...data };
        const docFields  = ['exam_results','national_id','birth_certificate','admission_letter',
                            'recommendation_lc1','recommendation_school','refugee_number'];
        const newDocs = {};
        let hasNew = false;
        docFields.forEach((key) => {
            const doc = data.documents[key];
            if (doc && typeof doc !== 'string') { newDocs[key] = doc; hasNew = true; }
        });
        if (hasNew) submitData.documents = newDocs;
        else delete submitData.documents;

        post(route('application.submit'), submitData, {
            forceFormData: true,
            preserveScroll: false,
            onSuccess: () => setDraftMessage('Application submitted successfully.'),
            onError:   () => {
                setDraftMessage('Error submitting application. Please check all required fields.');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
        });
    };

    // ── Shared props passed to every step component ────────────────────────────
    const stepProps = { data, errors, stepErrors, updateSection, isLocked };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800">
                            Leaders in Teaching – Scholarship Application Form
                        </h2>
                        <p className="text-sm text-gray-500">Female STEM Student Teachers' Scholarship 2026/2027</p>
                    </div>
                    <span className="rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700">
                        Status: {statusLabels[application?.status || 'draft'] || 'Draft'}
                    </span>
                </div>
            }
        >
            <Head title="Scholarship Application" />

            <div className="py-12">
                <div className="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">

                    {/* Guiding note */}
                    <div className="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        <p className="font-semibold mb-1">Guiding Note</p>
                        <ul className="list-disc pl-5 space-y-1">
                            <li>Complete every section of the Application Form in CAPITAL LETTERS.</li>
                            <li>Provide clear addresses, e-mail address and telephone numbers through which you can be easily reached.</li>
                            <li><strong>SUBMIT YOUR APPLICATION NOT LATER THAN 15th July 2026.</strong> No applications will be accepted after this date.</li>
                        </ul>
                    </div>

                    <div className="overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                        <div className="mb-4 flex flex-col gap-1">
                            <h3 className="text-lg font-semibold text-gray-900">
                                University Education Scholarships – Application Form
                            </h3>
                            {isLocked && (
                                <p className="rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                                    This application is already submitted. You can review details but cannot edit fields.
                                </p>
                            )}
                        </div>

                        {/* Progress bar */}
                        <div className="mb-4 h-2 w-full rounded-full bg-gray-200">
                            <div
                                className="h-full rounded-full bg-emerald-600 transition-all duration-300"
                                style={{ width: `${isLocked ? 100 : (activeStep / STEP_CONFIG.length) * 100}%` }}
                            />
                        </div>

                        {/* Step tabs */}
                        <div className="mb-8 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-7">
                            {STEP_CONFIG.map((step) => (
                                <button
                                    key={step.id}
                                    type="button"
                                    onClick={() => setActiveStep(step.id)}
                                    className={'relative rounded-md border px-3 py-3 text-left transition ' + getStepStatusColor(step.id)}
                                >
                                    {stepsWithErrors.has(step.id) && (
                                        <div className="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">!</div>
                                    )}
                                    <div className={`text-xs font-semibold uppercase tracking-wide ${stepsWithErrors.has(step.id) ? 'text-red-600' : 'text-gray-500'}`}>
                                        Step {step.id}
                                    </div>
                                    <div className="text-sm font-semibold text-gray-900">{step.title}</div>
                                    <div className="mt-1 text-xs text-gray-600">{step.description}</div>
                                </button>
                            ))}
                        </div>

                        <form onSubmit={submit}>
                            {/* Error summary banner */}
                            {(Object.keys(errors).length > 0 || Object.keys(stepErrors).length > 0) && (
                                <div className="mb-6 rounded-lg border border-red-300 bg-red-50 p-4">
                                    <h3 className="text-sm font-semibold text-red-800">Please fix the following errors:</h3>
                                    <ul className="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                                        {Object.entries({ ...errors, ...stepErrors }).map(([key, message]) => (
                                            <li key={key}>{message}</li>
                                        ))}
                                    </ul>
                                </div>
                            )}

                            <AnimatePresence mode="wait">
                                <motion.div
                                    key={activeStep}
                                    initial={{ opacity: 0, x: 20 }}
                                    animate={{ opacity: 1, x: 0 }}
                                    exit={{ opacity: 0, x: -20 }}
                                    transition={{ duration: 0.2 }}
                                    className="space-y-6"
                                >
                                    {activeStep === 1 && <Step1PersonalInfo        {...stepProps} updateNextOfKin={updateNextOfKin} />}
                                    {activeStep === 2 && <Step2Disability          {...stepProps} />}
                                    {activeStep === 3 && <Step3Dependants          {...stepProps} />}
                                    {activeStep === 4 && <Step4Motivation          {...stepProps} />}
                                    {activeStep === 5 && <Step5Documents           {...stepProps} setData={setData} hasChanged={hasChanged} />}
                                    {activeStep === 6 && <Step6GuardianDeclaration {...stepProps} />}
                                    {activeStep === 7 && <Step7Review              data={data} />}
                                </motion.div>
                            </AnimatePresence>

                            {/* Navigation bar */}
                            <div className="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 pt-6">
                                <div className="text-sm text-gray-600">
                                    {savingDraft ? 'Saving draft...' : draftMessage}
                                </div>
                                <div className="flex flex-wrap gap-2">
                                    <Link href={route('portal')}>
                                        <SecondaryButton type="button">Back to Dashboard</SecondaryButton>
                                    </Link>
                                    {activeStep > 1 && (
                                        <SecondaryButton type="button" onClick={previousStep}>Previous</SecondaryButton>
                                    )}
                                    {!isLocked && (
                                        <SecondaryButton type="button" onClick={() => saveDraft('Draft saved successfully.')} disabled={savingDraft}>
                                            Save Draft
                                        </SecondaryButton>
                                    )}
                                    {activeStep < STEP_CONFIG.length ? (
                                        <PrimaryButton type="button" onClick={(e) => { e.preventDefault(); nextStep(); }}>
                                            {activeStep === 6 ? 'Review & Submit' : 'Next Step'}
                                        </PrimaryButton>
                                    ) : (
                                        <PrimaryButton type="submit" disabled={processing || isLocked}>
                                            {processing ? 'Submitting...' : isLocked ? 'Already Submitted' : 'Submit Application'}
                                        </PrimaryButton>
                                    )}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
