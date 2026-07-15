import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

const objectives = [
    {
        icon: '👩‍🔬',
        title: 'Increase Female Participation in STEM Teaching',
        description:
            'Grow the number of women entering STEM teaching roles in secondary education across Uganda.',
    },
    {
        icon: '⚖️',
        title: 'Promote Equity and Inclusion in Education',
        description:
            'Break down barriers and create pathways that ensure every woman has equal access to quality education.',
    },
    {
        icon: '🏫',
        title: 'Expand Qualified Female Science Teachers',
        description:
            "Address the critical shortage of qualified female science teachers in Uganda's secondary schools.",
    },
    {
        icon: '🌟',
        title: 'Empower Female Role Models in Education',
        description:
            'Inspire the next generation of girls by cultivating visible, confident female leaders in STEM education.',
    },
];

const stats = [
    { stat: '1,000', label: 'Total Scholarships' },
    { stat: '100%', label: 'Young women aged 18–35' },
    { stat: '7%', label: 'Refugees & IDPs' },
    { stat: '5%', label: 'Persons with Disabilities' },
];

export default function Scholarships() {
    const { activeCohort, allCohorts } = usePage().props;

    // Past cohorts = all non-active ones, already sorted desc by id from backend
    const pastCohorts = (allCohorts ?? []).filter((c) => !c.is_active);

    return (
        <>
            <Head title="Scholarships — LGF" />

            <div className="relative min-h-screen bg-gray-50 text-gray-900 selection:bg-[#035A7D] selection:text-white">
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

                <PublicHeader currentRoute="scholarships" />

                <main className="relative z-10">

                    {/* Hero */}
                    <section className="mx-auto max-w-7xl px-6 py-14 lg:px-8">
                        <motion.div
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45 }}
                            className="max-w-3xl"
                        >
                            <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                                LIT-Uganda Program
                            </p>
                            <h1 className="mt-3 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">
                                Female STEM Student Teachers' Scholarships
                            </h1>
                            <p className="mt-4 text-lg leading-8 text-gray-600">
                                The Leaders in Teaching Uganda Program is investing in the next generation of
                                female STEM educators — providing 1,000 scholarships to women who will transform
                                secondary education across Uganda.
                            </p>
                            <div className="mt-8 flex flex-wrap gap-4">
                                <a
                                    href="#current-call"
                                    className="rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                                >
                                    View Current Call
                                </a>
                                <a
                                    href="#programme"
                                    className="rounded-full bg-white px-6 py-3 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50"
                                >
                                    About the Programme
                                </a>
                            </div>
                        </motion.div>
                    </section>

                    {/* Stats bar */}
                    <div className="bg-gradient-to-r from-[#035A7D] to-[#024a6b] py-14">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="grid grid-cols-2 gap-y-10 text-center lg:grid-cols-4">
                                {stats.map((item, i) => (
                                    <motion.div
                                        key={item.label}
                                        initial={{ opacity: 0, y: 10 }}
                                        whileInView={{ opacity: 1, y: 0 }}
                                        viewport={{ once: true }}
                                        transition={{ delay: i * 0.1 }}
                                        className="text-white"
                                    >
                                        <div className="text-4xl font-bold">{item.stat}</div>
                                        <div className="mt-2 text-sm text-blue-100">{item.label}</div>
                                    </motion.div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* About the programme */}
                    <section id="programme" className="bg-white py-16 scroll-mt-8">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <motion.div
                                initial={{ opacity: 0, y: 16 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.45 }}
                                className="max-w-4xl"
                            >
                                <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                                    Background
                                </p>
                                <h2 className="mt-2 text-3xl font-bold text-gray-900 sm:text-4xl">
                                    About the Leaders in Teaching Uganda Program
                                </h2>
                                <p className="mt-6 text-lg leading-8 text-gray-600">
                                    The Leaders in Teaching Uganda Program is a five-year Mastercard Foundation
                                    initiative aimed at transforming secondary education in Uganda by improving the
                                    quality of teaching and learning. Implemented under the strategic leadership and
                                    oversight of the Ministry of Education and Sports, the program is delivered through
                                    a consortium comprising Luigi Giussani Foundation (LGF), UNICEF, British Council,
                                    VVOB – education for all, Edukans International, Brainwave Careers Uganda, STiR
                                    Education, PEAS Uganda, Teach For Uganda, and the Forum for Education NGOs in
                                    Uganda (FENU).
                                </p>
                                <p className="mt-4 text-lg leading-8 text-gray-600">
                                    Anchored on the pillars of Teacher Recruitment, Teacher Training, School
                                    Leadership, and Teacher Motivation, the program seeks to increase both the quality
                                    and quantity of teachers in Uganda's secondary education system through inclusive,
                                    gender-responsive, and innovative approaches. As part of this commitment, the
                                    program is investing in the next generation of female STEM teachers through the
                                    provision of 1,000 scholarships for women pursuing a Bachelor of Science with
                                    Education (BScEd).
                                </p>
                            </motion.div>
                        </div>
                    </section>

                    {/* Objectives */}
                    <section className="bg-gray-50 py-16">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <motion.div
                                initial={{ opacity: 0, y: 16 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.45 }}
                                className="mb-12 max-w-2xl"
                            >
                                <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                                    Purpose
                                </p>
                                <h2 className="mt-2 text-3xl font-bold text-gray-900 sm:text-4xl">
                                    Objectives of the Scholarship Programme
                                </h2>
                            </motion.div>

                            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                                {objectives.map((obj, i) => (
                                    <motion.div
                                        key={obj.title}
                                        initial={{ opacity: 0, y: 20 }}
                                        whileInView={{ opacity: 1, y: 0 }}
                                        viewport={{ once: true }}
                                        transition={{ duration: 0.45, delay: i * 0.1 }}
                                        className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
                                    >
                                        <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-3xl">
                                            {obj.icon}
                                        </div>
                                        <h3 className="text-sm font-bold text-gray-900">{obj.title}</h3>
                                        <p className="mt-2 text-sm leading-6 text-gray-600">{obj.description}</p>
                                    </motion.div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Current / Active Call */}
                    <section id="current-call" className="bg-white py-16 scroll-mt-8">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <motion.div
                                initial={{ opacity: 0, y: 16 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.45 }}
                                className="mb-10"
                            >
                                <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                                    Scholarship Calls
                                </p>
                                <h2 className="mt-2 text-3xl font-bold text-gray-900 sm:text-4xl">
                                    Active &amp; Upcoming Calls
                                </h2>
                            </motion.div>

                            {activeCohort ? (
                                <motion.div
                                    initial={{ opacity: 0, y: 16 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true }}
                                    transition={{ duration: 0.45, delay: 0.1 }}
                                    className="relative overflow-hidden rounded-2xl border-2 border-[#035A7D] bg-gradient-to-br from-[#035A7D]/5 to-blue-50 p-8 shadow-sm"
                                >
                                    {/* Status badge */}
                                    {activeCohort.is_open ? (
                                        <span className="absolute right-6 top-6 inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            <span className="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse" />
                                            Now Open
                                        </span>
                                    ) : activeCohort.deadline_passed ? (
                                        <span className="absolute right-6 top-6 inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Closed
                                        </span>
                                    ) : (
                                        <span className="absolute right-6 top-6 inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                            Coming Soon
                                        </span>
                                    )}

                                    <div className="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
                                        <div>
                                            <p className="text-xs font-semibold uppercase tracking-[0.2em] text-[#035A7D]">
                                                {activeCohort.academic_year} Academic Year
                                            </p>
                                            <h3 className="mt-2 text-2xl font-bold text-gray-900">
                                                Female STEM Student Teachers' Scholarship — Call for Applications
                                            </h3>
                                            {activeCohort.description && (
                                                <p className="mt-3 text-gray-600">{activeCohort.description}</p>
                                            )}

                                            <div className="mt-6 flex flex-wrap gap-6 text-sm">
                                                <div>
                                                    <span className="font-semibold text-gray-700">Scholarships available</span>
                                                    <p className="text-[#035A7D] font-bold text-lg">
                                                        {activeCohort.scholarships_available.toLocaleString()}
                                                    </p>
                                                </div>
                                                {activeCohort.deadline_label && (
                                                    <div>
                                                        <span className="font-semibold text-gray-700">Application deadline</span>
                                                        <p className={`font-bold text-lg ${activeCohort.deadline_passed ? 'text-red-700' : 'text-amber-700'}`}>
                                                            {activeCohort.deadline_label}
                                                        </p>
                                                    </div>
                                                )}
                                                <div>
                                                    <span className="font-semibold text-gray-700">Award includes</span>
                                                    <p className="text-gray-600">Tuition · Accommodation · Laptop</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="flex flex-col gap-3 lg:items-end lg:min-w-[180px]">
                                            <Link
                                                href={route('scholarship.call', { slug: activeCohort.slug })}
                                                className="rounded-full bg-[#035A7D] px-6 py-3 text-center text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                                            >
                                                View Full Details
                                            </Link>
                                            {!activeCohort.deadline_passed && (
                                                <Link
                                                    href={route('register')}
                                                    className="rounded-full bg-white px-6 py-3 text-center text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50"
                                                >
                                                    Apply Now
                                                </Link>
                                            )}
                                        </div>
                                    </div>
                                </motion.div>
                            ) : (
                                <motion.div
                                    initial={{ opacity: 0, y: 16 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true }}
                                    transition={{ duration: 0.45, delay: 0.1 }}
                                    className="rounded-2xl border border-gray-200 bg-gray-50 p-8 text-center text-gray-500"
                                >
                                    <p className="text-lg font-medium">No active scholarship call at this time.</p>
                                    <p className="mt-2 text-sm">Check back soon or contact us for updates.</p>
                                </motion.div>
                            )}

                            {/* Past Calls */}
                            {pastCohorts.length > 0 && (
                                <div className="mt-12">
                                    <h3 className="text-lg font-bold text-gray-900 mb-4">Previous Calls</h3>
                                    <div className="space-y-4">
                                        {pastCohorts.map((cohort, i) => (
                                            <motion.div
                                                key={cohort.id}
                                                initial={{ opacity: 0, y: 12 }}
                                                whileInView={{ opacity: 1, y: 0 }}
                                                viewport={{ once: true }}
                                                transition={{ duration: 0.35, delay: i * 0.07 }}
                                                className="flex items-center justify-between rounded-xl border border-gray-200 bg-white px-6 py-4 shadow-sm"
                                            >
                                                <div>
                                                    <p className="font-semibold text-gray-900">{cohort.name}</p>
                                                    <p className="text-sm text-gray-500">
                                                        {cohort.academic_year}
                                                        {cohort.deadline_label && ` · Deadline: ${cohort.deadline_label}`}
                                                    </p>
                                                </div>
                                                <Link
                                                    href={route('scholarship.call', { slug: cohort.slug })}
                                                    className="text-sm font-medium text-[#035A7D] hover:underline"
                                                >
                                                    View Details →
                                                </Link>
                                            </motion.div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    </section>

                    {/* CTA */}
                    <section className="bg-gradient-to-r from-[#035A7D] to-[#024a6b] py-16">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8 text-center">
                            <motion.div
                                initial={{ opacity: 0, y: 16 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.45 }}
                            >
                                <h2 className="text-3xl font-bold text-white sm:text-4xl">
                                    Ready to Start Your Journey?
                                </h2>
                                <p className="mt-4 text-lg text-blue-100">
                                    Refugees, persons with disabilities, and young women from disadvantaged
                                    communities are strongly encouraged to apply.
                                </p>
                                <div className="mt-8 flex flex-wrap justify-center gap-4">
                                    <Link
                                        href={route('register')}
                                        className="rounded-full bg-white px-6 py-3 text-sm font-semibold text-[#035A7D] shadow transition hover:bg-blue-50"
                                    >
                                        Apply Now
                                    </Link>
                                    <Link
                                        href={route('contact')}
                                        className="rounded-full border border-white px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                                    >
                                        Contact Us
                                    </Link>
                                </div>
                            </motion.div>
                        </div>
                    </section>

                </main>

                <PublicFooter />
            </div>
        </>
    );
}
