import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

const fadeUp = (delay = 0) => ({
    initial: { opacity: 0, y: 18 },
    animate: { opacity: 1, y: 0 },
    transition: { duration: 0.45, delay },
});

const partners = [
    { name: 'Luigi Giussani Foundation (LGF)', role: 'Lead Implementer' },
    { name: 'UNICEF', role: 'Programme Partner' },
    { name: 'British Council', role: 'Programme Partner' },
    { name: 'VVOB', role: 'Programme Partner' },
    { name: 'Edukans International', role: 'Programme Partner' },
    { name: 'Brainwave Careers Uganda', role: 'Programme Partner' },
    { name: 'STiR Education', role: 'Programme Partner' },
    { name: 'PEAS Uganda', role: 'Programme Partner' },
    { name: 'Teach for Uganda', role: 'Programme Partner' },
    { name: 'Forum for Education NGOs in Uganda (FENU)', role: 'Programme Partner' },
];

const pillars = [
    {
        icon: '🎯',
        title: 'Recruit',
        description:
            "Identify and attract talented female students with the potential and commitment to become qualified STEM teachers in Uganda's secondary schools.",
    },
    {
        icon: '📚',
        title: 'Train',
        description:
            'Support scholars throughout their Bachelor of Science with Education (BScEd), covering tuition, accommodation, and providing tools like laptops to enable effective learning.',
    },
    {
        icon: '🌱',
        title: 'Lead',
        description:
            'Build leadership capacity and professional agency so that scholars become confident, capable educators who drive positive change in their schools and communities.',
    },
    {
        icon: '⭐',
        title: 'Motivate',
        description:
            'Create a sustained support system — mentorship, peer networks, and ongoing professional development — to keep scholars engaged, inspired, and committed to teaching.',
    },
];

export default function About() {
    return (
        <>
            <Head title="About the Programme" />

            <div className="relative min-h-screen overflow-hidden bg-gray-50 text-gray-900 selection:bg-[#035A7D] selection:text-white">
                {/* Background gradient blob */}
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

                <PublicHeader currentRoute="about" />

                <main className="relative z-10 mx-auto w-full max-w-7xl px-6 py-12 lg:px-8">

                    {/* ── Page heading ── */}
                    <motion.div {...fadeUp(0)}>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                            About the Programme
                        </p>
                        <h1 className="mt-3 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">
                            LIT-Uganda Female STEM<br className="hidden sm:block" /> Student Teachers' Scholarship
                        </h1>
                        <p className="mt-4 max-w-3xl text-lg text-gray-600">
                            A five-year Mastercard Foundation initiative transforming secondary education
                            in Uganda by investing in the next generation of female STEM educators.
                        </p>
                    </motion.div>

                    {/* ── What the programme is ── */}
                    <motion.section
                        {...fadeUp(0.1)}
                        className="mt-12 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm"
                        aria-labelledby="about-programme"
                    >
                        <h2 id="about-programme" className="text-2xl font-bold text-gray-900">
                            What Is the Programme?
                        </h2>
                        <div className="mt-4 space-y-4 text-gray-600 leading-relaxed">
                            <p>
                                The Leaders in Teaching (LiT) Uganda programme is a five-year initiative
                                funded by the Mastercard Foundation, designed to transform the quality
                                of secondary education in Uganda. At its heart is a commitment to equity:
                                ensuring that female students with the talent and drive to become science
                                teachers are not held back by financial barriers.
                            </p>
                            <p>
                                The scholarship arm of the programme provides <strong>1,000 scholarships</strong> to
                                female students pursuing a Bachelor of Science with Education (BScEd) at
                                selected universities and Uganda National Institute for Teacher Education
                                (UNITE) campuses across the country. Each scholarship covers tuition and
                                functional fees, accommodation, and a laptop — removing the practical
                                obstacles that prevent talented women from completing their education.
                            </p>
                            <p>
                                The programme is aligned with the Mastercard Foundation's <em>Young Africa Works</em> strategy,
                                which aims to enable 30 million young Africans to access dignified and
                                fulfilling work by 2030 — including a target of 4.3 million young people
                                in Uganda. By growing the pipeline of qualified female STEM teachers,
                                LiT-Uganda directly contributes to that goal while addressing the acute
                                shortage of science teachers in Uganda's secondary schools.
                            </p>
                        </div>
                    </motion.section>

                    {/* ── Four pillars ── */}
                    <motion.section
                        {...fadeUp(0.15)}
                        className="mt-8"
                        aria-labelledby="about-pillars"
                    >
                        <div className="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                            <h2 id="about-pillars" className="text-2xl font-bold text-gray-900">
                                How It Works: The Four Pillars
                            </h2>
                            <p className="mt-2 text-gray-600">
                                The programme is built on four interlocking pillars that guide every
                                intervention, from scholar selection through to long-term career support.
                            </p>
                            <div className="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                                {pillars.map((pillar, i) => (
                                    <motion.div
                                        key={pillar.title}
                                        initial={{ opacity: 0, y: 14 }}
                                        whileInView={{ opacity: 1, y: 0 }}
                                        viewport={{ once: true }}
                                        transition={{ duration: 0.4, delay: i * 0.08 }}
                                        className="rounded-xl border border-gray-100 bg-gray-50 p-6"
                                    >
                                        <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 text-2xl mb-4">
                                            {pillar.icon}
                                        </div>
                                        <h3 className="font-bold text-gray-900 text-base">{pillar.title}</h3>
                                        <p className="mt-2 text-sm text-gray-600 leading-relaxed">{pillar.description}</p>
                                    </motion.div>
                                ))}
                            </div>
                        </div>
                    </motion.section>

                    {/* ── Who delivers it ── */}
                    {/* <motion.section
                        {...fadeUp(0.2)}
                        className="mt-8 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm"
                        aria-labelledby="about-consortium"
                    >
                        <h2 id="about-consortium" className="text-2xl font-bold text-gray-900">
                            Who Delivers the Programme?
                        </h2>
                        <p className="mt-3 text-gray-600 leading-relaxed">
                            The programme is implemented by a consortium of ten organisations under the
                            strategic leadership and oversight of Uganda's <strong>Ministry of Education and Sports</strong>.
                            Each partner brings distinct expertise, ensuring the programme is grounded
                            in both global best practice and local knowledge.
                        </p>
                        <div className="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            {partners.map((partner) => (
                                <div
                                    key={partner.name}
                                    className="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 px-4 py-3"
                                >
                                    <span className="mt-0.5 shrink-0 text-[#035A7D] font-bold">✓</span>
                                    <div>
                                        <p className="text-sm font-semibold text-gray-800">{partner.name}</p>
                                        <p className="text-xs text-gray-500">{partner.role}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </motion.section> */}

                    {/* ── Who it's for ── */}
                    <motion.section
                        {...fadeUp(0.25)}
                        className="mt-8 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm"
                        aria-labelledby="about-who"
                    >
                        <h2 id="about-who" className="text-2xl font-bold text-gray-900">
                            Who is the Scholarship Program for?
                        </h2>
                        <p className="mt-3 text-gray-600 leading-relaxed">
                            The scholarship is for female Ugandan citizens — including those with refugee
                            status and persons with disabilities — who are enrolled in or seeking
                            admission to a BScEd programme at a LiT partner institution. The programme
                            places particular emphasis on reaching those most in need, with dedicated
                            slots reserved for marginalised groups.
                        </p>
                        <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            {[
                                { stat: '1,000', label: 'Scholarship places available' },
                                { stat: '100%', label: 'Young women aged 18–35' },
                                { stat: '7%', label: 'Refugees & Internally Displaced Persons' },
                                { stat: '5%', label: 'Persons with Disabilities' },
                            ].map((item) => (
                                <div
                                    key={item.label}
                                    className="rounded-xl bg-gradient-to-br from-[#035A7D]/5 to-blue-50 border border-blue-100 p-5 text-center"
                                >
                                    <p className="text-3xl font-bold text-[#035A7D]">{item.stat}</p>
                                    <p className="mt-1 text-sm text-gray-600">{item.label}</p>
                                </div>
                            ))}
                        </div>
                        <p className="mt-5 text-sm text-gray-500">
                            An additional 20% of places are reserved for female in-service science,
                            technology, and mathematics teachers who want to upgrade to a full BScEd degree.
                        </p>
                    </motion.section>

                    {/* ── CTA ── */}
                    <motion.section
                        {...fadeUp(0.3)}
                        className="mt-8 rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 to-[#035A7D]/5 p-8 text-center shadow-sm"
                        aria-labelledby="about-cta"
                    >
                        <h2 id="about-cta" className="text-2xl font-bold text-gray-900">Ready to Apply?</h2>
                        <p className="mt-2 text-gray-600 max-w-xl mx-auto">
                            If you meet the eligibility criteria and are committed to becoming a STEM
                            teacher in Uganda, we encourage you to start your application today.
                        </p>
                        <div className="mt-6 flex flex-wrap justify-center gap-4">
                            <Link
                                href={route('register')}
                                className="rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                            >
                                Apply Now
                            </Link>
                            <Link
                                href={route('scholarships')}
                                className="rounded-full bg-white px-6 py-3 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50"
                            >
                                View Scholarships
                            </Link>
                            <Link
                                href={route('contact')}
                                className="rounded-full bg-white px-6 py-3 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50"
                            >
                                Contact Us
                            </Link>
                        </div>
                    </motion.section>

                </main>

                <PublicFooter />
            </div>
        </>
    );
}
