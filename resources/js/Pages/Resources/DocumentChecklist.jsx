import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useState } from 'react';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

const fadeUp = (delay = 0) => ({
    initial: { opacity: 0, y: 18 },
    animate: { opacity: 1, y: 0 },
    transition: { duration: 0.45, delay },
});

const categories = [
    {
        title: 'Identity & Citizenship Documents',
        icon: '🪪',
        required: true,
        description: 'You must provide at least one of the following, depending on your status.',
        items: [
            {
                name: 'National Identity Card (NIN)',
                detail: 'Required for Ugandan citizens. Ensure the card is valid and your name matches exactly what you enter in the application form.',
                required: true,
                tips: 'If your NIN card is being processed, a confirmation letter from NIRA is acceptable.',
            },
            {
                name: 'Refugee Identity Card or UNHCR Documentation',
                detail: 'Required for applicants with Refugee Status in Uganda. Must clearly show your name, photograph, and refugee identification number.',
                required: false,
                label: 'If applicable',
            },
            {
                name: 'Disability Documentation',
                detail: 'For applicants applying under the persons with disabilities quota. An assessment letter or certificate from a recognised medical or government authority.',
                required: false,
                label: 'If applicable',
            },
        ],
    },
    {
        title: 'Admission & Enrolment Documents',
        icon: '🏫',
        required: true,
        description: 'Proof that you have been admitted to or are in the process of being admitted to a LiT partner university or UNITE campus for a BScEd programme.',
        items: [
            {
                name: 'Admission / Acceptance Letter',
                detail: 'An official letter from your university or UNITE campus confirming your admission to the BScEd programme for the relevant academic year. Must show your name, programme title, and institution name.',
                required: true,
                tips: 'If you have not yet received your formal letter, a provisional admission letter or provisional selection notice is acceptable.',
            },
            {
                name: 'Student ID Card (if already enrolled)',
                detail: 'If you are a continuing student or upgrading from a certificate programme, include your current student identity card.',
                required: false,
                label: 'If applicable',
            },
        ],
    },
    {
        title: 'Academic Transcripts & Certificates',
        icon: '🎓',
        required: true,
        description: 'Documents showing your academic history. Scanned copies must be clear and complete — partial or cut-off pages will not be accepted.',
        items: [
            {
                name: 'Uganda Certificate of Education (UCE) Results',
                detail: 'Your O-Level results slip or certificate issued by UNEB. Must show your name, candidate number, and subject results.',
                required: true,
            },
            {
                name: 'Uganda Advanced Certificate of Education (UACE) Results',
                detail: 'Your A-Level results slip or certificate issued by UNEB. Must clearly show your principal and subsidiary subjects and grades.',
                required: true,
            },
            {
                name: 'Diploma or Certificate Transcripts',
                detail: 'Required only if you are applying as an in-service teacher upgrading from a diploma or certificate qualification.',
                required: false,
                label: 'If applicable (in-service teachers)',
            },
        ],
    },
    {
        title: 'Financial Need Documentation',
        icon: '💼',
        required: true,
        description: 'Evidence demonstrating that you require financial assistance to complete your studies. The committee understands that financial circumstances vary — provide whatever documentation best reflects your situation.',
        items: [
            {
                name: 'Financial Need Declaration / Statement',
                detail: 'A written declaration describing your household financial situation. Include details such as household income, number of dependants, and any other relevant financial circumstances.',
                required: true,
                tips: 'This can be a signed personal statement. Be honest and specific — vague declarations are less persuasive than detailed ones.',
            },
            {
                name: 'Supporting Letter from LC1 / Community Leader',
                detail: 'A letter from your local council (LC1) chairperson, parish chief, or a recognised community leader confirming your financial circumstances.',
                required: false,
                label: 'Strongly recommended',
                tips: 'This adds credibility to your financial declaration and is strongly recommended.',
            },
            {
                name: 'Proof of Guardian / Parent Income',
                detail: 'Where available, a payslip, tax document, or employment letter for your parent or guardian. If your guardian is unemployed or a subsistence farmer, a brief explanatory letter is sufficient.',
                required: false,
                label: 'Where available',
            },
        ],
    },
    {
        title: 'Photograph',
        icon: '📸',
        required: true,
        description: 'A recent passport-sized photograph.',
        items: [
            {
                name: 'Recent Passport-Sized Photograph',
                detail: 'A clear, recent photograph of yourself. Plain background preferred. The photograph should show your full face and shoulders clearly.',
                required: true,
                tips: 'The photograph does not need to be taken professionally — a clear phone photo with a plain background works well.',
            },
        ],
    },
    {
        title: 'Additional Documents for Special Categories',
        icon: '📎',
        required: false,
        description: 'Only required if you are applying under a specific category.',
        items: [
            {
                name: 'Teaching Service Letter or Employment Confirmation',
                detail: 'Required for in-service teachers applying to upgrade to a BScEd. Must be from your school headteacher or district education officer, confirming your current teaching role and subjects taught.',
                required: false,
                label: 'In-service teachers only',
            },
            {
                name: 'IDP / Displacement Documentation',
                detail: 'For applicants who are Internally Displaced Persons (IDPs). Documentation from a relevant authority confirming your displacement status.',
                required: false,
                label: 'IDPs only',
            },
        ],
    },
];

const tips = [
    { icon: '📱', tip: 'Scan documents or take clear photographs in good lighting. Dark or blurry uploads may be rejected.' },
    { icon: '📁', tip: 'Save your documents as PDFs or high-quality JPEGs before uploading. Keep file sizes below 5 MB per document.' },
    { icon: '🔤', tip: 'Ensure your name appears the same way on all documents and matches what you enter in the application form exactly.' },
    { icon: '🗂️', tip: 'Organise and label your files clearly before your application session so uploading is quick and straightforward.' },
    { icon: '📋', tip: 'Use this checklist to tick off each item before you start your application — it will save time later.' },
    { icon: '❓', tip: 'If you are unsure whether a document qualifies, contact our support team before submitting.' },
];

export default function DocumentChecklist() {
    const [checked, setChecked] = useState({});

    function toggle(key) {
        setChecked(prev => ({ ...prev, [key]: !prev[key] }));
    }

    const allRequired = categories.flatMap((cat, ci) =>
        cat.items
            .filter(item => item.required)
            .map((_, ii) => `${ci}-${ii}`)
    );
    const checkedCount = allRequired.filter(k => checked[k]).length;

    return (
        <>
            <Head title="Document Checklist — LiT-Uganda Scholarship" />

            <div className="relative min-h-screen overflow-hidden bg-gray-50 text-gray-900 selection:bg-[#035A7D] selection:text-white">
                <div
                    className="pointer-events-none absolute inset-x-0 -top-48 -z-10 transform-gpu overflow-hidden blur-3xl"
                    aria-hidden="true"
                >
                    <div
                        className="relative left-1/2 aspect-[1155/678] w-[70rem] -translate-x-1/2 rotate-[20deg] bg-gradient-to-tr from-[#4A90E2] to-[#035A7D] opacity-20"
                        style={{
                            clipPath:
                                'polygon(74.1% 44.1%,100% 61.6%,97.5% 26.9%,85.5% 0.1%,80.7% 2%,72.5% 32.5%,60.2% 62.4%,52.4% 68.1%,47.5% 58.3%,45.2% 34.5%,27.5% 76.7%,0.1% 64.9%,17.9% 100%,27.6% 76.8%,76.1% 97.7%,74.1% 44.1%)',
                        }}
                    />
                </div>

                <PublicHeader currentRoute="resources" />

                {/* Breadcrumb */}
                <div className="relative z-10 mx-auto max-w-7xl px-6 pt-6 lg:px-8">
                    <nav className="flex items-center gap-2 text-sm text-gray-500" aria-label="Breadcrumb">
                        <Link href={route('resources')} className="hover:text-[#035A7D] transition">Resources</Link>
                        <span aria-hidden="true">/</span>
                        <span className="font-medium text-gray-900">Document Checklist</span>
                    </nav>
                </div>

                <main className="relative z-10 mx-auto w-full max-w-4xl px-6 py-10 lg:px-8">

                    {/* Heading */}
                    <motion.div {...fadeUp(0)}>
                        <div className="flex items-center gap-3">
                            <span className="text-4xl" aria-hidden="true">📄</span>
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">Application Resources</p>
                                <h1 className="mt-1 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">Document Checklist</h1>
                            </div>
                        </div>
                        <p className="mt-5 text-lg text-gray-600 leading-relaxed">
                            Everything you need to gather before you start your application.
                            Having your documents ready in advance makes the process much smoother.
                        </p>
                    </motion.div>

                    {/* Progress tracker */}
                    <motion.div
                        {...fadeUp(0.1)}
                        className="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
                        aria-label={`${checkedCount} of ${allRequired.length} required items checked`}
                    >
                        <div className="flex items-center justify-between text-sm mb-3">
                            <span className="font-semibold text-gray-700">Required items checked off</span>
                            <span className="font-bold text-[#035A7D]">{checkedCount} / {allRequired.length}</span>
                        </div>
                        <div className="h-2.5 w-full rounded-full bg-gray-100 overflow-hidden">
                            <div
                                className="h-full rounded-full bg-[#035A7D] transition-all duration-500"
                                style={{ width: `${(checkedCount / allRequired.length) * 100}%` }}
                                role="progressbar"
                                aria-valuenow={checkedCount}
                                aria-valuemin={0}
                                aria-valuemax={allRequired.length}
                            />
                        </div>
                        {checkedCount === allRequired.length && (
                            <p className="mt-3 text-sm font-semibold text-green-700">
                                ✅ All required documents checked — you're ready to apply!
                            </p>
                        )}
                    </motion.div>

                    {/* Categories */}
                    <div className="mt-6 space-y-6">
                        {categories.map((cat, ci) => (
                            <motion.section
                                key={ci}
                                initial={{ opacity: 0, y: 16 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.4, delay: ci * 0.05 }}
                                className="rounded-2xl border border-gray-200 bg-white p-7 shadow-sm"
                                aria-labelledby={`cat-${ci}`}
                            >
                                <div className="flex items-center gap-3 mb-1">
                                    <span className="text-2xl" aria-hidden="true">{cat.icon}</span>
                                    <h2 id={`cat-${ci}`} className="text-xl font-bold text-gray-900">{cat.title}</h2>
                                    {!cat.required && (
                                        <span className="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                            Conditional
                                        </span>
                                    )}
                                </div>
                                <p className="text-sm text-gray-500 mb-5 ml-9">{cat.description}</p>

                                <ul className="space-y-4">
                                    {cat.items.map((item, ii) => {
                                        const key = `${ci}-${ii}`;
                                        const isChecked = !!checked[key];
                                        return (
                                            <li
                                                key={ii}
                                                className={`rounded-xl border p-4 transition ${
                                                    isChecked
                                                        ? 'border-green-200 bg-green-50'
                                                        : 'border-gray-100 bg-gray-50'
                                                }`}
                                            >
                                                <div className="flex items-start gap-3">
                                                    <button
                                                        type="button"
                                                        onClick={() => toggle(key)}
                                                        aria-label={isChecked ? `Uncheck ${item.name}` : `Check ${item.name}`}
                                                        aria-pressed={isChecked}
                                                        className={`mt-0.5 shrink-0 flex h-5 w-5 items-center justify-center rounded border-2 transition ${
                                                            isChecked
                                                                ? 'border-green-600 bg-green-600 text-white'
                                                                : 'border-gray-300 bg-white'
                                                        }`}
                                                    >
                                                        {isChecked && (
                                                            <svg viewBox="0 0 12 12" fill="currentColor" className="h-3 w-3" aria-hidden="true">
                                                                <path d="M10.28 1.28L4 7.56 1.72 5.28a1 1 0 00-1.44 1.44l3 3a1 1 0 001.44 0l7-7a1 1 0 00-1.44-1.44z" />
                                                            </svg>
                                                        )}
                                                    </button>
                                                    <div className="flex-1 min-w-0">
                                                        <div className="flex flex-wrap items-center gap-2 mb-1">
                                                            <p className="font-semibold text-gray-900 text-sm">{item.name}</p>
                                                            {item.required ? (
                                                                <span className="rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-600 border border-red-100">
                                                                    Required
                                                                </span>
                                                            ) : (
                                                                <span className="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 border border-amber-100">
                                                                    {item.label}
                                                                </span>
                                                            )}
                                                        </div>
                                                        <p className="text-sm text-gray-600 leading-relaxed">{item.detail}</p>
                                                        {item.tips && (
                                                            <p className="mt-2 text-xs text-[#035A7D] bg-blue-50 rounded-lg px-3 py-2 border border-blue-100">
                                                                💡 {item.tips}
                                                            </p>
                                                        )}
                                                    </div>
                                                </div>
                                            </li>
                                        );
                                    })}
                                </ul>
                            </motion.section>
                        ))}
                    </div>

                    {/* Preparation tips */}
                    <motion.section
                        {...fadeUp(0.2)}
                        className="mt-8 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm"
                        aria-labelledby="tips-heading"
                    >
                        <h2 id="tips-heading" className="text-xl font-bold text-gray-900">Document Preparation Tips</h2>
                        <div className="mt-5 grid gap-4 sm:grid-cols-2">
                            {tips.map((t, i) => (
                                <div key={i} className="flex items-start gap-3 rounded-xl bg-gray-50 border border-gray-100 p-4">
                                    <span className="text-xl shrink-0" aria-hidden="true">{t.icon}</span>
                                    <p className="text-sm text-gray-700 leading-relaxed">{t.tip}</p>
                                </div>
                            ))}
                        </div>
                    </motion.section>

                    {/* Related resources */}
                    <motion.section
                        {...fadeUp(0.25)}
                        className="mt-8 rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 to-[#035A7D]/5 p-8 shadow-sm"
                        aria-labelledby="related-heading"
                    >
                        <h2 id="related-heading" className="text-lg font-bold text-gray-900">Related Resources</h2>
                        <div className="mt-4 flex flex-wrap gap-3">
                            <Link
                                href={route('resources.application-guide')}
                                className="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-[#035A7D]/30 hover:text-[#035A7D]"
                            >
                                <span aria-hidden="true">📋</span> Application Guide
                            </Link>
                            <Link
                                href={route('resources.essay-tips')}
                                className="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-[#035A7D]/30 hover:text-[#035A7D]"
                            >
                                <span aria-hidden="true">✍️</span> Essay Writing Tips
                            </Link>
                            <Link
                                href={route('faq')}
                                className="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-[#035A7D]/30 hover:text-[#035A7D]"
                            >
                                <span aria-hidden="true">❓</span> FAQs
                            </Link>
                        </div>
                        <div className="mt-6">
                            <Link
                                href={route('register')}
                                className="inline-block rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                            >
                                Start Your Application →
                            </Link>
                        </div>
                    </motion.section>

                </main>

                <PublicFooter />
            </div>
        </>
    );
}
