import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

export default function Resources() {
    return (
        <>
            <Head title="Resources" />

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

                <main className="relative z-10 mx-auto w-full max-w-7xl px-6 py-12 lg:px-8">
                    <motion.div
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45 }}
                    >
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                            Resources
                        </p>
                        <h1 className="mt-3 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">
                            Tools & Guides for Success
                        </h1>
                        <p className="mt-4 text-lg text-gray-600">
                            Everything you need to prepare a strong application and succeed as a
                            scholar.
                        </p>
                    </motion.div>

                    <div className="mt-12 space-y-8">
                        <motion.section
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.1 }}
                        >
                            <h2 className="text-2xl font-bold text-gray-900">
                                Application Resources
                            </h2>
                            <div className="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                {[
                                    {
                                        title: 'Application Guide',
                                        description:
                                            'Step-by-step instructions for completing your scholarship application.',
                                        icon: '📋',
                                    },
                                    {
                                        title: 'Essay Writing Tips',
                                        description:
                                            'Learn how to craft a compelling essay that showcases your commitment.',
                                        icon: '✍️',
                                    },
                                    {
                                        title: 'Document Checklist',
                                        description:
                                            'Complete list of required documents and how to prepare them.',
                                        icon: '📄',
                                    },
                                    {
                                        title: 'Financial Aid Calculator',
                                        description:
                                            'Estimate your funding needs and scholarship eligibility.',
                                        icon: '💰',
                                    },
                                    {
                                        title: 'Sample Applications',
                                        description:
                                            'Review successful applications from previous scholarship recipients.',
                                        icon: '⭐',
                                    },
                                    {
                                        title: 'Video Tutorials',
                                        description:
                                            'Watch guided walkthroughs of the application process.',
                                        icon: '🎥',
                                    },
                                ].map((item, index) => (
                                    <div
                                        key={index}
                                        className="group cursor-pointer rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-blue-200 hover:shadow-md"
                                    >
                                        <div className="text-4xl">{item.icon}</div>
                                        <h3 className="mt-4 font-bold text-gray-900 group-hover:text-[#035A7D]">
                                            {item.title}
                                        </h3>
                                        <p className="mt-2 text-sm text-gray-600">
                                            {item.description}
                                        </p>
                                        <div className="mt-4 text-sm font-semibold text-[#035A7D]">
                                            Learn more →
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </motion.section>

                        <motion.section
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.2 }}
                        >
                            <h2 className="text-2xl font-bold text-gray-900">
                                Scholar Resources
                            </h2>
                            <div className="mt-6 grid gap-6 sm:grid-cols-2">
                                {[
                                    {
                                        title: 'Academic Progress Tracking',
                                        description:
                                            'Tools and templates to monitor your academic journey and maintain scholarship requirements.',
                                        icon: '📊',
                                    },
                                    {
                                        title: 'Teaching Resources',
                                        description:
                                            'Lesson plans, classroom management tips, and educational materials for rural teaching.',
                                        icon: '📚',
                                    },
                                    {
                                        title: 'Professional Development',
                                        description:
                                            'Workshops, webinars, and training opportunities to enhance your teaching skills.',
                                        icon: '🎓',
                                    },
                                    {
                                        title: 'Community Forum',
                                        description:
                                            'Connect with fellow scholars, share experiences, and get advice from alumni.',
                                        icon: '💬',
                                    },
                                ].map((item, index) => (
                                    <div
                                        key={index}
                                        className="group cursor-pointer rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-blue-200 hover:shadow-md"
                                    >
                                        <div className="text-4xl">{item.icon}</div>
                                        <h3 className="mt-4 font-bold text-gray-900 group-hover:text-[#035A7D]">
                                            {item.title}
                                        </h3>
                                        <p className="mt-2 text-sm text-gray-600">
                                            {item.description}
                                        </p>
                                        <div className="mt-4 text-sm font-semibold text-[#035A7D]">
                                            Access resource →
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </motion.section>

                        <motion.section
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.3 }}
                            className="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm"
                        >
                            <h2 className="text-2xl font-bold text-gray-900">
                                Important Documents
                            </h2>
                            <div className="mt-6 space-y-4">
                                {[
                                    {
                                        title: 'Scholarship Program Handbook',
                                        size: '2.4 MB',
                                        type: 'PDF',
                                    },
                                    {
                                        title: 'Rural Teaching Commitment Agreement',
                                        size: '156 KB',
                                        type: 'PDF',
                                    },
                                    {
                                        title: 'Financial Need Assessment Form',
                                        size: '89 KB',
                                        type: 'PDF',
                                    },
                                    {
                                        title: 'Academic Progress Report Template',
                                        size: '124 KB',
                                        type: 'DOCX',
                                    },
                                ].map((doc, index) => (
                                    <div
                                        key={index}
                                        className="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:bg-gray-100"
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-sm font-bold text-[#035A7D]">
                                                {doc.type}
                                            </div>
                                            <div>
                                                <div className="font-semibold text-gray-900">
                                                    {doc.title}
                                                </div>
                                                <div className="text-sm text-gray-600">
                                                    {doc.size}
                                                </div>
                                            </div>
                                        </div>
                                        <button className="rounded-full bg-[#035A7D] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#024a6b]">
                                            Download
                                        </button>
                                    </div>
                                ))}
                            </div>
                        </motion.section>

                        <motion.section
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.4 }}
                            className="rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 to-[#035A7D]/5 p-8 text-center shadow-sm"
                        >
                            <h2 className="text-2xl font-bold text-gray-900">Need More Help?</h2>
                            <p className="mt-2 text-gray-600">
                                Can't find what you're looking for? Our team is here to assist you.
                            </p>
                            <div className="mt-6 flex justify-center gap-4">
                                <Link
                                    href={route('contact')}
                                    className="rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                                >
                                    Contact Support
                                </Link>
                                <Link
                                    href={route('faq')}
                                    className="rounded-full bg-white px-6 py-3 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50"
                                >
                                    View FAQ
                                </Link>
                            </div>
                        </motion.section>
                    </div>
                </main>

                <PublicFooter />
            </div>
        </>
    );
}
