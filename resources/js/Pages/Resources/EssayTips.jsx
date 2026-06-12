import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

const fadeUp = (delay = 0) => ({
    initial: { opacity: 0, y: 18 },
    animate: { opacity: 1, y: 0 },
    transition: { duration: 0.45, delay },
});

const sections = [
    {
        title: 'What the Selection Committee Is Looking For',
        icon: '🔍',
        content: `The personal statement is your opportunity to speak directly to the selection committee. They are not looking for a perfect writer — they are looking for a genuine, motivated person who understands why teaching matters and is committed to making a difference in Uganda's secondary schools.

Strong statements share four qualities: they are personal (your own voice, your own story), specific (concrete examples and details rather than generalisations), honest (authentic motivation, not what you think they want to hear), and focused (connected to teaching, STEM, and the communities you want to serve).`,
        list: null,
    },
    {
        title: 'Structure Your Statement Clearly',
        icon: '🏗️',
        content: 'A well-structured statement makes it easy for reviewers to follow your thinking. Consider organising yours around three core parts:',
        list: [
            { label: 'Opening — Who are you?', detail: 'Begin with something personal and specific. Avoid generic openers like "I have always wanted to be a teacher." Instead, start with a moment, an experience, or a person that sparked your interest in education or STEM.' },
            { label: 'Middle — Why teaching? Why STEM? Why now?', detail: 'Explain your motivation in depth. Describe the experiences — at school, in your community, or in your own life — that have shaped your desire to become a STEM teacher. Be honest about challenges you have faced and how they have prepared you.' },
            { label: 'Closing — What will you contribute?', detail: 'End by looking forward. What kind of teacher do you want to be? What impact do you hope to have on your students and your community? How does this scholarship connect to your long-term goals?' },
        ],
    },
    {
        title: 'Show, Don\'t Tell',
        icon: '🎯',
        content: 'This is the most important writing principle to apply. Instead of stating qualities, demonstrate them through your story.',
        list: [
            { label: 'Weak:', detail: '"I am passionate about science and committed to teaching."' },
            { label: 'Strong:', detail: '"In my final year of secondary school, I started an informal study group for classmates who were struggling with mathematics. Watching them gain confidence — and seeing two of them go on to study science subjects — made me realise that teaching is what I am meant to do."' },
        ],
        note: 'Specific, real examples are far more persuasive than general claims about your character.',
    },
    {
        title: 'Address the Programme\'s Values',
        icon: '🌍',
        content: 'The LiT-Uganda scholarship is specifically focused on equity, inclusion, and service in underserved communities. Your statement should reflect an understanding of these values. Consider addressing:',
        list: [
            { label: 'Your connection to rural or underserved communities', detail: 'Have you grown up in or spent time in such a community? What did you observe about access to education there?' },
            { label: 'Why female STEM teachers matter', detail: 'What does it mean to you to be a female role model in science education? How might your presence in a classroom change things for your future students?' },
            { label: 'Your commitment to staying in teaching', detail: 'The programme asks scholars to commit to at least 2 years of teaching after graduation. Explain why this is something you genuinely want, not just a condition you are accepting.' },
        ],
    },
    {
        title: 'Practical Writing Advice',
        icon: '✏️',
        content: null,
        list: [
            { label: 'Write a draft first, then revise', detail: 'Do not try to write a perfect statement in one sitting. Write freely in your first draft, then read it back and improve it.' },
            { label: 'Read it aloud', detail: 'Reading your statement aloud helps you catch awkward sentences and identify parts that do not flow naturally.' },
            { label: 'Ask someone to review it', detail: 'A teacher, mentor, or trusted friend can spot gaps or unclear passages that you might miss after reading it many times yourself.' },
            { label: 'Be concise', detail: 'Every sentence should earn its place. Cut anything that does not directly support your story or argument.' },
            { label: 'Avoid copying examples from the internet', detail: 'The committee reads many applications. A statement that sounds borrowed will stand out for the wrong reasons.' },
            { label: 'Proofread carefully', detail: 'Spelling and grammar errors are distracting. Take time to check your final version thoroughly.' },
        ],
    },
    {
        title: 'Things to Avoid',
        icon: '⚠️',
        content: null,
        list: [
            { label: 'Vague generalisations', detail: '"Education is very important for development." This tells the reviewer nothing specific about you.' },
            { label: 'Excessive flattery of the programme', detail: 'Spending too much of your statement praising the scholarship comes across as insincere. Focus on yourself.' },
            { label: 'Listing achievements without reflection', detail: 'A list of grades or awards is not a statement. Explain what experiences shaped you, not just what you have done.' },
            { label: 'Overusing buzzwords', detail: 'Words like "passionate," "dedicated," and "hardworking" appear in almost every statement. Replace them with evidence.' },
            { label: 'Writing what you think they want to hear', detail: 'Authentic statements are more compelling than strategic ones. Be honest about who you are and what drives you.' },
        ],
    },
];

const prompts = [
    'Describe a moment when you saw the impact — positive or negative — that a teacher had on a student. What did it teach you?',
    'What is one challenge you have faced in accessing or continuing your education? How did you overcome it?',
    'Why is it important to you specifically, as a woman, to become a STEM teacher in Uganda?',
    'Describe a community you have been part of. What educational challenges does it face, and what role could a good teacher play?',
    'What does "commitment to teaching" mean to you beyond the 2-year requirement?',
];

export default function EssayTips() {
    return (
        <>
            <Head title="Essay Writing Tips — LiT-Uganda Scholarship" />

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
                        <span className="font-medium text-gray-900">Essay Writing Tips</span>
                    </nav>
                </div>

                <main className="relative z-10 mx-auto w-full max-w-4xl px-6 py-10 lg:px-8">

                    {/* Heading */}
                    <motion.div {...fadeUp(0)}>
                        <div className="flex items-center gap-3">
                            <span className="text-4xl" aria-hidden="true">✍️</span>
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">Application Resources</p>
                                <h1 className="mt-1 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">Essay Writing Tips</h1>
                            </div>
                        </div>
                        <p className="mt-5 text-lg text-gray-600 leading-relaxed">
                            Your personal statement is one of the most important parts of your
                            application. This guide will help you write something honest, specific,
                            and compelling that reflects who you really are.
                        </p>
                    </motion.div>

                    {/* Sections */}
                    <div className="mt-12 space-y-6">
                        {sections.map((section, i) => (
                            <motion.section
                                key={i}
                                initial={{ opacity: 0, y: 16 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.4, delay: i * 0.05 }}
                                className="rounded-2xl border border-gray-200 bg-white p-7 shadow-sm"
                                aria-labelledby={`section-${i}`}
                            >
                                <h2 id={`section-${i}`} className="flex items-center gap-3 text-xl font-bold text-gray-900">
                                    <span className="text-2xl" aria-hidden="true">{section.icon}</span>
                                    {section.title}
                                </h2>

                                {section.content && (
                                    <div className="mt-3 space-y-3">
                                        {section.content.split('\n\n').map((para, pi) => (
                                            <p key={pi} className="text-gray-600 leading-relaxed">{para}</p>
                                        ))}
                                    </div>
                                )}

                                {section.list && (
                                    <ul className="mt-4 space-y-3">
                                        {section.list.map((item, li) => (
                                            <li key={li} className="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                                <p className="font-semibold text-gray-800 text-sm">{item.label}</p>
                                                <p className="mt-1 text-sm text-gray-600 leading-relaxed">{item.detail}</p>
                                            </li>
                                        ))}
                                    </ul>
                                )}

                                {section.note && (
                                    <div className="mt-4 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                                        {section.note}
                                    </div>
                                )}
                            </motion.section>
                        ))}
                    </div>

                    {/* Writing prompts */}
                    <motion.section
                        {...fadeUp(0.2)}
                        className="mt-8 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm"
                        aria-labelledby="prompts-heading"
                    >
                        <h2 id="prompts-heading" className="text-xl font-bold text-gray-900">
                            Starter Prompts to Get You Writing
                        </h2>
                        <p className="mt-2 text-sm text-gray-600">
                            If you are unsure where to begin, try responding to one of these prompts as a freewriting exercise. You do not have to use them verbatim — they are just to help you get started.
                        </p>
                        <ul className="mt-5 space-y-3">
                            {prompts.map((prompt, i) => (
                                <li key={i} className="flex items-start gap-3">
                                    <span className="shrink-0 flex h-6 w-6 items-center justify-center rounded-full bg-[#035A7D]/10 text-xs font-bold text-[#035A7D]" aria-hidden="true">
                                        {i + 1}
                                    </span>
                                    <p className="text-gray-700 text-sm leading-relaxed">{prompt}</p>
                                </li>
                            ))}
                        </ul>
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
