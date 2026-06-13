import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import { useEffect, useMemo, useRef, useState } from 'react';

// Helper component for required labels
const RequiredLabel = ({ htmlFor, value, required = false }) => (
    <InputLabel
        htmlFor={htmlFor}
        value={
            <span>
                {value}
                {required && <span className="ml-1 text-red-500">*</span>}
            </span>
        }
    />
);

// Reusable checkbox component
const CheckboxField = ({ id, label, checked, onChange, disabled }) => (
    <label className="flex items-center gap-2 cursor-pointer">
        <input
            type="checkbox"
            id={id}
            checked={!!checked}
            onChange={(e) => onChange(e.target.checked)}
            disabled={disabled}
            className="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
        />
        <span className="text-sm text-gray-700">{label}</span>
    </label>
);

// Reusable radio component
const RadioField = ({ name, value, label, checked, onChange, disabled }) => (
    <label className="flex items-center gap-2 cursor-pointer">
        <input
            type="radio"
            name={name}
            value={value}
            checked={checked}
            onChange={() => onChange(value)}
            disabled={disabled}
            className="h-4 w-4 border-gray-300 text-emerald-600 focus:ring-emerald-500"
        />
        <span className="text-sm text-gray-700">{label}</span>
    </label>
);

const STEP_CONFIG = [
    { id: 1, title: 'Section A', description: 'Personal background & education info' },
    { id: 2, title: 'Section B2', description: 'Disability information' },
    { id: 3, title: 'Section B3', description: 'Dependants information' },
    { id: 4, title: 'Section B6', description: 'Motivation essay' },
    { id: 5, title: 'Documents', description: 'Upload required documents' },
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

    const nameParts = (auth?.user?.name || '').trim().split(/\s+/).filter(Boolean);
    const defaultFirstName = nameParts[0] || '';
    const defaultLastName = nameParts.slice(1).join(' ');

    const defaults = useMemo(() => ({
        personal_info: {
            // Section A - Personal Information
            surname: defaultLastName,
            other_names: defaultFirstName,
            date_of_birth: '',
            nin: '',
            has_disability: '',
            disability_specify: '',
            phone: '',
            email: auth?.user?.email || '',
            marital_status: '',
            // Next of kin
            next_of_kin: [
                { name: '', relationship: '', telephone: '' },
                { name: '', relationship: '', telephone: '' },
            ],
            // Nationality
            is_ugandan: '',
            non_ugandan_explanation: '',
            // Place of birth
            birth_village: '', birth_district: '', birth_region: '', birth_country: '',
            // Place of origin
            origin_village: '', origin_district: '', origin_region: '', origin_country: '',
            // Place of residence
            residence_village: '', residence_district: '', residence_region: '', residence_country: '',
            // Study info
            academic_programme: '',
            teaching_subjects_1: '',
            teaching_subjects_2: '',
            institution: '',
            student_admission_number: '',
            // Schools attended
            primary_school_name: '', primary_school_district: '', primary_school_dates: '', primary_school_responsible: '',
            olevel_school_name: '', olevel_school_district: '', olevel_school_dates: '', olevel_school_responsible: '',
            alevel_school_name: '', alevel_school_district: '', alevel_school_dates: '', alevel_school_responsible: '',
            university_name: '', university_district: '', university_dates: '', university_responsible: '',
            // Admission mode
            alevel_school_exam: '', alevel_year: '', alevel_index: '', alevel_points: '',
            diploma_school: '', diploma_year: '', diploma_index: '', diploma_cgpa: '',
            heac_school: '', heac_year: '', heac_index: '', heac_points: '',
            mature_school: '', mature_year: '', mature_index: '', mature_points: '',
        },
        disability_info: {
            // Section B2
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
            // Section B3
            // Spouse info
            spouse_surname: '',
            spouse_other_names: '',
            spouse_education_level: '',
            spouse_occupation: '',
            marriage_balance_plan: '',
            // Children info
            num_children: '',
            oldest_child_age: '',
            youngest_child_age: '',
            childcare_plan: '',
            spouse_support: '',
            non_financial_support_needed: '',
        },
        essay: {
            // Section B6
            motivation: '',
        },
        guardian_info: {
            // Section C
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
            // Section D
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
    }), [defaultFirstName, defaultLastName, auth]);

    const initialData = useMemo(() => ({
        personal_info: { ...defaults.personal_info, ...(application?.personal_info || {}) },
        disability_info: { ...defaults.disability_info, ...(application?.disability_info || {}) },
        dependants_info: { ...defaults.dependants_info, ...(application?.dependants_info || {}) },
        essay: { ...defaults.essay, ...(application?.essay || {}) },
        guardian_info: { ...defaults.guardian_info, ...(application?.guardian_info || {}) },
        declaration_info: { ...defaults.declaration_info, ...(application?.declaration_info || {}) },
        documents: { ...defaults.documents, ...(application?.documents || {}) },
    }), [application, defaults]);

    const { data, setData, post, processing, errors } = useForm(initialData);

    const [activeStep, setActiveStep] = useState(1);
    const [savingDraft, setSavingDraft] = useState(false);
    const [draftMessage, setDraftMessage] = useState('');
    const [stepErrors, setStepErrors] = useState({});

    const initialRender = useRef(true);
    const hasChanged = useRef(false);

    const isLocked = ['submitted', 'under_review', 'approved', 'rejected'].includes(application?.status);

    const statusLabels = {
        draft: 'Draft', submitted: 'Submitted', under_review: 'Under Review',
        approved: 'Approved', rejected: 'Rejected',
    };

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
        const updated = [...(data.personal_info.next_of_kin || [{ name: '', relationship: '', telephone: '' }, { name: '', relationship: '', telephone: '' }])];
        updated[index] = { ...updated[index], [field]: value };
        setData('personal_info', { ...data.personal_info, next_of_kin: updated });
    };

    const saveDraft = async (message = 'Draft saved successfully.') => {
        if (isLocked) return;
        setSavingDraft(true);
        setDraftMessage('Saving draft...');
        try {
            const formData = new FormData();
            formData.append('personal_info', JSON.stringify(data.personal_info));
            formData.append('disability_info', JSON.stringify(data.disability_info));
            formData.append('dependants_info', JSON.stringify(data.dependants_info));
            formData.append('essay', JSON.stringify(data.essay));
            formData.append('guardian_info', JSON.stringify(data.guardian_info));
            formData.append('declaration_info', JSON.stringify(data.declaration_info));

            const docFields = ['exam_results','national_id','birth_certificate','admission_letter','recommendation_lc1','recommendation_school','refugee_number'];
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

    useEffect(() => {
        if (isLocked) return;
        if (initialRender.current) { initialRender.current = false; return; }
        if (!hasChanged.current) return;
        const timer = setTimeout(() => { void saveDraft('Draft auto-saved.'); }, 1200);
        return () => clearTimeout(timer);
    }, [data.essay, data.disability_info, data.dependants_info, data.guardian_info, data.personal_info, data.declaration_info, data.documents, isLocked]);

    const validateStep = (step) => {
        const errs = {};
        if (step === 1) {
            if (!data.personal_info.surname?.trim()) errs['personal_info.surname'] = 'Surname is required';
            if (!data.personal_info.other_names?.trim()) errs['personal_info.other_names'] = 'Other name(s) are required';
            if (!data.personal_info.date_of_birth?.trim()) errs['personal_info.date_of_birth'] = 'Date of birth is required';
            if (!data.personal_info.phone?.trim()) errs['personal_info.phone'] = 'Telephone number is required';
            if (!data.personal_info.marital_status) errs['personal_info.marital_status'] = 'Marital status is required';
            if (!data.personal_info.is_ugandan) errs['personal_info.is_ugandan'] = 'Nationality is required';
            if (!data.personal_info.academic_programme?.trim()) errs['personal_info.academic_programme'] = 'Academic programme is required';
            if (!data.personal_info.institution?.trim()) errs['personal_info.institution'] = 'Institution is required';
        } else if (step === 5) {
            const docFields = ['exam_results','national_id'];
            docFields.forEach((key) => {
                const doc = data.documents[key];
                const has = doc && (typeof doc === 'string' || doc instanceof File);
                if (!has) errs[`documents.${key}`] = `${key.replace(/_/g,' ')} is required`;
            });
        } else if (step === 4) {
            if (!data.essay.motivation?.trim() || countWords(data.essay.motivation) < 50) {
                errs['essay.motivation'] = 'Motivation essay is required (minimum 50 words, target 250 words)';
            }
        } else if (step === 6) {
            if (!data.guardian_info.guardian_surname?.trim()) errs['guardian_info.guardian_surname'] = 'Guardian surname is required';
            if (!data.guardian_info.guardian_telephone?.trim()) errs['guardian_info.guardian_telephone'] = 'Guardian telephone is required';
            if (!data.guardian_info.guardian_relation?.trim()) errs['guardian_info.guardian_relation'] = 'Guardian relationship is required';
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
            else if (key.startsWith('disability_info.')) steps.add(2);
            else if (key.startsWith('dependants_info.')) steps.add(3);
            else if (key.startsWith('essay.')) steps.add(4);
            else if (key.startsWith('documents.')) steps.add(5);
            else if (key.startsWith('guardian_info.') || key.startsWith('declaration_info.')) steps.add(6);
        });
        return steps;
    }, [errors]);

    const getStepStatusColor = (stepId) => {
        if (stepId === activeStep) return 'border-emerald-500 bg-emerald-50';
        if (stepsWithErrors.has(stepId)) return 'border-red-300 bg-red-50 hover:border-red-400';
        if (isStepComplete(stepId)) return 'border-green-300 bg-green-50 hover:border-green-400';
        return 'border-orange-300 bg-orange-50 hover:border-orange-400';
    };

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

    const submit = (event) => {
        event.preventDefault();
        const submitData = { ...data };
        const docFields = ['exam_results','national_id','birth_certificate','admission_letter','recommendation_lc1','recommendation_school','refugee_number'];
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
            onError: () => {
                setDraftMessage('Error submitting application. Please check all required fields.');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
        });
    };

    // ─── Shorthand helpers ──────────────────────────────────────────────────────
    const pi = data.personal_info;
    const di = data.disability_info;
    const dep = data.dependants_info;
    const gi = data.guardian_info;
    const decl = data.declaration_info;
    const docs = data.documents;

    const nokList = pi.next_of_kin || [
        { name: '', relationship: '', telephone: '' },
        { name: '', relationship: '', telephone: '' },
    ];

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
                            <h3 className="text-lg font-semibold text-gray-900">University Education Scholarships – Application Form</h3>
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
                            {/* Error summary */}
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
                                    {/* ══════════════════════════════════════════
                                        STEP 1 – SECTION A: PERSONAL & EDUCATION
                                    ══════════════════════════════════════════ */}
                                    {activeStep === 1 && (
                                        <div className="space-y-8">
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                                                    Section A – Applicant Background Information
                                                </h4>
                                                <p className="text-xs text-gray-500 mb-4 italic">
                                                    Complete all questions using BLOCK letters only. Your application will not be processed if you leave any questions unanswered.
                                                </p>

                                                {/* Personal Information */}
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
                                                    <div>
                                                        <InputLabel htmlFor="nin" value="National Identification Number (NIN)" />
                                                        <TextInput id="nin" className="mt-1 block w-full uppercase tracking-widest"
                                                            maxLength={14}
                                                            placeholder="e.g. CM9100012345ABCD"
                                                            value={pi.nin}
                                                            onChange={(e) => updateSection('personal_info', 'nin', e.target.value)}
                                                            disabled={isLocked} />
                                                        <InputError message={errors['personal_info.nin']} className="mt-2" />
                                                    </div>
                                                </div>

                                                {/* Disability quick question (Section A Q3) */}
                                                <div className="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                                                    <div>
                                                        <RequiredLabel htmlFor="has_disability_a" value="3. Do you have any Disability?" required />
                                                        <div className="mt-2 flex gap-6">
                                                            <RadioField name="has_disability_a" value="yes" label="YES"
                                                                checked={pi.has_disability === 'yes'} onChange={(v) => updateSection('personal_info', 'has_disability', v)} disabled={isLocked} />
                                                            <RadioField name="has_disability_a" value="no" label="NO"
                                                                checked={pi.has_disability === 'no'} onChange={(v) => updateSection('personal_info', 'has_disability', v)} disabled={isLocked} />
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

                                                {/* Contact */}
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
                                                            {['Single','Married','Cohabiting / living with a partner'].map((ms) => (
                                                                <RadioField key={ms} name="marital_status" value={ms} label={ms}
                                                                    checked={pi.marital_status === ms}
                                                                    onChange={(v) => updateSection('personal_info', 'marital_status', v)}
                                                                    disabled={isLocked} />
                                                            ))}
                                                        </div>
                                                        <InputError message={errors['personal_info.marital_status'] || stepErrors['personal_info.marital_status']} className="mt-2" />
                                                    </div>
                                                </div>

                                                {/* Next of Kin */}
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

                                                {/* Nationality */}
                                                <div className="mt-6">
                                                    <h5 className="font-semibold text-gray-700 mb-3">Nationality and Address</h5>
                                                    <div className="mb-4">
                                                        <RequiredLabel htmlFor="is_ugandan" value="6. Are you a Ugandan?" required />
                                                        <div className="mt-2 flex gap-6">
                                                            <RadioField name="is_ugandan" value="yes" label="YES"
                                                                checked={pi.is_ugandan === 'yes'} onChange={(v) => updateSection('personal_info', 'is_ugandan', v)} disabled={isLocked} />
                                                            <RadioField name="is_ugandan" value="no" label="NO"
                                                                checked={pi.is_ugandan === 'no'} onChange={(v) => updateSection('personal_info', 'is_ugandan', v)} disabled={isLocked} />
                                                        </div>
                                                        <InputError message={errors['personal_info.is_ugandan'] || stepErrors['personal_info.is_ugandan']} className="mt-2" />
                                                    </div>
                                                    {pi.is_ugandan === 'no' && (
                                                        <div className="mb-4">
                                                            <InputLabel htmlFor="non_ugandan_explanation" value="If NO, explain:" />
                                                            <textarea rows={2} className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                                                value={pi.non_ugandan_explanation}
                                                                onChange={(e) => updateSection('personal_info', 'non_ugandan_explanation', e.target.value)}
                                                                disabled={isLocked} />
                                                        </div>
                                                    )}

                                                    {/* Place of Birth */}
                                                    <div className="mb-4">
                                                        <InputLabel value="7. Place of Birth" className="font-medium" />
                                                        <div className="grid grid-cols-2 gap-3 mt-2 md:grid-cols-4">
                                                            {[['birth_village','Village/Parish/Sub-county'],['birth_district','District'],['birth_region','Region'],['birth_country','Country']].map(([field, label]) => (
                                                                <div key={field}>
                                                                    <InputLabel value={label} className="text-xs text-gray-500" />
                                                                    <TextInput className="mt-1 block w-full text-sm uppercase"
                                                                        value={pi[field] || ''}
                                                                        onChange={(e) => updateSection('personal_info', field, e.target.value)}
                                                                        disabled={isLocked} />
                                                                </div>
                                                            ))}
                                                        </div>
                                                    </div>

                                                    {/* Place of Origin */}
                                                    <div className="mb-4">
                                                        <InputLabel value="8. Place of Origin" className="font-medium" />
                                                        <div className="grid grid-cols-2 gap-3 mt-2 md:grid-cols-4">
                                                            {[['origin_village','Village/Parish/Sub-county'],['origin_district','District'],['origin_region','Region'],['origin_country','Country']].map(([field, label]) => (
                                                                <div key={field}>
                                                                    <InputLabel value={label} className="text-xs text-gray-500" />
                                                                    <TextInput className="mt-1 block w-full text-sm uppercase"
                                                                        value={pi[field] || ''}
                                                                        onChange={(e) => updateSection('personal_info', field, e.target.value)}
                                                                        disabled={isLocked} />
                                                                </div>
                                                            ))}
                                                        </div>
                                                    </div>

                                                    {/* Place of Residence */}
                                                    <div className="mb-4">
                                                        <InputLabel value="9. Place of Residence" className="font-medium" />
                                                        <div className="grid grid-cols-2 gap-3 mt-2 md:grid-cols-4">
                                                            {[['residence_village','Village/Parish/Sub-county'],['residence_district','District'],['residence_region','Region'],['residence_country','Country']].map(([field, label]) => (
                                                                <div key={field}>
                                                                    <InputLabel value={label} className="text-xs text-gray-500" />
                                                                    <TextInput className="mt-1 block w-full text-sm uppercase"
                                                                        value={pi[field] || ''}
                                                                        onChange={(e) => updateSection('personal_info', field, e.target.value)}
                                                                        disabled={isLocked} />
                                                                </div>
                                                            ))}
                                                        </div>
                                                    </div>
                                                </div>

                                                {/* Section B1 – Education */}
                                                <div className="mt-6">
                                                    <h4 className="font-semibold text-gray-800 text-base border-b pb-2 mb-4">
                                                        Section B1 – Information on Education
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

                                                    {/* Schools attended – Q14 */}
                                                    <div className="mt-6">
                                                        <h5 className="font-semibold text-gray-700 mb-3">14. Schools Attended</h5>
                                                        <div className="overflow-x-auto">
                                                            <table className="min-w-full border border-gray-200 text-sm">
                                                                <thead className="bg-gray-50">
                                                                    <tr>
                                                                        <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">Level</th>
                                                                        <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">Name of School</th>
                                                                        <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">District/Country</th>
                                                                        <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">Dates of Attendance</th>
                                                                        <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">Who was responsible for education & upkeep?</th>
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
                                                                            {['name','district','dates','responsible'].map((col) => (
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

                                                    {/* Admission mode – Q15 */}
                                                    <div className="mt-6">
                                                        <h5 className="font-semibold text-gray-700 mb-1">15. Mode of Admission to University</h5>
                                                        <p className="text-xs text-gray-500 mb-3 italic">
                                                            Use the aggregate that your admission into the University was based on. For Diploma holders provide the CGPA obtained.
                                                        </p>
                                                        <div className="overflow-x-auto">
                                                            <table className="min-w-full border border-gray-200 text-sm">
                                                                <thead className="bg-gray-50">
                                                                    <tr>
                                                                        <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">Mode</th>
                                                                        <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">School/Institution</th>
                                                                        <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">Year of Exam/Completion</th>
                                                                        <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">Candidate Index/Reg. Number</th>
                                                                        <th className="border border-gray-200 px-3 py-2 text-left font-medium text-gray-700">Points Score / CGPA</th>
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
                                                                            {['school_exam','year','index','points'].map((col) => (
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
                                    )}

                                    {/* ══════════════════════════════════════════
                                        STEP 2 – SECTION B2: DISABILITY
                                    ══════════════════════════════════════════ */}
                                    {activeStep === 2 && (
                                        <div className="space-y-6">
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                                                    Section B2 – For Students with Disabilities
                                                </h4>
                                                <p className="text-sm text-gray-500 mb-4 italic">
                                                    Complete this section only if you indicated that you have a disability. If you have no disability, you may proceed to the next section.
                                                </p>

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

                                                <h5 className="font-semibold text-gray-700 mb-3">17. Level of functionality based on difficulty ticked</h5>
                                                <div className="flex flex-wrap gap-4 mb-6">
                                                    {['Some difficulty', 'A lot of difficulty', 'Cannot do at all'].map((level) => (
                                                        <RadioField key={level} name="functionality_level" value={level} label={level}
                                                            checked={di.functionality_level === level}
                                                            onChange={(v) => updateSection('disability_info', 'functionality_level', v)}
                                                            disabled={isLocked} />
                                                    ))}
                                                </div>

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

                                                <div>
                                                    <h5 className="font-semibold text-gray-700 mb-2">18. Indicate the kind of assistive support/reasonable accommodation you may require to aid safe participation while studying</h5>
                                                    <textarea rows={4}
                                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                                        value={di.assistive_support}
                                                        onChange={(e) => updateSection('disability_info', 'assistive_support', e.target.value)}
                                                        disabled={isLocked}
                                                        placeholder="Describe any assistive support or reasonable accommodation needed..." />
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* ══════════════════════════════════════════
                                        STEP 3 – SECTION B3: DEPENDANTS
                                    ══════════════════════════════════════════ */}
                                    {activeStep === 3 && (
                                        <div className="space-y-6">
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                                                    Section B3 – To Be Filled by Applicants with Dependants
                                                </h4>

                                                <h5 className="font-semibold text-gray-700 mb-3">19a. If married/cohabiting, provide the following information about your spouse/partner</h5>
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
                                                        <InputLabel htmlFor="marriage_balance_plan" value="How do you plan to ensure that you strike a balance between marriage and school obligations?" />
                                                        <textarea rows={3}
                                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                                            value={dep.marriage_balance_plan}
                                                            onChange={(e) => updateSection('dependants_info', 'marriage_balance_plan', e.target.value)}
                                                            disabled={isLocked} />
                                                    </div>
                                                </div>

                                                <h5 className="font-semibold text-gray-700 mb-3">19b. If you are a mother, provide the following information about your children</h5>
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
                                                        <InputLabel htmlFor="childcare_plan" value="How do you plan to manage taking care of the children while pursuing your studies?" />
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
                                                        <InputLabel htmlFor="non_financial_support_needed" value="What kind of non-financial support do you need as a mother to enable you pursue your studies?" />
                                                        <textarea rows={3}
                                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                                            value={dep.non_financial_support_needed}
                                                            onChange={(e) => updateSection('dependants_info', 'non_financial_support_needed', e.target.value)}
                                                            disabled={isLocked} />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* ══════════════════════════════════════════
                                        STEP 4 – SECTION B6: MOTIVATION ESSAY
                                    ══════════════════════════════════════════ */}
                                    {activeStep === 4 && (
                                        <div className="space-y-6">
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                                                    Section B6 – Motivation Statement
                                                </h4>
                                                <div>
                                                    <RequiredLabel htmlFor="motivation" value="20. Write a 250-word motivation, expressing why you need this scholarship offer and how you intend to use it to improve yourself and the community around you." required />
                                                    <textarea
                                                        id="motivation"
                                                        rows={12}
                                                        className="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                                        value={data.essay.motivation}
                                                        onChange={(e) => updateSection('essay', 'motivation', e.target.value)}
                                                        disabled={isLocked}
                                                        placeholder="Write your motivation here (target: 250 words)..."
                                                    />
                                                    <div className="mt-2 flex items-center justify-between">
                                                        <div className="text-xs text-gray-500">
                                                            Word count: <span className={countWords(data.essay.motivation) >= 250 ? 'text-green-600 font-semibold' : 'text-amber-600'}>
                                                                {countWords(data.essay.motivation)} / 250
                                                            </span>
                                                        </div>
                                                        {countWords(data.essay.motivation) >= 250 && (
                                                            <span className="text-xs text-green-600 font-medium">✓ Target reached</span>
                                                        )}
                                                    </div>
                                                    <InputError message={errors['essay.motivation'] || stepErrors['essay.motivation']} className="mt-2" />
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* ══════════════════════════════════════════
                                        STEP 5 – DOCUMENTS
                                    ══════════════════════════════════════════ */}
                                    {activeStep === 5 && (
                                        <div className="space-y-5">
                                            <div className="rounded-md border border-blue-200 bg-blue-50 p-4">
                                                <h4 className="text-sm font-semibold text-blue-900">Required & Optional Attachments</h4>
                                                <p className="mt-1 text-xs text-blue-700">
                                                    Please submit clear, legible copies. Accepted formats: PDF, JPG, PNG (max 5MB per file).
                                                </p>
                                            </div>

                                            {[
                                                { key: 'exam_results', label: 'Photocopy of Examination Results (PLE, UCE, UACE) / Academic Transcript for Diploma', required: true,
                                                    hint: 'Upload your PLE, UCE, UACE results or Diploma transcript' },
                                                { key: 'national_id', label: 'Photocopy of National ID Card (back and front) / NIN', required: true,
                                                    hint: 'Upload both sides of your National ID card or NIN confirmation' },
                                                { key: 'birth_certificate', label: 'Photocopy of National Birth Certificate', required: false,
                                                    hint: 'Upload your national birth certificate' },
                                                { key: 'admission_letter', label: 'Photocopy of Admission Letter to LiT Partner University / UNITE Campus', required: false,
                                                    hint: 'Upload your university admission letter' },
                                                { key: 'recommendation_lc1', label: 'Recommendation Letter from LC1 Chairperson or Person of Reputable Standing', required: false,
                                                    hint: 'Upload recommendation from your area LC1 or equivalent' },
                                                { key: 'recommendation_school', label: 'Recommendation Letter from Former School', required: false,
                                                    hint: 'Upload recommendation from your former school' },
                                                { key: 'refugee_number', label: 'Photocopy of Refugee Number (for those living in Uganda with refugee status)', required: false,
                                                    hint: 'Only required if you have refugee status in Uganda' },
                                            ].map(({ key, label, required, hint }) => (
                                                <div key={key}>
                                                    <RequiredLabel htmlFor={key} value={label} required={required} />
                                                    <p className="mt-1 text-xs text-gray-500 mb-2">{hint}</p>
                                                    <input
                                                        id={key}
                                                        type="file"
                                                        accept=".pdf,.jpg,.jpeg,.png"
                                                        className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                                                        onChange={(e) => {
                                                            hasChanged.current = true;
                                                            setData('documents', { ...docs, [key]: e.target.files[0] });
                                                        }}
                                                        disabled={isLocked}
                                                    />
                                                    {docs[key] && (
                                                        <p className="mt-2 text-sm text-green-600">
                                                            ✓ {typeof docs[key] === 'string' ? 'Previously uploaded' : docs[key].name}
                                                        </p>
                                                    )}
                                                    <InputError message={errors[`documents.${key}`] || stepErrors[`documents.${key}`]} className="mt-2" />
                                                </div>
                                            ))}
                                        </div>
                                    )}

                                    {/* ══════════════════════════════════════════
                                        STEP 6 – SECTION C & D: GUARDIAN / DECLARATION
                                    ══════════════════════════════════════════ */}
                                    {activeStep === 6 && (
                                        <div className="space-y-6">
                                            {/* Section C */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                                                    Section C – To Be Completed by Parent/Legal Guardian
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

                                            {/* Section D */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                                                    Section D – Criminal Offence Declaration
                                                </h4>
                                                <div className="space-y-4">
                                                    <div>
                                                        <RequiredLabel htmlFor="criminal_offence" value="25. Have you ever been Charged and/or Convicted of a criminal offence?" required />
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
                                                            <InputLabel htmlFor="criminal_details" value="If so, please state the Charge/Conviction and elaborate on the circumstances and outcome." />
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

                                            {/* Declaration notice */}
                                            <div className="rounded-md border border-gray-300 bg-gray-50 p-4 text-sm text-gray-700">
                                                <p className="font-semibold mb-2">Declaration</p>
                                                <p className="mb-2">
                                                    It is important that your eligibility for student financial aid be based upon accurate information.
                                                </p>
                                                <p className="mb-2 italic">
                                                    I do hereby declare that all the information given above is true.
                                                </p>
                                                <p className="text-xs text-gray-500">
                                                    <strong>Note:</strong> Misrepresentation in any material form renders the application null and void. Any award made based on misrepresentation shall be withdrawn or refunded by the applicant, and he/she may be prosecuted. The truth, rather than lies, will get you Financial Aid.
                                                </p>
                                            </div>
                                        </div>
                                    )}

                                    {/* ══════════════════════════════════════════
                                        STEP 7 – REVIEW & SUBMIT
                                    ══════════════════════════════════════════ */}
                                    {activeStep === 7 && (
                                        <div className="space-y-6">
                                            <div className="rounded-md border border-emerald-200 bg-emerald-50 p-4">
                                                <h3 className="text-base font-semibold text-emerald-900">Review Your Application</h3>
                                                <p className="mt-1 text-sm text-emerald-700">
                                                    Please review all information carefully before submitting. You can go back to any step to make changes.
                                                </p>
                                            </div>

                                            {/* Personal Info summary */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="text-sm font-semibold text-gray-900 mb-3">Section A – Personal Information</h4>
                                                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                                                    <div><dt className="font-medium text-gray-500">Full Name</dt><dd className="mt-1">{[pi.surname, pi.other_names].filter(Boolean).join(', ') || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Date of Birth</dt><dd className="mt-1">{pi.date_of_birth || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">NIN</dt><dd className="mt-1">{pi.nin || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Phone</dt><dd className="mt-1">{pi.phone || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Email</dt><dd className="mt-1">{pi.email || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Marital Status</dt><dd className="mt-1">{pi.marital_status || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Ugandan National</dt><dd className="mt-1">{pi.is_ugandan === 'yes' ? 'Yes' : pi.is_ugandan === 'no' ? 'No' : 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Disability</dt><dd className="mt-1">{pi.has_disability === 'yes' ? `Yes – ${pi.disability_specify || 'specified in Section B2'}` : pi.has_disability === 'no' ? 'No' : 'Not specified'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Place of Residence</dt><dd className="mt-1">{[pi.residence_village, pi.residence_district, pi.residence_region, pi.residence_country].filter(Boolean).join(', ') || 'Not provided'}</dd></div>
                                                </dl>
                                            </div>

                                            {/* Education summary */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="text-sm font-semibold text-gray-900 mb-3">Section B1 – Education</h4>
                                                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                                                    <div><dt className="font-medium text-gray-500">Academic Programme</dt><dd className="mt-1">{pi.academic_programme || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Institution</dt><dd className="mt-1">{pi.institution || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Teaching Subjects</dt><dd className="mt-1">{[pi.teaching_subjects_1, pi.teaching_subjects_2].filter(Boolean).join(', ') || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Student Admission No.</dt><dd className="mt-1">{pi.student_admission_number || 'Not provided'}</dd></div>
                                                </dl>
                                            </div>

                                            {/* Motivation summary */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="text-sm font-semibold text-gray-900 mb-3">Section B6 – Motivation</h4>
                                                <p className="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded">
                                                    {data.essay.motivation || 'Not provided'}
                                                </p>
                                                <p className="mt-1 text-xs text-gray-500">Word count: {countWords(data.essay.motivation)}</p>
                                            </div>

                                            {/* Documents summary */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="text-sm font-semibold text-gray-900 mb-3">Uploaded Documents</h4>
                                                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                                                    {[
                                                        ['exam_results', 'Examination Results'],
                                                        ['national_id', 'National ID'],
                                                        ['birth_certificate', 'Birth Certificate'],
                                                        ['admission_letter', 'Admission Letter'],
                                                        ['recommendation_lc1', 'Recommendation (LC1)'],
                                                        ['recommendation_school', 'Recommendation (School)'],
                                                        ['refugee_number', 'Refugee Number'],
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

                                            {/* Guardian summary */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="text-sm font-semibold text-gray-900 mb-3">Section C – Guardian/Parent</h4>
                                                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                                                    <div><dt className="font-medium text-gray-500">Name</dt><dd className="mt-1">{[gi.guardian_surname, gi.guardian_other_names].filter(Boolean).join(', ') || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Telephone</dt><dd className="mt-1">{gi.guardian_telephone || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">District/Region</dt><dd className="mt-1">{[gi.guardian_district, gi.guardian_region].filter(Boolean).join(', ') || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Occupation</dt><dd className="mt-1">{gi.guardian_occupation || 'Not provided'}</dd></div>
                                                    <div><dt className="font-medium text-gray-500">Relationship</dt><dd className="mt-1">{gi.guardian_relation || 'Not provided'}</dd></div>
                                                </dl>
                                            </div>

                                            {/* Final declaration notice */}
                                            <div className="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                                <p className="font-semibold">By submitting this application, I declare that:</p>
                                                <ul className="list-disc pl-5 mt-2 space-y-1 text-sm">
                                                    <li>All information provided is true and accurate to the best of my knowledge.</li>
                                                    <li>I understand that misrepresentation renders the application null and void.</li>
                                                    <li>I have read and agree to the terms of the LiT Scholarship Programme.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    )}
                                </motion.div>
                            </AnimatePresence>

                            {/* Navigation */}
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
