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

const STEP_CONFIG = [
    {
        id: 1,
        title: 'Personal Info',
        description: 'Demographics, program, CGPA, schools',
    },
    {
        id: 2,
        title: 'Finances',
        description: 'Expenses, income sources, funding gap',
    },
    {
        id: 3,
        title: 'Guardian Info',
        description: 'Parent or guardian details',
    },
    {
        id: 4,
        title: 'Documents',
        description: 'Upload required documents',
    },
    {
        id: 5,
        title: 'Essay & Commitment',
        description: 'STEM teaching narrative and commitment',
    },
    {
        id: 6,
        title: 'Review & Submit',
        description: 'Summary and final submission',
    },
];

function toNumber(value) {
    const parsed = Number(value);

    return Number.isFinite(parsed) ? parsed : 0;
}

function countWords(text) {
    const normalized = (text || '').trim();

    if (!normalized) {
        return 0;
    }

    return normalized.split(/\s+/).length;
}

function formatCurrency(value) {
    return new Intl.NumberFormat('en-UG', {
        style: 'currency',
        currency: 'UGX',
        maximumFractionDigits: 0,
    }).format(toNumber(value));
}

function scoreLabel(total) {
    if (total >= 80) {
        return 'Excellent';
    }

    if (total >= 65) {
        return 'Strong';
    }

    return 'Needs Review';
}

export default function Form() {
    const { auth, application } = usePage().props;

    const nameParts = (auth?.user?.name || '').trim().split(/\s+/).filter(Boolean);
    const defaultFirstName = nameParts[0] || '';
    const defaultLastName = nameParts.slice(1).join(' ');

    const defaults = useMemo(
        () => ({
            personal_info: {
                first_name: defaultFirstName,
                last_name: defaultLastName,
                phone: '',
                date_of_birth: '',
                gender: '',
                nationality: '',
                has_disability: '',
                disability_details: '',
                refugee_or_displaced: '',
                refugee_details: '',
                residence_area: '',
                university: '',
                program_of_study: '',
                year_of_study: '',
                cgpa: '',
                high_school: '',
                current_school: '',
            },
            financial_info: {
                household_income: '',
                number_of_dependents: '',
                estimated_tuition: '',
                estimated_living_expenses: '',
                other_expenses: '',
                income_sources: '',
                existing_support: '',
                funding_gap: 0,
                requested_support_amount: '',
                scholarship_type_requested: '',
            },
            guardian_info: {
                guardian_name: '',
                guardian_relation: '',
                guardian_phone: '',
                guardian_email: '',
                guardian_occupation: '',
                guardian_address: '',
            },
            essay: {
                personal_statement: '',
                commitment: '',
                additional_information: '',
            },
            documents: {
                academic_documents: null,
                national_id: null,
                admission_form: null,
                provisional_results: null,
            },
        }),
        [defaultFirstName, defaultLastName],
    );

    const initialData = useMemo(
        () => ({
            personal_info: {
                ...defaults.personal_info,
                ...(application?.personal_info || {}),
            },
            financial_info: {
                ...defaults.financial_info,
                ...(application?.financial_info || {}),
            },
            guardian_info: {
                ...defaults.guardian_info,
                ...(application?.guardian_info || {}),
            },
            essay: {
                ...defaults.essay,
                ...(application?.essay || {}),
            },
            documents: {
                ...defaults.documents,
                ...(application?.documents || {}),
            },
        }),
        [application, defaults],
    );

    const { data, setData, post, processing, errors } = useForm(initialData);

    const [activeStep, setActiveStep] = useState(1);
    const [savingDraft, setSavingDraft] = useState(false);
    const [draftMessage, setDraftMessage] = useState('');
    const [stepErrors, setStepErrors] = useState({});

    const initialRender = useRef(true);
    const hasChanged = useRef(false);

    const isLocked = ['submitted', 'under_review', 'approved', 'rejected'].includes(
        application?.status,
    );

    const statusLabels = {
        draft: 'Draft',
        submitted: 'Submitted',
        under_review: 'Under Review',
        approved: 'Approved',
        rejected: 'Rejected',
    };

    const fundingGap = useMemo(() => {
        const totalExpenses =
            toNumber(data.financial_info.estimated_tuition) +
            toNumber(data.financial_info.estimated_living_expenses) +
            toNumber(data.financial_info.other_expenses);

        const totalFunding =
            toNumber(data.financial_info.household_income) +
            toNumber(data.financial_info.existing_support);

        return Math.max(0, totalExpenses - totalFunding);
    }, [
        data.financial_info.estimated_living_expenses,
        data.financial_info.estimated_tuition,
        data.financial_info.existing_support,
        data.financial_info.household_income,
        data.financial_info.other_expenses,
    ]);

    useEffect(() => {
        if (toNumber(data.financial_info.funding_gap) === fundingGap) {
            return;
        }

        setData('financial_info', {
            ...data.financial_info,
            funding_gap: fundingGap,
        });
    }, [data.financial_info, fundingGap, setData]);

    const scorePreview = useMemo(() => {
        let financialNeed = 0;
        const income = toNumber(data.financial_info.household_income);

        if (income < 20000) {
            financialNeed += 20;
        } else if (income < 40000) {
            financialNeed += 15;
        } else if (income < 60000) {
            financialNeed += 10;
        } else if (income < 80000) {
            financialNeed += 5;
        }

        financialNeed += Math.min(
            10,
            toNumber(data.financial_info.number_of_dependents) * 2,
        );

        // Handle optional CGPA - give base points if not provided
        const cgpa = toNumber(data.personal_info.cgpa);
        const academicMerit = cgpa === 0 
            ? 10 // Base points for first-year students without CGPA
            : Math.max(0, Math.min(25, (cgpa / 4) * 25));

        let demographics = 5;

        if ((data.personal_info.gender || '').toLowerCase() === 'female') {
            demographics += 5;
        }

        if (data.personal_info.residence_area === 'rural') {
            demographics += 5;
        }

        demographics = Math.max(0, Math.min(15, demographics));

        const commitment = Math.max(
            0,
            Math.min(15, (countWords(data.essay.commitment) / 100) * 15),
        );

        const essayQuality = Math.max(
            0,
            Math.min(15, (countWords(data.essay.personal_statement) / 300) * 15),
        );

        const total = Math.round(
            financialNeed +
                academicMerit +
                demographics +
                commitment +
                essayQuality,
        );

        return {
            financial_need: Math.round(financialNeed),
            academic_merit: Math.round(academicMerit),
            demographics: Math.round(demographics),
            commitment: Math.round(commitment),
            essay_quality: Math.round(essayQuality),
            total,
            label: scoreLabel(total),
        };
    }, [
        data.essay.commitment,
        data.essay.personal_statement,
        data.financial_info.household_income,
        data.financial_info.number_of_dependents,
        data.personal_info.cgpa,
        data.personal_info.gender,
        data.personal_info.residence_area,
    ]);

    const updateSectionValue = (section, field, value) => {
        hasChanged.current = true;

        setData(section, {
            ...data[section],
            [field]: value,
        });

        // Clear step errors for this field when user starts typing
        const fieldKey = `${section}.${field}`;
        if (stepErrors[fieldKey]) {
            setStepErrors(prev => {
                const newErrors = { ...prev };
                delete newErrors[fieldKey];
                return newErrors;
            });
        }
    };

    const saveDraft = async (message = 'Draft saved successfully.') => {
        if (isLocked) {
            return;
        }

        setSavingDraft(true);
        setDraftMessage('Saving draft...');

        try {
            await window.axios.post(route('application.draft'), data);
            setDraftMessage(message);
        } catch {
            setDraftMessage('Unable to save draft right now. Please try again.');
        } finally {
            setSavingDraft(false);
        }
    };

    useEffect(() => {
        if (isLocked) {
            return;
        }

        if (initialRender.current) {
            initialRender.current = false;
            return;
        }

        if (!hasChanged.current) {
            return;
        }

        const timer = setTimeout(() => {
            void saveDraft('Draft auto-saved.');
        }, 1200);

        return () => clearTimeout(timer);
    }, [
        data.essay,
        data.financial_info,
        data.guardian_info,
        data.personal_info,
        data.documents,
        isLocked,
    ]);

    const goToStep = (stepId) => {
        setActiveStep(stepId);
    };

    // Validate current step before moving forward
    const validateStep = (step) => {
        const errors = {};

        if (step === 1) {
            // Personal Info validation
            if (!data.personal_info.first_name?.trim()) {
                errors['personal_info.first_name'] = 'First name is required';
            }
            if (!data.personal_info.last_name?.trim()) {
                errors['personal_info.last_name'] = 'Last name is required';
            }
            if (!data.personal_info.gender) {
                errors['personal_info.gender'] = 'Gender is required';
            }
            if (!data.personal_info.has_disability) {
                errors['personal_info.has_disability'] = 'Disability status is required';
            }
            if (data.personal_info.has_disability === 'yes' && !data.personal_info.disability_details?.trim()) {
                errors['personal_info.disability_details'] = 'Disability details are required';
            }
            if (!data.personal_info.refugee_or_displaced) {
                errors['personal_info.refugee_or_displaced'] = 'Refugee/displaced status is required';
            }
            if (data.personal_info.refugee_or_displaced === 'yes' && !data.personal_info.refugee_details?.trim()) {
                errors['personal_info.refugee_details'] = 'Refugee/displaced details are required';
            }
            if (!data.personal_info.residence_area) {
                errors['personal_info.residence_area'] = 'Residence area is required';
            }
            if (!data.personal_info.university?.trim()) {
                errors['personal_info.university'] = 'University is required';
            }
            if (!data.personal_info.program_of_study?.trim()) {
                errors['personal_info.program_of_study'] = 'Program of study is required';
            }
            if (!data.personal_info.high_school?.trim()) {
                errors['personal_info.high_school'] = 'High school is required';
            }
        } else if (step === 2) {
            // Financial Info validation
            if (!data.financial_info.household_income || data.financial_info.household_income === '') {
                errors['financial_info.household_income'] = 'Household income is required';
            }
            if (!data.financial_info.number_of_dependents && data.financial_info.number_of_dependents !== 0) {
                errors['financial_info.number_of_dependents'] = 'Number of dependents is required';
            }
            if (!data.financial_info.estimated_tuition || data.financial_info.estimated_tuition === '') {
                errors['financial_info.estimated_tuition'] = 'Estimated tuition is required';
            }
            if (!data.financial_info.estimated_living_expenses || data.financial_info.estimated_living_expenses === '') {
                errors['financial_info.estimated_living_expenses'] = 'Estimated living expenses is required';
            }
            if (!data.financial_info.income_sources?.trim()) {
                errors['financial_info.income_sources'] = 'Income sources is required';
            }
        } else if (step === 3) {
            // Guardian Info validation
            if (!data.guardian_info.guardian_name?.trim()) {
                errors['guardian_info.guardian_name'] = 'Guardian name is required';
            }
            if (!data.guardian_info.guardian_phone?.trim()) {
                errors['guardian_info.guardian_phone'] = 'Guardian phone is required';
            }
            if (!data.guardian_info.guardian_relation?.trim()) {
                errors['guardian_info.guardian_relation'] = 'Guardian relation is required';
            }
        } else if (step === 4) {
            // Documents validation
            if (!data.documents.academic_documents) {
                errors['documents.academic_documents'] = 'Academic documents are required';
            }
            if (!data.documents.national_id) {
                errors['documents.national_id'] = 'National ID is required';
            }
        } else if (step === 5) {
            // Essay validation
            if (!data.essay.personal_statement?.trim() || countWords(data.essay.personal_statement) < 100) {
                errors['essay.personal_statement'] = 'Personal statement is required (minimum 100 words)';
            }
            if (!data.essay.commitment?.trim() || countWords(data.essay.commitment) < 100) {
                errors['essay.commitment'] = 'Teaching commitment is required (minimum 100 words)';
            }
        }

        return errors;
    };

    const nextStep = () => {
        if (activeStep === STEP_CONFIG.length) {
            return;
        }

        // Validate current step
        const errors = validateStep(activeStep);
        setStepErrors(errors);

        if (Object.keys(errors).length > 0) {
            setDraftMessage('Please fill in all required fields before proceeding.');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        if (!isLocked) {
            void saveDraft('Draft saved.');
        }

        setActiveStep((current) => current + 1);
    };

    const previousStep = () => {
        if (activeStep === 1) {
            return;
        }

        setActiveStep((current) => current - 1);
    };

    // Helper function to determine which step has errors
    const getStepWithErrors = (errorKey) => {
        if (errorKey.startsWith('personal_info.')) return 1;
        if (errorKey.startsWith('financial_info.')) return 2;
        if (errorKey.startsWith('guardian_info.')) return 3;
        if (errorKey.startsWith('documents.')) return 4;
        if (errorKey.startsWith('essay.')) return 5;
        return 6;
    };

    // Get steps that have errors
    const stepsWithErrors = useMemo(() => {
        const steps = new Set();
        Object.keys(errors).forEach((key) => {
            steps.add(getStepWithErrors(key));
        });
        return steps;
    }, [errors]);

    const submit = (event) => {
        event.preventDefault();

        console.log('Submitting application...', {
            hasDocuments: !!data.documents,
            documentKeys: Object.keys(data.documents || {}),
        });

        // Use FormData for file uploads
        post(route('application.submit'), {
            forceFormData: true,
            preserveScroll: false,
            onSuccess: () => {
                console.log('Application submitted successfully');
                setDraftMessage('Application submitted successfully.');
            },
            onError: (errors) => {
                console.error('Submission errors:', errors);
                setDraftMessage('Error submitting application. Please check all required fields.');
                // Scroll to top to show error summary
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Scholarship Application
                    </h2>
                    <span className="rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700">
                        Status: {statusLabels[application?.status || 'draft'] || 'Draft'}
                    </span>
                </div>
            }
        >
            <Head title="My Application" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                        <div className="mb-6 flex flex-col gap-2">
                            <h3 className="text-lg font-semibold text-gray-900">
                                Multi-Step Application Form
                            </h3>
                            <p className="text-sm text-gray-600">
                                Complete all five steps and submit once every required field is filled.
                            </p>
                            {isLocked && (
                                <p className="rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                                    This application is already submitted. You can review details but cannot edit fields.
                                </p>
                            )}
                        </div>

                        <div className="mb-6 h-2 w-full rounded-full bg-gray-200">
                            <div
                                className="h-full rounded-full bg-emerald-600 transition-all duration-300"
                                style={{
                                    width: `${(activeStep / STEP_CONFIG.length) * 100}%`,
                                }}
                            />
                        </div>

                        <div className="mb-8 grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6">
                            {STEP_CONFIG.map((step) => (
                                <button
                                    key={step.id}
                                    type="button"
                                    onClick={() => goToStep(step.id)}
                                    className={
                                        'relative rounded-md border px-3 py-3 text-left transition ' +
                                        (step.id === activeStep
                                            ? 'border-emerald-500 bg-emerald-50'
                                            : stepsWithErrors.has(step.id)
                                              ? 'border-red-300 bg-red-50 hover:border-red-400'
                                              : 'border-gray-200 hover:border-gray-300')
                                    }
                                >
                                    {stepsWithErrors.has(step.id) && (
                                        <div className="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">
                                            !
                                        </div>
                                    )}
                                    <div className={
                                        'text-xs font-semibold uppercase tracking-wide ' +
                                        (stepsWithErrors.has(step.id) ? 'text-red-600' : 'text-gray-500')
                                    }>
                                        Step {step.id}
                                    </div>
                                    <div className="text-sm font-semibold text-gray-900">
                                        {step.title}
                                    </div>
                                    <div className="mt-1 text-xs text-gray-600">
                                        {step.description}
                                    </div>
                                </button>
                            ))}
                        </div>

                        <form onSubmit={submit}>
                            {/* Error Summary Banner */}
                            {(Object.keys(errors).length > 0 || Object.keys(stepErrors).length > 0) && (
                                <div className="mb-6 rounded-lg border border-red-300 bg-red-50 p-4">
                                    <div className="flex items-start">
                                        <div className="flex-shrink-0">
                                            <svg className="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                                            </svg>
                                        </div>
                                        <div className="ml-3 flex-1">
                                            <h3 className="text-sm font-semibold text-red-800">
                                                Please fix the following errors before proceeding:
                                            </h3>
                                            <div className="mt-2 text-sm text-red-700">
                                                <ul className="list-disc space-y-1 pl-5">
                                                    {Object.entries({...errors, ...stepErrors}).map(([key, message]) => (
                                                        <li key={key}>{message}</li>
                                                    ))}
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
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
                                    {activeStep === 1 && (
                                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div>
                                                <RequiredLabel htmlFor="first_name" value="First Name" required />
                                                <TextInput
                                                    id="first_name"
                                                    className="mt-1 block w-full"
                                                    value={data.personal_info.first_name}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'first_name',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={errors['personal_info.first_name'] || stepErrors['personal_info.first_name']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <RequiredLabel htmlFor="last_name" value="Last Name" required />
                                                <TextInput
                                                    id="last_name"
                                                    className="mt-1 block w-full"
                                                    value={data.personal_info.last_name}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'last_name',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={errors['personal_info.last_name'] || stepErrors['personal_info.last_name']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel htmlFor="phone" value="Phone Number" />
                                                <TextInput
                                                    id="phone"
                                                    className="mt-1 block w-full"
                                                    value={data.personal_info.phone}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'phone',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <InputError
                                                    message={errors['personal_info.phone']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel htmlFor="date_of_birth" value="Date of Birth" />
                                                <TextInput
                                                    id="date_of_birth"
                                                    type="date"
                                                    className="mt-1 block w-full"
                                                    value={data.personal_info.date_of_birth}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'date_of_birth',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <InputError
                                                    message={errors['personal_info.date_of_birth']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <RequiredLabel htmlFor="gender" value="Gender" required />
                                                <select
                                                    id="gender"
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    value={data.personal_info.gender}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'gender',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                >
                                                    <option value="">Select gender</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Prefer not to say">
                                                        Prefer not to say
                                                    </option>
                                                </select>
                                                <InputError
                                                    message={errors['personal_info.gender'] || stepErrors['personal_info.gender']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel htmlFor="nationality" value="Nationality" />
                                                <TextInput
                                                    id="nationality"
                                                    className="mt-1 block w-full"
                                                    value={data.personal_info.nationality}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'nationality',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <InputError
                                                    message={errors['personal_info.nationality']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <RequiredLabel htmlFor="university" value="University" required />
                                                <TextInput
                                                    id="university"
                                                    className="mt-1 block w-full"
                                                    value={data.personal_info.university}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'university',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={errors['personal_info.university'] || stepErrors['personal_info.university']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <RequiredLabel
                                                    htmlFor="program_of_study"
                                                    value="Program of Study"
                                                    required
                                                />
                                                <TextInput
                                                    id="program_of_study"
                                                    className="mt-1 block w-full"
                                                    value={data.personal_info.program_of_study}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'program_of_study',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={errors['personal_info.program_of_study'] || stepErrors['personal_info.program_of_study']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel htmlFor="year_of_study" value="Year of Study" />
                                                <TextInput
                                                    id="year_of_study"
                                                    className="mt-1 block w-full"
                                                    value={data.personal_info.year_of_study}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'year_of_study',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <InputError
                                                    message={errors['personal_info.year_of_study']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel htmlFor="cgpa" value="Current CGPA (Optional)" />
                                                <TextInput
                                                    id="cgpa"
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    max="5"
                                                    className="mt-1 block w-full"
                                                    value={data.personal_info.cgpa}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'cgpa',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <p className="mt-1 text-xs text-gray-600">
                                                    Leave blank if you're a first-year student
                                                </p>
                                                <InputError
                                                    message={errors['personal_info.cgpa']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <RequiredLabel htmlFor="high_school" value="High School" required />
                                                <TextInput
                                                    id="high_school"
                                                    className="mt-1 block w-full"
                                                    value={data.personal_info.high_school}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'high_school',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={errors['personal_info.high_school'] || stepErrors['personal_info.high_school']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="current_school"
                                                    value="Current School / Campus"
                                                />
                                                <TextInput
                                                    id="current_school"
                                                    className="mt-1 block w-full"
                                                    value={data.personal_info.current_school}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'current_school',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <InputError
                                                    message={errors['personal_info.current_school']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div className="md:col-span-2">
                                                <RequiredLabel
                                                    htmlFor="has_disability"
                                                    value="Are you a person with disability?"
                                                    required
                                                />
                                                <select
                                                    id="has_disability"
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    value={data.personal_info.has_disability}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'has_disability',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                >
                                                    <option value="">Select an option</option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                    <option value="prefer_not_to_answer">Prefer Not to Answer</option>
                                                </select>
                                                <InputError
                                                    message={errors['personal_info.has_disability'] || stepErrors['personal_info.has_disability']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            {data.personal_info.has_disability === 'yes' && (
                                                <div className="md:col-span-2">
                                                    <RequiredLabel
                                                        htmlFor="disability_details"
                                                        value="Please specify your disability"
                                                        required
                                                    />
                                                    <TextInput
                                                        id="disability_details"
                                                        className="mt-1 block w-full"
                                                        value={data.personal_info.disability_details}
                                                        onChange={(event) =>
                                                            updateSectionValue(
                                                                'personal_info',
                                                                'disability_details',
                                                                event.target.value,
                                                            )
                                                        }
                                                        disabled={isLocked}
                                                        placeholder="Please describe your disability"
                                                    />
                                                    <InputError
                                                        message={errors['personal_info.disability_details'] || stepErrors['personal_info.disability_details']}
                                                        className="mt-2"
                                                    />
                                                </div>
                                            )}

                                            <div className="md:col-span-2">
                                                <InputLabel
                                                    htmlFor="refugee_or_displaced"
                                                    value="Are you a refugee or displaced person?"
                                                />
                                                <select
                                                    id="refugee_or_displaced"
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    value={data.personal_info.refugee_or_displaced}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'refugee_or_displaced',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                >
                                                    <option value="">Select an option</option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                    <option value="prefer_not_to_answer">Prefer Not to Answer</option>
                                                </select>
                                                <InputError
                                                    message={errors['personal_info.refugee_or_displaced']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            {data.personal_info.refugee_or_displaced === 'yes' && (
                                                <div className="md:col-span-2">
                                                    <InputLabel
                                                        htmlFor="refugee_details"
                                                        value="Please provide details"
                                                    />
                                                    <TextInput
                                                        id="refugee_details"
                                                        className="mt-1 block w-full"
                                                        value={data.personal_info.refugee_details}
                                                        onChange={(event) =>
                                                            updateSectionValue(
                                                                'personal_info',
                                                                'refugee_details',
                                                                event.target.value,
                                                            )
                                                        }
                                                        disabled={isLocked}
                                                        placeholder="Please provide details about your refugee or displaced status"
                                                    />
                                                    <InputError
                                                        message={errors['personal_info.refugee_details']}
                                                        className="mt-2"
                                                    />
                                                </div>
                                            )}

                                            <div className="md:col-span-2">
                                                <InputLabel
                                                    htmlFor="residence_area"
                                                    value="Are you living in a Rural or Urban area?"
                                                />
                                                <select
                                                    id="residence_area"
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    value={data.personal_info.residence_area}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'personal_info',
                                                            'residence_area',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                >
                                                    <option value="">Select an option</option>
                                                    <option value="rural">Rural Area</option>
                                                    <option value="urban">Urban Area</option>
                                                </select>
                                                <InputError
                                                    message={errors['personal_info.residence_area']}
                                                    className="mt-2"
                                                />
                                            </div>
                                        </div>
                                    )}

                                    {activeStep === 2 && (
                                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div>
                                                <RequiredLabel
                                                    htmlFor="household_income"
                                                    value="Household Income (UGX)"
                                                    required
                                                />
                                                <TextInput
                                                    id="household_income"
                                                    type="number"
                                                    min="0"
                                                    className="mt-1 block w-full"
                                                    value={data.financial_info.household_income}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'financial_info',
                                                            'household_income',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={errors['financial_info.household_income'] || stepErrors['financial_info.household_income']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="number_of_dependents"
                                                    value="Number of Dependents"
                                                />
                                                <TextInput
                                                    id="number_of_dependents"
                                                    type="number"
                                                    min="0"
                                                    className="mt-1 block w-full"
                                                    value={
                                                        data.financial_info
                                                            .number_of_dependents
                                                    }
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'financial_info',
                                                            'number_of_dependents',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={
                                                        errors[
                                                            'financial_info.number_of_dependents'
                                                        ]
                                                    }
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="estimated_tuition"
                                                    value="Estimated Tuition (UGX)"
                                                />
                                                <TextInput
                                                    id="estimated_tuition"
                                                    type="number"
                                                    min="0"
                                                    className="mt-1 block w-full"
                                                    value={data.financial_info.estimated_tuition}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'financial_info',
                                                            'estimated_tuition',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={errors['financial_info.estimated_tuition']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="estimated_living_expenses"
                                                    value="Estimated Living Expenses (UGX)"
                                                />
                                                <TextInput
                                                    id="estimated_living_expenses"
                                                    type="number"
                                                    min="0"
                                                    className="mt-1 block w-full"
                                                    value={
                                                        data.financial_info
                                                            .estimated_living_expenses
                                                    }
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'financial_info',
                                                            'estimated_living_expenses',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={
                                                        errors[
                                                            'financial_info.estimated_living_expenses'
                                                        ]
                                                    }
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="other_expenses"
                                                    value="Other Estimated Expenses (UGX)"
                                                />
                                                <TextInput
                                                    id="other_expenses"
                                                    type="number"
                                                    min="0"
                                                    className="mt-1 block w-full"
                                                    value={data.financial_info.other_expenses}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'financial_info',
                                                            'other_expenses',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <InputError
                                                    message={errors['financial_info.other_expenses']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="existing_support"
                                                    value="Existing Funding Support (UGX)"
                                                />
                                                <TextInput
                                                    id="existing_support"
                                                    type="number"
                                                    min="0"
                                                    className="mt-1 block w-full"
                                                    value={data.financial_info.existing_support}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'financial_info',
                                                            'existing_support',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <InputError
                                                    message={errors['financial_info.existing_support']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="requested_support_amount"
                                                    value="Requested Scholarship Amount (UGX)"
                                                />
                                                <TextInput
                                                    id="requested_support_amount"
                                                    type="number"
                                                    min="0"
                                                    className="mt-1 block w-full"
                                                    value={
                                                        data.financial_info
                                                            .requested_support_amount
                                                    }
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'financial_info',
                                                            'requested_support_amount',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <InputError
                                                    message={
                                                        errors[
                                                            'financial_info.requested_support_amount'
                                                        ]
                                                    }
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="scholarship_type_requested"
                                                    value="Scholarship Type Requested"
                                                />
                                                <select
                                                    id="scholarship_type_requested"
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    value={
                                                        data.financial_info
                                                            .scholarship_type_requested
                                                    }
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'financial_info',
                                                            'scholarship_type_requested',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                >
                                                    <option value="">Select option</option>
                                                    <option value="full">Full Scholarship</option>
                                                    <option value="partial">
                                                        Partial Scholarship
                                                    </option>
                                                </select>
                                                <InputError
                                                    message={
                                                        errors[
                                                            'financial_info.scholarship_type_requested'
                                                        ]
                                                    }
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div className="md:col-span-2">
                                                <InputLabel
                                                    htmlFor="income_sources"
                                                    value="Income Sources"
                                                />
                                                <textarea
                                                    id="income_sources"
                                                    rows={4}
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    value={data.financial_info.income_sources}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'financial_info',
                                                            'income_sources',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                    placeholder="Describe household income sources and current financial constraints"
                                                />
                                                <InputError
                                                    message={errors['financial_info.income_sources']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div className="md:col-span-2 rounded-md border border-emerald-200 bg-emerald-50 p-4">
                                                <div className="text-sm text-emerald-800">
                                                    <span className="font-semibold">Funding Gap:</span>{' '}
                                                    {formatCurrency(fundingGap)}
                                                </div>
                                                <div className="mt-1 text-xs text-emerald-700">
                                                    Automatically calculated from estimated expenses minus available funding.
                                                </div>
                                                <InputError
                                                    message={errors['financial_info.funding_gap']}
                                                    className="mt-2"
                                                />
                                            </div>
                                        </div>
                                    )}

                                    {activeStep === 3 && (
                                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div>
                                                <InputLabel
                                                    htmlFor="guardian_name"
                                                    value="Parent / Guardian Name"
                                                />
                                                <TextInput
                                                    id="guardian_name"
                                                    className="mt-1 block w-full"
                                                    value={data.guardian_info.guardian_name}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'guardian_info',
                                                            'guardian_name',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={errors['guardian_info.guardian_name']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="guardian_relation"
                                                    value="Relation"
                                                />
                                                <TextInput
                                                    id="guardian_relation"
                                                    className="mt-1 block w-full"
                                                    value={data.guardian_info.guardian_relation}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'guardian_info',
                                                            'guardian_relation',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={errors['guardian_info.guardian_relation']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel htmlFor="guardian_phone" value="Phone" />
                                                <TextInput
                                                    id="guardian_phone"
                                                    className="mt-1 block w-full"
                                                    value={data.guardian_info.guardian_phone}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'guardian_info',
                                                            'guardian_phone',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                <InputError
                                                    message={errors['guardian_info.guardian_phone']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel htmlFor="guardian_email" value="Email" />
                                                <TextInput
                                                    id="guardian_email"
                                                    type="email"
                                                    className="mt-1 block w-full"
                                                    value={data.guardian_info.guardian_email}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'guardian_info',
                                                            'guardian_email',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <InputError
                                                    message={errors['guardian_info.guardian_email']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="guardian_occupation"
                                                    value="Occupation"
                                                />
                                                <TextInput
                                                    id="guardian_occupation"
                                                    className="mt-1 block w-full"
                                                    value={data.guardian_info.guardian_occupation}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'guardian_info',
                                                            'guardian_occupation',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <InputError
                                                    message={errors['guardian_info.guardian_occupation']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="guardian_address"
                                                    value="Address"
                                                />
                                                <TextInput
                                                    id="guardian_address"
                                                    className="mt-1 block w-full"
                                                    value={data.guardian_info.guardian_address}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'guardian_info',
                                                            'guardian_address',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                <InputError
                                                    message={errors['guardian_info.guardian_address']}
                                                    className="mt-2"
                                                />
                                            </div>
                                        </div>
                                    )}

                                    {activeStep === 4 && (
                                        <div className="space-y-5">
                                            <div className="rounded-md border border-blue-200 bg-blue-50 p-4 mb-4">
                                                <h4 className="text-sm font-semibold text-blue-900">
                                                    Required Documents
                                                </h4>
                                                <p className="mt-1 text-xs text-blue-700">
                                                    Please upload clear, legible copies of the required documents. Accepted formats: PDF, JPG, PNG (Max 5MB per file)
                                                </p>
                                            </div>

                                            <div>
                                                <RequiredLabel
                                                    htmlFor="academic_documents"
                                                    value="Academic Documents"
                                                    required
                                                />
                                                <p className="mt-1 text-xs text-gray-600 mb-2">
                                                    Upload transcripts, certificates, or other academic records as a single PDF document
                                                </p>
                                                <input
                                                    id="academic_documents"
                                                    type="file"
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'documents',
                                                            'academic_documents',
                                                            event.target.files[0],
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                {data.documents.academic_documents && (
                                                    <p className="mt-2 text-sm text-green-600">
                                                        ✓ File selected: {data.documents.academic_documents.name || 'Uploaded'}
                                                    </p>
                                                )}
                                                <InputError
                                                    message={errors['documents.academic_documents'] || stepErrors['documents.academic_documents']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <RequiredLabel
                                                    htmlFor="national_id"
                                                    value="National ID"
                                                    required
                                                />
                                                <p className="mt-1 text-xs text-gray-600 mb-2">
                                                    Upload a clear copy of your National ID card (both sides if applicable)
                                                </p>
                                                <input
                                                    id="national_id"
                                                    type="file"
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'documents',
                                                            'national_id',
                                                            event.target.files[0],
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                />
                                                {data.documents.national_id && (
                                                    <p className="mt-2 text-sm text-green-600">
                                                        ✓ File selected: {data.documents.national_id.name || 'Uploaded'}
                                                    </p>
                                                )}
                                                <InputError
                                                    message={errors['documents.national_id']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="admission_form"
                                                    value="Admission Form (Optional)"
                                                />
                                                <p className="mt-1 text-xs text-gray-600 mb-2">
                                                    Upload your university admission letter or form if available
                                                </p>
                                                <input
                                                    id="admission_form"
                                                    type="file"
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'documents',
                                                            'admission_form',
                                                            event.target.files[0],
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                {data.documents.admission_form && (
                                                    <p className="mt-2 text-sm text-green-600">
                                                        ✓ File selected: {data.documents.admission_form.name || 'Uploaded'}
                                                    </p>
                                                )}
                                                <InputError
                                                    message={errors['documents.admission_form']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="provisional_results"
                                                    value="Provisional Result Statement (Optional)"
                                                />
                                                <p className="mt-1 text-xs text-gray-600 mb-2">
                                                    Upload your most recent provisional results if available
                                                </p>
                                                <input
                                                    id="provisional_results"
                                                    type="file"
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'documents',
                                                            'provisional_results',
                                                            event.target.files[0],
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                />
                                                {data.documents.provisional_results && (
                                                    <p className="mt-2 text-sm text-green-600">
                                                        ✓ File selected: {data.documents.provisional_results.name || 'Uploaded'}
                                                    </p>
                                                )}
                                                <InputError
                                                    message={errors['documents.provisional_results']}
                                                    className="mt-2"
                                                />
                                            </div>
                                        </div>
                                    )}

                                    {activeStep === 5 && (
                                        <div className="space-y-5">
                                            <div>
                                                <InputLabel
                                                    htmlFor="personal_statement"
                                                    value="Personal Essay (STEM Journey and Need)"
                                                />
                                                <textarea
                                                    id="personal_statement"
                                                    rows={8}
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    value={data.essay.personal_statement}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'essay',
                                                            'personal_statement',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                    placeholder="Share your academic path, financial context, and why this scholarship matters."
                                                />
                                                <div className="mt-1 text-xs text-gray-500">
                                                    Word count: {countWords(data.essay.personal_statement)}
                                                </div>
                                                <InputError
                                                    message={errors['essay.personal_statement']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="commitment"
                                                    value="Commitment to Teaching in Rural/Underserved Areas"
                                                />
                                                <textarea
                                                    id="commitment"
                                                    rows={8}
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    value={data.essay.commitment}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'essay',
                                                            'commitment',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    required
                                                    placeholder="Explain how you will apply your STEM education in schools and communities with the greatest need."
                                                />
                                                <div className="mt-1 text-xs text-gray-500">
                                                    Word count: {countWords(data.essay.commitment)}
                                                </div>
                                                <InputError
                                                    message={errors['essay.commitment']}
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    htmlFor="additional_information"
                                                    value="Additional Information (Optional)"
                                                />
                                                <textarea
                                                    id="additional_information"
                                                    rows={4}
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    value={data.essay.additional_information}
                                                    onChange={(event) =>
                                                        updateSectionValue(
                                                            'essay',
                                                            'additional_information',
                                                            event.target.value,
                                                        )
                                                    }
                                                    disabled={isLocked}
                                                    placeholder="Any context you want the committee to consider"
                                                />
                                                <InputError
                                                    message={errors['essay.additional_information']}
                                                    className="mt-2"
                                                />
                                            </div>
                                        </div>
                                    )}

                                    {activeStep === 6 && (
                                        <div className="space-y-6">
                                            <div className="rounded-md border border-emerald-200 bg-emerald-50 p-4 mb-4">
                                                <h3 className="text-base font-semibold text-emerald-900">
                                                    Review Your Application
                                                </h3>
                                                <p className="mt-1 text-sm text-emerald-700">
                                                    Please review all information carefully before submitting. You can go back to any step to make changes.
                                                </p>
                                            </div>

                                            {/* Personal Information */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="text-sm font-semibold text-gray-900 mb-3">
                                                    Personal Information
                                                </h4>
                                                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                                                    <div>
                                                        <dt className="font-medium text-gray-500">First Name</dt>
                                                        <dd className="mt-1">{data.personal_info.first_name || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Last Name</dt>
                                                        <dd className="mt-1">{data.personal_info.last_name || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Phone Number</dt>
                                                        <dd className="mt-1">{data.personal_info.phone || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Date of Birth</dt>
                                                        <dd className="mt-1">{data.personal_info.date_of_birth || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Gender</dt>
                                                        <dd className="mt-1">{data.personal_info.gender || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Nationality</dt>
                                                        <dd className="mt-1">{data.personal_info.nationality || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Person with Disability</dt>
                                                        <dd className="mt-1">
                                                            {data.personal_info.has_disability === 'yes' ? 'Yes' : 
                                                             data.personal_info.has_disability === 'no' ? 'No' : 
                                                             data.personal_info.has_disability === 'prefer_not_to_answer' ? 'Prefer Not to Answer' : 
                                                             'Not provided'}
                                                        </dd>
                                                    </div>
                                                    {data.personal_info.has_disability === 'yes' && (
                                                        <div>
                                                            <dt className="font-medium text-gray-500">Disability Details</dt>
                                                            <dd className="mt-1">{data.personal_info.disability_details || 'Not provided'}</dd>
                                                        </div>
                                                    )}
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Refugee or Displaced Person</dt>
                                                        <dd className="mt-1">
                                                            {data.personal_info.refugee_or_displaced === 'yes' ? 'Yes' : 
                                                             data.personal_info.refugee_or_displaced === 'no' ? 'No' : 
                                                             data.personal_info.refugee_or_displaced === 'prefer_not_to_answer' ? 'Prefer Not to Answer' : 
                                                             'Not provided'}
                                                        </dd>
                                                    </div>
                                                    {data.personal_info.refugee_or_displaced === 'yes' && (
                                                        <div>
                                                            <dt className="font-medium text-gray-500">Refugee/Displaced Details</dt>
                                                            <dd className="mt-1">{data.personal_info.refugee_details || 'Not provided'}</dd>
                                                        </div>
                                                    )}
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Residence Area</dt>
                                                        <dd className="mt-1">
                                                            {data.personal_info.residence_area === 'rural' ? 'Rural Area' : 
                                                             data.personal_info.residence_area === 'urban' ? 'Urban Area' : 
                                                             'Not provided'}
                                                        </dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">University</dt>
                                                        <dd className="mt-1">{data.personal_info.university || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Program of Study</dt>
                                                        <dd className="mt-1">{data.personal_info.program_of_study || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Year of Study</dt>
                                                        <dd className="mt-1">{data.personal_info.year_of_study || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Current CGPA</dt>
                                                        <dd className="mt-1">{data.personal_info.cgpa || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">High School</dt>
                                                        <dd className="mt-1">{data.personal_info.high_school || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Current School/Campus</dt>
                                                        <dd className="mt-1">{data.personal_info.current_school || 'Not provided'}</dd>
                                                    </div>
                                                </dl>
                                            </div>

                                            {/* Financial Information */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="text-sm font-semibold text-gray-900 mb-3">
                                                    Financial Information
                                                </h4>
                                                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Household Income</dt>
                                                        <dd className="mt-1">{data.financial_info.household_income ? formatCurrency(data.financial_info.household_income) : 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Number of Dependents</dt>
                                                        <dd className="mt-1">{data.financial_info.number_of_dependents || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Estimated Tuition</dt>
                                                        <dd className="mt-1">{data.financial_info.estimated_tuition ? formatCurrency(data.financial_info.estimated_tuition) : 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Estimated Living Expenses</dt>
                                                        <dd className="mt-1">{data.financial_info.estimated_living_expenses ? formatCurrency(data.financial_info.estimated_living_expenses) : 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Other Expenses</dt>
                                                        <dd className="mt-1">{data.financial_info.other_expenses ? formatCurrency(data.financial_info.other_expenses) : 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Existing Support</dt>
                                                        <dd className="mt-1">{data.financial_info.existing_support ? formatCurrency(data.financial_info.existing_support) : 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Requested Support Amount</dt>
                                                        <dd className="mt-1">{data.financial_info.requested_support_amount ? formatCurrency(data.financial_info.requested_support_amount) : 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Scholarship Type Requested</dt>
                                                        <dd className="mt-1">
                                                            {data.financial_info.scholarship_type_requested === 'full' ? 'Full Scholarship' : 
                                                             data.financial_info.scholarship_type_requested === 'partial' ? 'Partial Scholarship' : 
                                                             'Not provided'}
                                                        </dd>
                                                    </div>
                                                    <div className="md:col-span-2">
                                                        <dt className="font-medium text-gray-500">Income Sources</dt>
                                                        <dd className="mt-1 whitespace-pre-wrap">{data.financial_info.income_sources || 'Not provided'}</dd>
                                                    </div>
                                                    <div className="md:col-span-2">
                                                        <dt className="font-medium text-gray-500">Funding Gap</dt>
                                                        <dd className="mt-1 text-emerald-700 font-semibold">{formatCurrency(fundingGap)}</dd>
                                                    </div>
                                                </dl>
                                            </div>

                                            {/* Guardian Information */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="text-sm font-semibold text-gray-900 mb-3">
                                                    Guardian Information
                                                </h4>
                                                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Guardian Name</dt>
                                                        <dd className="mt-1">{data.guardian_info.guardian_name || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Relation</dt>
                                                        <dd className="mt-1">{data.guardian_info.guardian_relation || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Phone</dt>
                                                        <dd className="mt-1">{data.guardian_info.guardian_phone || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Email</dt>
                                                        <dd className="mt-1">{data.guardian_info.guardian_email || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Occupation</dt>
                                                        <dd className="mt-1">{data.guardian_info.guardian_occupation || 'Not provided'}</dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Address</dt>
                                                        <dd className="mt-1">{data.guardian_info.guardian_address || 'Not provided'}</dd>
                                                    </div>
                                                </dl>
                                            </div>

                                            {/* Essays */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="text-sm font-semibold text-gray-900 mb-3">
                                                    Essays & Statements
                                                </h4>
                                                <div className="space-y-4">
                                                    <div>
                                                        <dt className="font-medium text-gray-500 text-sm">Personal Statement</dt>
                                                        <dd className="mt-2 text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded">
                                                            {data.essay.personal_statement || 'Not provided'}
                                                        </dd>
                                                        <p className="mt-1 text-xs text-gray-500">
                                                            Word count: {countWords(data.essay.personal_statement)}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500 text-sm">Teaching Commitment</dt>
                                                        <dd className="mt-2 text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded">
                                                            {data.essay.commitment || 'Not provided'}
                                                        </dd>
                                                        <p className="mt-1 text-xs text-gray-500">
                                                            Word count: {countWords(data.essay.commitment)}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500 text-sm">Additional Information</dt>
                                                        <dd className="mt-2 text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded">
                                                            {data.essay.additional_information || 'Not provided'}
                                                        </dd>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Documents */}
                                            <div className="rounded-md border border-gray-200 p-4">
                                                <h4 className="text-sm font-semibold text-gray-900 mb-3">
                                                    Uploaded Documents
                                                </h4>
                                                <dl className="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm text-gray-700">
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Academic Documents</dt>
                                                        <dd className="mt-1">
                                                            {data.documents.academic_documents ? 
                                                                <span className="text-green-600">✓ {data.documents.academic_documents.name || 'Uploaded'}</span> : 
                                                                <span className="text-red-600">Not uploaded</span>
                                                            }
                                                        </dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">National ID</dt>
                                                        <dd className="mt-1">
                                                            {data.documents.national_id ? 
                                                                <span className="text-green-600">✓ {data.documents.national_id.name || 'Uploaded'}</span> : 
                                                                <span className="text-red-600">Not uploaded</span>
                                                            }
                                                        </dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Admission Form (Optional)</dt>
                                                        <dd className="mt-1">
                                                            {data.documents.admission_form ? 
                                                                <span className="text-green-600">✓ {data.documents.admission_form.name || 'Uploaded'}</span> : 
                                                                <span className="text-gray-500">Not uploaded</span>
                                                            }
                                                        </dd>
                                                    </div>
                                                    <div>
                                                        <dt className="font-medium text-gray-500">Provisional Results (Optional)</dt>
                                                        <dd className="mt-1">
                                                            {data.documents.provisional_results ? 
                                                                <span className="text-green-600">✓ {data.documents.provisional_results.name || 'Uploaded'}</span> : 
                                                                <span className="text-gray-500">Not uploaded</span>
                                                            }
                                                        </dd>
                                                    </div>
                                                </dl>
                                            </div>
                                        </div>
                                    )}
                                </motion.div>
                            </AnimatePresence>

                            <div className="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 pt-6">
                                <div className="text-sm text-gray-600">
                                    {savingDraft ? 'Saving draft...' : draftMessage}
                                </div>

                                <div className="flex flex-wrap gap-2">
                                    <Link href={route('portal')}>
                                        <SecondaryButton type="button">
                                            Back to Dashboard
                                        </SecondaryButton>
                                    </Link>

                                    {activeStep > 1 && (
                                        <SecondaryButton
                                            type="button"
                                            onClick={previousStep}
                                        >
                                            Previous
                                        </SecondaryButton>
                                    )}

                                    {!isLocked && (
                                        <SecondaryButton
                                            type="button"
                                            onClick={() =>
                                                saveDraft('Draft saved successfully.')
                                            }
                                            disabled={savingDraft}
                                        >
                                            Save Draft
                                        </SecondaryButton>
                                    )}

                                    {activeStep < STEP_CONFIG.length ? (
                                        <PrimaryButton type="button" onClick={nextStep}>
                                            Next Step
                                        </PrimaryButton>
                                    ) : (
                                        <PrimaryButton
                                            type="submit"
                                            disabled={processing || isLocked}
                                        >
                                            {processing
                                                ? 'Submitting...'
                                                : isLocked
                                                  ? 'Already Submitted'
                                                  : 'Submit Application'}
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
