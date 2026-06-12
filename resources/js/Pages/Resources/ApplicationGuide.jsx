import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

const fadeUp = (delay = 0) => ({
    initial: { opacity: 0, y: 18 },
    animate: { opacity: 1, y: 0 },
    transition: { duration: 0.45, delay },
});

const steps = [
    {
        num: '01',
        title: 'Check Your Eligibility',
        duration: '5 minutes',
        content: [
            'Before starting your application, confirm you meet all of the following criteria:',
        ],
        checklist: [
            'You are a female Ugandan citizen, hold Refugee Status in Uganda, or are a female youth with a disability in Uganda.',
            'You are aged between 18 and 35 years.',
            'You have received, or are in the process of receiving, an admission offer for a Bachelor of Science with Education (BScEd) at a LiT partner university or UNITE campus.',
            'You are pursuing a STEM subject combination (Biology, Chemistry, Physics, Mathematics, Agriculture, or Computer Studies).',
            'You can demonstrate genuine financial need.',
            'You are committed to teaching in a secondary school — particularly in a rural or underserved community — for at least 2 years after graduation.',
        ],
    },
    {
        num: '02',
        title: 'Create Your Account',
        duration: '5 minutes',
        content: [
            'Visit the registration page and create a free account using a valid email address you can access. You will receive a verification email — click the link in it to activate your account.',
            'Once verified, log in to access your application dashboard. Your progress is saved automatically at every step, so you can return at any time.',
        ],
        checklist: null,
    },
    {
        num: '03',
        title: 'Gather Your Documents',
        duration: '1–2 days',
        content: [
            'Before filling in the form, collect all required documents so you can upload them without interruption. See the Document Checklist resource for the full list. Key documents include:',
        ],
        checklist: [
            'National Identity Card, Refugee ID, or relevant disability documentation.',
            'Admission letter or acceptance letter from your LiT partner institution.',
            'Academic transcripts or certificates (UCE and/or UACE).',
            'Evidence of financial need (e.g. a financial declaration or supporting letter).',
            'Any other supporting documentation relevant to your situation.',
        ],
    },
    {
        num: '04',
        title: 'Complete the Application Form',
        duration: '30–60 minutes',
        content: [
            'The application is divided into clear sections. Work through each one carefully — incomplete sections will be flagged before you can submit.',
        ],
        checklist: [
            'Personal Information — your name, contact details, date of birth, nationality, and identity documentation.',
            'Academic Background — your education history, current institution, and BScEd subject combination.',
            'Financial Information — your household income situation and evidence of financial need.',
            'Commitment to Teaching — a written statement on why you want to teach and your willingness to serve in underserved schools.',
            'Supporting Documents — upload scanned copies or clear photographs of all required documents.',
        ],
    },
    {
        num: '05',
        title: 'Write Your Personal Statement',
        duration: '1–2 hours',
        content: [
            'The personal statement is one of the most important parts of your application. Use it to tell the selection committee who you are, why you want to become a STEM teacher, and what drives your commitment to education in Uganda.',
            'Be honest, specific, and personal. Generic statements are easy to spot. See the Essay Writing Tips resource for detailed guidance on crafting a strong statement.',
        ],
        checklist: null,
    },
    {
        num: '06',
        title: 'Review and Submit',
        duration: '15 minutes',
        content: [
            'Before submitting, carefully review every section of your application. Check that:',
        ],
        checklist: [
            'All personal and academic details are accurate and match your documents.',
            'All required documents have been uploaded and are legible.',
            'Your personal statement is complete and polished.',
            'You have read and agree to the terms and conditions of the scholarship.',
        ],
        note: 'Once submitted, you cannot edit your application. If you spot a critical error immediately after submitting, contact us through the Contact page as quickly as possible.',
    },
    {
        num: '07',
        title: 'Track Your Application',
        duration: 'Ongoing',
        content: [
            'After submitting, you can monitor the status of your application from your dashboard. You will also receive email notifications as your application moves through the review process.',
            'The selection committee reviews all applications after the deadline. The full timeline for results will be communicated at the start of each call.',
        ],
        checklist: null,
    },
];

const tips = [
    { icon: '⏰', tip: 'Start early — give yourself time to gather documents and write a strong personal statement.' },
    { icon: '📱', tip: 'Use a stable internet connection when uploading documents to avoid upload failures.' },
    { icon: '🖨️', tip: 'Ensure scanned documents are clear and legible — blurry or incomplete documents may delay processing.' },
    { icon: '📧', tip: 'Use an email address you check regularly, as all notifications will be sent there.' },
    { icon: '💾', tip: 'Your progress saves automatically, but always log out properly to protect your account.' },
    { icon: '📞', tip: 'If you get stuck, contact our support team — we are here to help.' },
];

export default function ApplicationGuide() {
    return (
        <>
            <Head title="Application Guide — LiT-Uganda Scholarship" />

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
                        <span className="font-medium text-gray-900">Application Guide</span>
                    </nav>
                </div>

                <main className="relative z-10 mx-auto w-full max-w-4xl px-6 py-10 lg:px-8">

                    {/* Heading */}
                    <motion.div {...fadeUp(0)}>
                        <div className="flex items-center gap-3">
                            <span className="text-4xl" aria-hidden="true">📋</span>
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">Application Resources</p>
                                <h1 className="mt-1 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">Application Guide</h1>
                            </div>
                        </div>
                        <p className="mt-5 text-lg text-gray-600 leading-relaxed">
                            A step-by-step walkthrough of everything you need to do to submit a
                            complete, competitive application for the LiT-Uganda Female STEM Student
                            Teachers' Scholarship.
                        </p>
                        <div className="mt-4 flex items-center gap-4 text-sm text-gray-500">
                            <span className="flex items-center gap-1.5">
                                <span aria-hidden="true">⏱️</span> Total time: approximately 2–3 hours (spread over 1–2 days)
                            </span>
                        </div>
                    </motion.div>

                    {/* Steps */}
                    <div className="mt-12 space-y-6">
                        {steps.map((step, i) => (
                            <motion.section
                                key={step.num}
                                initial={{ opacity: 0, y: 16 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.4, delay: i * 0.05 }}
                                className="rounded-2xl border border-gray-200 bg-white p-7 shadow-sm"
                                aria-labelledby={`step-${step.num}`}
                            >
                                <div className="flex items-start gap-5">
                                    <span
                                        className="shrink-0 text-3xl font-bold text-[#035A7D]/20 tabular-nums leading-none"
                                        aria-hidden="true"
                                    >
                                        {step.num}
                                    </span>
                                    <div className="flex-1 min-w-0">
                                        <div className="flex flex-wrap items-center gap-3">
                                            <h2 id={`step-${step.num}`} className="text-xl font-bold text-gray-900">
                                                {step.title}
                                            </h2>
                                            <span className="rounded-full bg-blue-50 px-3 py-0.5 text-xs font-medium text-[#035A7D] border border-blue-100">
                                                {step.duration}
                                            </span>
                                        </div>

                                        <div className="mt-3 space-y-2">
                                            {step.content.map((para, pi) => (
                                                <p key={pi} className="text-gray-600 leading-relaxed">{para}</p>
                                            ))}
                                        </div>

                                        {step.checklist && (
                                            <ul className="mt-4 space-y-2">
                                                {step.checklist.map((item, ci) => (
                                                    <li key={ci} className="flex items-start gap-2.5 text-sm text-gray-700">
                                                        <span className="mt-0.5 shrink-0 flex h-5 w-5 items-center justify-center rounded-full bg-[#035A7D]/10 text-[#035A7D] text-xs font-bold" aria-hidden="true">✓</span>
                                                        {item}
                                                    </li>
                                                ))}
                                            </ul>
                                        )}

                                        {step.note && (
                                            <div className="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                                <strong>Note:</strong> {step.note}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </motion.section>
                        ))}
                    </div>

                    {/* Tips */}
                    <motion.section
                        {...fadeUp(0.2)}
                        className="mt-10 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm"
                        aria-labelledby="tips-heading"
                    >
                        <h2 id="tips-heading" className="text-2xl font-bold text-gray-900">Quick Tips</h2>
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
                                href={route('resources.essay-tips')}
                                className="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-[#035A7D]/30 hover:text-[#035A7D]"
                            >
                                <span aria-hidden="true">✍️</span> Essay Writing Tips
                            </Link>
                            <Link
                                href={route('resources.document-checklist')}
                                className="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-[#035A7D]/30 hover:text-[#035A7D]"
                            >
                                <span aria-hidden="true">📄</span> Document Checklist
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
