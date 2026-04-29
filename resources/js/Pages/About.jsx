import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

export default function About() {
    return (
        <>
            <Head title="About Us" />

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

                <PublicHeader currentRoute="about" />

                <main className="relative z-10 mx-auto w-full max-w-7xl px-6 py-12 lg:px-8">
                    <motion.div
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45 }}
                    >
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                            About Us
                        </p>
                        <h1 className="mt-3 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">
                            Empowering Future Educators
                        </h1>
                        <p className="mt-4 text-lg text-gray-600">
                            The LGF Scholarship Program is dedicated to supporting aspiring teachers
                            who are committed to serving rural communities.
                        </p>
                    </motion.div>

                    <div className="mt-12 grid gap-8 lg:grid-cols-2">
                        <motion.section
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.1 }}
                            className="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm"
                        >
                            <h2 className="text-2xl font-bold text-gray-900">Our Mission</h2>
                            <p className="mt-4 text-gray-600">
                                We believe that quality education should be accessible to all,
                                regardless of geographic location. Our mission is to identify and
                                support talented individuals who demonstrate both academic excellence
                                and a genuine commitment to teaching in underserved rural areas.
                            </p>
                            <p className="mt-4 text-gray-600">
                                Through financial assistance and ongoing support, we aim to reduce
                                the educational gap between urban and rural communities by ensuring
                                that rural schools have access to well-trained, dedicated educators.
                            </p>
                        </motion.section>

                        <motion.section
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.2 }}
                            className="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm"
                        >
                            <h2 className="text-2xl font-bold text-gray-900">Our Vision</h2>
                            <p className="mt-4 text-gray-600">
                                We envision a future where every child in rural communities has
                                access to passionate, qualified teachers who inspire learning and
                                foster growth. By investing in future educators today, we're building
                                stronger communities for tomorrow.
                            </p>
                            <p className="mt-4 text-gray-600">
                                Our scholarship recipients become part of a network of educators
                                committed to making a lasting impact in rural education, creating
                                ripple effects that benefit entire communities for generations.
                            </p>
                        </motion.section>
                    </div>

                    <motion.section
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45, delay: 0.3 }}
                        className="mt-8 rounded-2xl border border-blue-100 bg-white p-8 shadow-sm"
                    >
                        <h2 className="text-2xl font-bold text-gray-900">What We Offer</h2>
                        <div className="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {[
                                {
                                    title: 'Financial Support',
                                    description:
                                        'Comprehensive scholarship coverage for tuition, books, and living expenses throughout your education journey.',
                                },
                                {
                                    title: 'Mentorship Program',
                                    description:
                                        'Connect with experienced educators who provide guidance, support, and professional development opportunities.',
                                },
                                {
                                    title: 'Community Network',
                                    description:
                                        'Join a vibrant community of scholars and alumni committed to rural education excellence.',
                                },
                                {
                                    title: 'Career Placement',
                                    description:
                                        'Assistance in finding teaching positions in rural schools that align with your goals and values.',
                                },
                                {
                                    title: 'Ongoing Support',
                                    description:
                                        'Continued professional development and resources even after graduation and placement.',
                                },
                                {
                                    title: 'Impact Tracking',
                                    description:
                                        'Monitor your progress and see the tangible impact you\'re making in rural communities.',
                                },
                            ].map((item, index) => (
                                    <div className="rounded-lg border border-gray-200 bg-gray-50 p-6">
                                    <h3 className="font-bold text-gray-900">{item.title}</h3>
                                    <p className="mt-2 text-sm text-gray-600">{item.description}</p>
                                </div>
                            ))}
                        </div>
                    </motion.section>

                    <motion.section
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45, delay: 0.4 }}
                        className="mt-8 rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 to-[#035A7D]/5 p-8 text-center shadow-sm"
                    >
                        <h2 className="text-2xl font-bold text-gray-900">Ready to Apply?</h2>
                        <p className="mt-2 text-gray-600">
                            Join our community of dedicated educators making a difference in rural
                            education.
                        </p>
                        <div className="mt-6 flex justify-center gap-4">
                            <Link
                                href={route('register')}
                                className="rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                            >
                                Apply Now
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
