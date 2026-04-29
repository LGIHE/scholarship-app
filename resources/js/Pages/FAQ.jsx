import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useState } from 'react';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

export default function FAQ() {
    const [openIndex, setOpenIndex] = useState(null);

    const faqs = [
        {
            category: 'Application Process',
            questions: [
                {
                    question: 'Who is eligible to apply for the LGF Scholarship?',
                    answer:
                        'The scholarship is open to students pursuing education degrees who demonstrate academic excellence, financial need, and a genuine commitment to teaching in rural communities for at least 3 years after graduation.',
                },
                {
                    question: 'When is the application deadline?',
                    answer:
                        'Applications are accepted on a rolling basis throughout the year. However, priority consideration is given to applications submitted before March 31st for the upcoming academic year.',
                },
                {
                    question: 'What documents do I need to submit?',
                    answer:
                        'Required documents include: academic transcripts, proof of enrollment or acceptance letter, financial need documentation, two letters of recommendation, a personal essay, and a signed rural teaching commitment agreement.',
                },
                {
                    question: 'Can I save my application and complete it later?',
                    answer:
                        'Yes! Our platform allows you to save your progress at any time. Simply create an account, start your application, and you can return to complete it whenever you\'re ready.',
                },
                {
                    question: 'How long does the review process take?',
                    answer:
                        'The committee typically reviews applications within 4-6 weeks of submission. You\'ll receive email notifications about your application status and can track progress through your dashboard.',
                },
            ],
        },
        {
            category: 'Scholarship Details',
            questions: [
                {
                    question: 'How much is the scholarship worth?',
                    answer:
                        'Scholarship amounts vary based on individual financial need and academic standing, ranging from $5,000 to $20,000 per academic year. The scholarship can cover tuition, books, and living expenses.',
                },
                {
                    question: 'Is the scholarship renewable?',
                    answer:
                        'Yes, the scholarship is renewable annually for up to 4 years, provided you maintain satisfactory academic progress (minimum 3.0 GPA), submit progress reports each semester, and remain committed to the rural teaching requirement.',
                },
                {
                    question: 'What is the rural teaching commitment?',
                    answer:
                        'Recipients agree to teach in a designated rural school for a minimum of 3 years after graduation. We provide placement assistance and ongoing support throughout your teaching commitment.',
                },
                {
                    question: 'What happens if I cannot fulfill the teaching commitment?',
                    answer:
                        'If you\'re unable to fulfill the commitment due to unforeseen circumstances, you may need to repay a portion of the scholarship. However, we work with scholars on a case-by-case basis and offer flexibility for legitimate hardships.',
                },
            ],
        },
        {
            category: 'Technical Support',
            questions: [
                {
                    question: 'I forgot my password. How can I reset it?',
                    answer:
                        'Click the "Forgot password?" link on the login page. Enter your email address, and we\'ll send you instructions to reset your password.',
                },
                {
                    question: 'The application form is not loading properly. What should I do?',
                    answer:
                        'Try clearing your browser cache and cookies, or use a different browser. We recommend using the latest versions of Chrome, Firefox, or Safari. If issues persist, contact our technical support team.',
                },
                {
                    question: 'Can I edit my application after submission?',
                    answer:
                        'Once submitted, applications cannot be edited. However, you can contact us immediately if you need to update critical information, and we\'ll work with you on a case-by-case basis.',
                },
                {
                    question: 'How do I upload documents?',
                    answer:
                        'In the application form, you\'ll find upload buttons for each required document. Click the button, select your file (PDF or image format), and it will be automatically attached to your application.',
                },
            ],
        },
        {
            category: 'After Acceptance',
            questions: [
                {
                    question: 'What support do scholars receive?',
                    answer:
                        'Scholars receive financial support, mentorship from experienced educators, access to professional development workshops, teaching resources, and career placement assistance in rural schools.',
                },
                {
                    question: 'How do I submit academic progress reports?',
                    answer:
                        'Log into your scholar dashboard and navigate to the Academic Progress section. You\'ll submit reports at the end of each semester, including your grades, course schedule, and a brief reflection on your progress.',
                },
                {
                    question: 'Can I connect with other scholars?',
                    answer:
                        'Absolutely! We have a vibrant scholar community with regular meetups, an online forum, and mentorship programs where you can connect with current scholars and alumni.',
                },
                {
                    question: 'What if I need additional financial assistance?',
                    answer:
                        'If you experience unexpected financial hardship during your studies, you can apply for supplemental assistance. Contact your scholarship coordinator to discuss your situation and available options.',
                },
            ],
        },
    ];

    const toggleQuestion = (categoryIndex, questionIndex) => {
        const index = `${categoryIndex}-${questionIndex}`;
        setOpenIndex(openIndex === index ? null : index);
    };

    return (
        <>
            <Head title="FAQ" />

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

                <PublicHeader currentRoute="faq" />

                <main className="relative z-10 mx-auto w-full max-w-4xl px-6 py-12 lg:px-8">
                    <motion.div
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45 }}
                    >
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                            Frequently Asked Questions
                        </p>
                        <h1 className="mt-3 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">
                            Got Questions? We've Got Answers
                        </h1>
                        <p className="mt-4 text-lg text-gray-600">
                            Find answers to common questions about the LGF Scholarship Program.
                        </p>
                    </motion.div>

                    <div className="mt-12 space-y-8">
                        {faqs.map((category, categoryIndex) => (
                            <motion.section
                                key={categoryIndex}
                                initial={{ opacity: 0, y: 16 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.45, delay: categoryIndex * 0.1 }}
                            >
                                <h2 className="text-2xl font-bold text-gray-900">
                                    {category.category}
                                </h2>
                                <div className="mt-4 space-y-3">
                                    {category.questions.map((item, questionIndex) => {
                                        const index = `${categoryIndex}-${questionIndex}`;
                                        const isOpen = openIndex === index;

                                        return (
                                            <div
                                                key={questionIndex}
                                                className="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
                                            >
                                                <button
                                                    onClick={() =>
                                                        toggleQuestion(categoryIndex, questionIndex)
                                                    }
                                                    className="flex w-full items-center justify-between p-6 text-left transition hover:bg-gray-50"
                                                >
                                                    <span className="font-semibold text-gray-900">
                                                        {item.question}
                                                    </span>
                                                    <span
                                                        className={`ml-4 text-2xl text-[#035A7D] transition-transform ${
                                                            isOpen ? 'rotate-45' : ''
                                                        }`}
                                                    >
                                                        +
                                                    </span>
                                                </button>
                                                {isOpen && (
                                                    <div className="border-t border-gray-200 bg-gray-50 p-6">
                                                        <p className="text-gray-600">{item.answer}</p>
                                                    </div>
                                                )}
                                            </div>
                                        );
                                    })}
                                </div>
                            </motion.section>
                        ))}
                    </div>

                    <motion.section
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45, delay: 0.5 }}
                        className="mt-12 rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 to-[#035A7D]/5 p-8 text-center shadow-sm"
                    >
                        <h2 className="text-2xl font-bold text-gray-900">
                            Still Have Questions?
                        </h2>
                        <p className="mt-2 text-gray-600">
                            Can't find the answer you're looking for? Our support team is ready to
                            help.
                        </p>
                        <div className="mt-6 flex justify-center gap-4">
                            <Link
                                href={route('contact')}
                                className="rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                            >
                                Contact Us
                            </Link>
                            <Link
                                href={route('register')}
                                className="rounded-full bg-white px-6 py-3 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50"
                            >
                                Apply Now
                            </Link>
                        </div>
                    </motion.section>
                </main>

                <PublicFooter />
            </div>
        </>
    );
}
