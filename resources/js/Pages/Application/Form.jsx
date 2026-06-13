import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import { useEffect, useMemo, useRef, useState } from 'react';

import { useFormDefaults } from './useFormDefaults';
import StepSectionA  from './StepSectionA';
import StepSectionB2 from './StepSectionB2';
import StepSectionB3 from './StepSectionB3';
import StepSectionB6 from './StepSectionB6';
import StepDocuments from './StepDocuments';
import StepSectionCD from './StepSectionCD';
import StepReview    from './StepReview';

const STEP_CONFIG = [
    { id: 1, title: 'Section A',    description: 'Personal background & education info' },
    { id: 2, title: 'Section B2',   description: 'Disability information' },
    { id: 3, title: 'Section B3',   description: 'Dependants information' },
    { id: 4, title: 'Section B6',   description: 'Motivation essay' },
    { id: 5, title: 'Documents',    description: 'Upload required documents' },
    { id: 6, title: 'Section C & D', description: 'Parent/guardian & declaration' },
    { id: 7, title: 'Review & Submit', description: 'Summary and final submission' },
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
        data.essay, data.disability_info, data.dependants_info,
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
            if (!data.personal_info.phone?.trim())              errs['personal_info.phone']              = 'Telephone number is required';
            if (!data.personal_info.marital_status)             errs['personal_info.marital_status']     = 'Marital status is required';
            if (!data.personal_info.is_ugandan)                 errs['personal_info.is_ugandan']         = 'Nationality is required';
            if (!data.personal_info.academic_programme?.trim()) errs['personal_info.academic_programme'] = 'Academic programme is required';
            if (!data.personal_info.institution?.trim())        errs['personal_info.institution']        = 'Institution is required';
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
            else if (key.startsWith('dependants_info.'))                              steps.add(3);
            else if (key.startsWith('essay.'))                                        steps.add(4);
            else if (key.startsWith('documents.'))                                    steps.add(5);
            else if (key.startsWith('guardian_info.') || key.startsWith('declaration_info.')) steps.add(6);
        });
        return steps;
    }, [errors]);

    const getStepStatusColor = (stepId) => {
        if (stepId === activeStep)           return 'border-emerald-500 bg-emerald-50';
        if (stepsWithErrors.has(stepId))     return 'border-red-300 bg-red-50 hover:border-red-400';
        if (isStepComplete(stepId))          return 'border-green-300 bg-green-50 hover:border-green-400';
        return 'border-orange-300 bg-orange-50 hover:border-orange-400';
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
                                    {activeStep === 1 && <StepSectionA  {...stepProps} updateNextOfKin={updateNextOfKin} />}
                                    {activeStep === 2 && <StepSectionB2 {...stepProps} />}
                                    {activeStep === 3 && <StepSectionB3 {...stepProps} />}
                                    {activeStep === 4 && <StepSectionB6 {...stepProps} />}
                                    {activeStep === 5 && <StepDocuments {...stepProps} setData={setData} hasChanged={hasChanged} />}
                                    {activeStep === 6 && <StepSectionCD {...stepProps} />}
                                    {activeStep === 7 && <StepReview    data={data} />}
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
                                            {activeStep === 6 ? 'Proceed to Review & Submit' : 'Next Step'}
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
