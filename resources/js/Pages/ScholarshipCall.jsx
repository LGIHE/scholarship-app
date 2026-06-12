import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

const universities = [
    'Makerere University',
    'Kyambogo University',
    'Busitema University',
    'Islamic University in Uganda',
    'Gulu University',
    'Muni University',
    'Mountains of the Moon University',
    'Mbarara University of Science and Technology',
    'Uganda Martyrs University',
    'Kabale University',
];

const uniteCampuses = ['Kabale', 'Kaliro', 'Mubende', 'Muni', 'Unyama'];

const stemSubjects = [
    'Biology',
    'Chemistry',
    'Physics',
    'Mathematics',
    'Agriculture',
    'Computer Studies',
];

const tocSections = [
    { id: 'background', label: 'Background' },
    { id: 'eligibility', label: 'Who Can Apply?' },
    { id: 'benefits', label: 'Scholarship Benefits' },
    { id: 'how-to-apply', label: 'How to Apply' },
    { id: 'contact', label: 'Contact & Inquiries' },
];

export default function ScholarshipCall() {
    return (
        <>
            <Head title="2026/2027 Scholarship Call — LGF" />

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

                {/* Breadcrumb */}
                <div className="relative z-10 mx-auto max-w-7xl px-6 pt-6 lg:px-8">
                    <nav className="flex items-center gap-2 text-sm text-gray-500">
                        <Link href={route('scholarships')} className="hover:text-[#035A7D] transition">
                            Scholarships
                        </Link>
                        <span>/</span>
                        <span className="text-gray-900 font-medium">2026/2027 Call for Applications</span>
                    </nav>
                </div>

                <main className="relative z-10">

                    {/* Hero */}
                    <section className="mx-auto max-w-7xl px-6 py-10 lg:px-8">
                        <motion.div
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45 }}
                            className="max-w-4xl"
                        >
                            <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                                Application Call — 2026/2027 Academic Year
                            </p>
                            <h1 className="mt-3 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">
                                Female STEM Student Teachers' Scholarship
                            </h1>
                            <p className="mt-2 text-xl font-medium text-[#035A7D]">
                                Leaders in Teaching Uganda Program
                            </p>
                            <p className="mt-4 text-lg leading-8 text-gray-600">
                                The Leaders in Teaching Uganda Program is pleased to announce a call for
                                applications for <strong>400 scholarships</strong> for pre-service female
                                STEM students — Ugandan citizens, refugees, and young women with disabilities
                                — admitted to pursue a Bachelor of Science with Education (BScEd) during the
                                2026/2027 Academic Year.
                            </p>
                            <div className="mt-8 flex flex-wrap gap-4">
                                <Link
                                    href={route('register')}
                                    className="rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                                >
                                    Apply Now
                                </Link>
                                <a
                                    href="#eligibility"
                                    className="rounded-full bg-white px-6 py-3 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50"
                                >
                                    Check Eligibility
                                </a>
                            </div>
                        </motion.div>
                    </section>

                    {/* Deadline banner */}
                    <div className="border-y border-amber-200 bg-amber-50">
                        <div className="mx-auto flex max-w-7xl flex-wrap items-center gap-3 px-6 py-4 lg:px-8">
                            <span className="text-sm font-bold uppercase tracking-wide text-amber-700">
                                ⏰ Deadline:
                            </span>
                            <span className="font-semibold text-amber-800">July 15, 2026</span>
                            <span className="text-sm text-amber-600">
                                — Submit completed applications and supporting documents before this date.
                            </span>
                        </div>
                    </div>

                    {/* Two-column layout: ToC sidebar + content */}
                    <div className="mx-auto max-w-7xl px-6 py-12 lg:px-8">
                        <div className="lg:grid lg:grid-cols-[240px_1fr] lg:gap-12">

                            {/* Sticky ToC */}
                            <motion.aside
                                initial={{ opacity: 0, x: -12 }}
                                animate={{ opacity: 1, x: 0 }}
                                transition={{ duration: 0.45, delay: 0.1 }}
                                className="mb-8 lg:mb-0"
                            >
                                <div className="sticky top-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                    <h2 className="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        On this page
                                    </h2>
                                    <nav className="mt-4 space-y-1">
                                        {tocSections.map((s) => (
                                            <a
                                                key={s.id}
                                                href={`#${s.id}`}
                                                className="block rounded-lg px-3 py-2 text-sm text-gray-600 transition hover:bg-gray-50 hover:text-[#035A7D]"
                                            >
                                                {s.label}
                                            </a>
                                        ))}
                                    </nav>
                                    <div className="mt-6 space-y-2 border-t border-gray-100 pt-4">
                                        <Link
                                            href={route('register')}
                                            className="block rounded-full bg-[#035A7D] px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                                        >
                                            Apply Now
                                        </Link>
                                        <Link
                                            href={route('scholarships')}
                                            className="block text-center text-sm text-[#035A7D] hover:underline"
                                        >
                                            ← All Scholarships
                                        </Link>
                                    </div>
                                </div>
                            </motion.aside>

                            {/* Main content */}
                            <motion.div
                                initial={{ opacity: 0, y: 16 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.45, delay: 0.15 }}
                                className="space-y-12"
                            >

                                {/* Background */}
                                <section id="background" className="scroll-mt-8 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                                    <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                                        Background
                                    </p>
                                    <h2 className="mt-2 text-2xl font-bold text-gray-900">
                                        About the Leaders in Teaching Program
                                    </h2>
                                    <p className="mt-4 leading-8 text-gray-600">
                                        The Leaders in Teaching Uganda Program is a five-year Mastercard Foundation
                                        initiative aimed at transforming secondary education in Uganda by improving the
                                        quality of teaching and learning. Implemented under the strategic leadership and
                                        oversight of the Ministry of Education and Sports, the program is delivered
                                        through a consortium comprising Luigi Giussani Foundation (LGF), UNICEF,
                                        British Council, VVOB – education for all, Edukans International, Brainwave
                                        Careers Uganda, STiR Education, Promoting Equality in African Schools (PEAS)
                                        Uganda, Teach For Uganda, and the Forum for Education NGOs in Uganda (FENU).
                                    </p>
                                    <p className="mt-4 leading-8 text-gray-600">
                                        Anchored on the pillars of Teacher Recruitment, Teacher Training, School
                                        Leadership, and Teacher Motivation, the program seeks to increase both the
                                        quality and quantity of teachers in Uganda's secondary education system through
                                        inclusive, gender-responsive, and innovative approaches to education. As part
                                        of this commitment, the program is investing in the next generation of female
                                        STEM teachers through the provision of scholarships.
                                    </p>
                                </section>

                                {/* Eligibility */}
                                <section id="eligibility" className="scroll-mt-8 space-y-6">
                                    <div className="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                                            Eligibility
                                        </p>
                                        <h2 className="mt-2 text-2xl font-bold text-gray-900">Who Can Apply?</h2>
                                        <p className="mt-2 text-gray-600">
                                            Applicants must meet all of the following criteria:
                                        </p>
                                    </div>

                                    {/* Identity & Residency */}
                                    <div className="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                                        <h3 className="text-lg font-bold text-gray-900 mb-4">
                                            1. Identity &amp; Residency
                                        </h3>
                                        <ul className="space-y-3">
                                            {[
                                                'Female Ugandan citizen, or female with refugee status, or female with a disability residing in Uganda',
                                                'Young woman aged 18–35 years',
                                            ].map((item) => (
                                                <li key={item} className="flex items-start gap-3">
                                                    <span className="mt-0.5 shrink-0 font-bold text-[#035A7D]">✓</span>
                                                    <span className="text-sm leading-6 text-gray-600">{item}</span>
                                                </li>
                                            ))}
                                        </ul>
                                        <p className="mt-4 text-sm text-gray-500">
                                            Priority slots are reserved for: <strong>7%</strong> Refugees &amp; IDPs ·{' '}
                                            <strong>5%</strong> Persons with Disabilities ·{' '}
                                            <strong>20%</strong> In-service science &amp; maths teachers upgrading to BScEd.
                                        </p>
                                    </div>

                                    {/* Financial & Commitment */}
                                    <div className="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                                        <h3 className="text-lg font-bold text-gray-900 mb-4">
                                            2. Financial Need &amp; Teaching Commitment
                                        </h3>
                                        <ul className="space-y-3">
                                            {[
                                                'Demonstrate genuine financial need',
                                                'Demonstrate a strong commitment to pursuing a career in teaching in secondary education',
                                                'Be willing to serve in rural or underserved areas for a minimum of 2 years after graduation',
                                            ].map((item) => (
                                                <li key={item} className="flex items-start gap-3">
                                                    <span className="mt-0.5 shrink-0 font-bold text-[#035A7D]">✓</span>
                                                    <span className="text-sm leading-6 text-gray-600">{item}</span>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>

                                    {/* Academic Admission */}
                                    <div className="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                                        <h3 className="text-lg font-bold text-gray-900 mb-2">
                                            3. Academic Admission
                                        </h3>
                                        <p className="text-sm text-gray-600 mb-6">
                                            Must have secured admission to pursue a{' '}
                                            <strong>Bachelor of Science with Education (BScEd)</strong> with
                                            a STEM subject combination in{' '}
                                            <span className="font-medium text-gray-800">
                                                {stemSubjects.join(', ')}
                                            </span>{' '}
                                            at one of the following institutions:
                                        </p>
                                        <div className="grid gap-8 sm:grid-cols-2">
                                            <div>
                                                <p className="mb-3 text-xs font-semibold uppercase tracking-wider text-[#035A7D]">
                                                    Partner Universities
                                                </p>
                                                <ul className="space-y-2">
                                                    {universities.map((uni) => (
                                                        <li
                                                            key={uni}
                                                            className="flex items-center gap-2 text-sm text-gray-600"
                                                        >
                                                            <span className="font-bold text-[#035A7D]">✓</span>
                                                            {uni}
                                                        </li>
                                                    ))}
                                                </ul>
                                            </div>
                                            <div>
                                                <p className="mb-3 text-xs font-semibold uppercase tracking-wider text-[#035A7D]">
                                                    UNITE Campuses
                                                </p>
                                                <div className="flex flex-wrap gap-2">
                                                    {uniteCampuses.map((campus) => (
                                                        <span
                                                            key={campus}
                                                            className="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-sm font-medium text-[#035A7D]"
                                                        >
                                                            {campus}
                                                        </span>
                                                    ))}
                                                </div>
                                                <p className="mt-6 text-sm italic text-gray-500">
                                                    Note: In-service science and mathematics teachers intending to
                                                    upgrade to a BScEd Degree are also encouraged to apply.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                {/* Benefits */}
                                <section id="benefits" className="scroll-mt-8 space-y-6">
                                    <div className="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                                            What's Covered
                                        </p>
                                        <h2 className="mt-2 text-2xl font-bold text-gray-900">
                                            Scholarship Benefits
                                        </h2>
                                        <p className="mt-2 text-gray-600">
                                            The scholarship package covers the following for all successful beneficiaries:
                                        </p>
                                    </div>

                                    <div className="grid gap-6 sm:grid-cols-3">
                                        {[
                                            {
                                                icon: '🎓',
                                                title: 'Tuition & Functional Fees',
                                                description:
                                                    'Full coverage of tuition and functional fees for the entire duration of the BScEd program.',
                                            },
                                            {
                                                icon: '🏠',
                                                title: 'Accommodation Costs',
                                                description:
                                                    'Accommodation expenses fully covered, providing a safe and stable living environment to focus on studies.',
                                            },
                                            {
                                                icon: '💻',
                                                title: 'Laptop Computer',
                                                description:
                                                    'Each scholar receives a laptop to support academic work and access digital learning resources throughout the programme.',
                                            },
                                        ].map((benefit, i) => (
                                            <div
                                                key={benefit.title}
                                                className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
                                            >
                                                <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-3xl">
                                                    {benefit.icon}
                                                </div>
                                                <h3 className="font-bold text-gray-900">{benefit.title}</h3>
                                                <p className="mt-2 text-sm leading-6 text-gray-600">
                                                    {benefit.description}
                                                </p>
                                            </div>
                                        ))}
                                    </div>

                                    <div className="rounded-xl border border-blue-100 bg-blue-50 px-6 py-4 text-sm text-[#035A7D]">
                                        <strong>Service Requirement:</strong> Successful scholarship beneficiaries
                                        will be required to serve in the teaching profession for a minimum period of{' '}
                                        <strong>two years</strong> upon completion of their studies.
                                    </div>
                                </section>

                                {/* How to Apply */}
                                <section id="how-to-apply" className="scroll-mt-8 space-y-6">
                                    <div className="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                                            Application
                                        </p>
                                        <h2 className="mt-2 text-2xl font-bold text-gray-900">How to Apply</h2>
                                    </div>

                                    <div className="grid gap-6 lg:grid-cols-2">
                                        <div className="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                                            <h3 className="text-lg font-bold text-gray-900 mb-4">
                                                Online Application Steps
                                            </h3>
                                            <ol className="space-y-4">
                                                {[
                                                    <>
                                                        Visit{' '}
                                                        <a
                                                            href="https://scholarships.lgfug.org"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            className="text-[#035A7D] underline underline-offset-2"
                                                        >
                                                            scholarships.lgfug.org
                                                        </a>{' '}
                                                        or{' '}
                                                        <a
                                                            href="https://www.lgfug.org"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            className="text-[#035A7D] underline underline-offset-2"
                                                        >
                                                            www.lgfug.org
                                                        </a>
                                                    </>,
                                                    'Log in or create a new account',
                                                    'Follow the prompts to complete the application form',
                                                    'Upload all required supporting documents',
                                                    'Submit before the July 15, 2026 deadline',
                                                ].map((step, i) => (
                                                    <li key={i} className="flex items-start gap-4">
                                                        <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#035A7D] text-xs font-bold text-white">
                                                            {i + 1}
                                                        </span>
                                                        <span className="pt-0.5 text-sm leading-6 text-gray-600">
                                                            {step}
                                                        </span>
                                                    </li>
                                                ))}
                                            </ol>
                                        </div>

                                        <div className="space-y-4">
                                            <div className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                                <h3 className="text-base font-bold text-gray-900 mb-3">Need Help?</h3>
                                                <p className="text-sm text-gray-600 mb-3">
                                                    Contact our IT support officer for assistance:
                                                </p>
                                                <div className="space-y-1.5 text-sm text-gray-700">
                                                    <p>
                                                        <span className="font-semibold">Name:</span> Mr. Ian Murari
                                                        Buteera (IT Officer)
                                                    </p>
                                                    <p>
                                                        <span className="font-semibold">Phone:</span>{' '}
                                                        <a
                                                            href="tel:+256764078712"
                                                            className="text-[#035A7D] hover:underline"
                                                        >
                                                            (+256) 764 078712
                                                        </a>
                                                    </p>
                                                    <p>
                                                        <span className="font-semibold">Email:</span>{' '}
                                                        <a
                                                            href="mailto:imurari@lgfug.org"
                                                            className="text-[#035A7D] hover:underline"
                                                        >
                                                            imurari@lgfug.org
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>

                                            <div className="rounded-2xl border border-red-100 bg-red-50 p-6">
                                                <h3 className="mb-2 text-base font-bold text-red-800">
                                                    ⚠️ Important Notice
                                                </h3>
                                                <p className="text-sm leading-6 text-red-700">
                                                    No fees are charged for application, processing, selection,
                                                    verification, or award of the scholarship. Do not make any
                                                    payment to any individual or organisation claiming to facilitate
                                                    the scholarship process.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                {/* Contact & Inquiries */}
                                <section
                                    id="contact"
                                    className="scroll-mt-8 overflow-hidden rounded-2xl bg-gradient-to-r from-[#035A7D] to-[#024a6b] p-8 shadow-sm"
                                >
                                    <div className="grid gap-10 lg:grid-cols-2 lg:items-center">
                                        <div>
                                            <h2 className="text-2xl font-bold text-white">Ready to Apply?</h2>
                                            <p className="mt-3 text-blue-100">
                                                The Leaders in Teaching Uganda Program is committed to promoting equity
                                                and inclusion. Refugees, persons with disabilities, and young women from
                                                disadvantaged and underserved communities are strongly encouraged to apply.
                                            </p>
                                            <div className="mt-6 flex flex-wrap gap-4">
                                                <Link
                                                    href={route('register')}
                                                    className="rounded-full bg-white px-6 py-3 text-sm font-semibold text-[#035A7D] shadow transition hover:bg-blue-50"
                                                >
                                                    Start Application
                                                </Link>
                                                <a
                                                    href="mailto:info@lgfug.org"
                                                    className="rounded-full border border-white px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                                                >
                                                    Email Us
                                                </a>
                                            </div>
                                        </div>

                                        <div className="rounded-2xl border border-white/20 bg-white/10 p-6">
                                            <h3 className="mb-3 text-base font-bold text-white">
                                                Enquiries
                                            </h3>
                                            <p className="text-sm text-blue-100 mb-3">
                                                For questions about eligibility, the application process, or
                                                scholarship benefits:
                                            </p>
                                            <a
                                                href="mailto:info@lgfug.org"
                                                className="font-semibold text-white hover:underline"
                                            >
                                                info@lgfug.org
                                            </a>
                                            <div className="mt-6 border-t border-white/20 pt-5">
                                                <p className="text-sm font-semibold text-white">
                                                    Application Deadline
                                                </p>
                                                <p className="mt-1 text-2xl font-bold text-white">July 15, 2026</p>
                                                <p className="mt-1 text-sm text-blue-100">
                                                    No late submissions will be accepted
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                            </motion.div>
                        </div>
                    </div>

                </main>

                <PublicFooter />
            </div>
        </>
    );
}
