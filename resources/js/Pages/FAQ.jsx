import { Head, Link } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { useState } from 'react';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

const faqs = [
    {
        category: 'About the Programme',
        questions: [
            {
                question: 'What is the LIT-Uganda Female STEM Student Teachers\' Scholarship?',
                answer:
                    'The Leaders in Teaching (LiT) Uganda Programme is a five-year Mastercard Foundation initiative aimed at transforming secondary education in Uganda. As part of this, the programme is providing 1,000 scholarships to female students pursuing a Bachelor of Science with Education (BScEd) at selected universities and Uganda National Institute for Teacher Education (UNITE) campuses. The scholarship covers tuition and functional fees, accommodation, and a laptop.',
            },
            {
                question: 'What is the Leaders in Teaching Program about?',
                answer:
                    'Leaders in Teaching Uganda Program is a Mastercard Foundation flagship initiative that aims to elevate the quality of secondary school education in Uganda by supporting teachers, school leaders, and learning institutions.',
            },
            {
                question: 'What does the program focus on?',
                answer:
                    'Leaders in Teaching focuses on strengthening Uganda\'s secondary school education system with a goal to improve teacher quality, school leadership, and student learning outcomes especially for girls, youth with disabilities, and underserved schools.',
            },
            {
                question: 'How will the program be implemented?',
                answer:
                    'Leaders in Teaching is implemented through a 10-partner consortium, working closely with the Ministry of Education and Sports to ensure full integration into Uganda\'s national education system.',
            },
            {
                question: 'Who are the 10 Consortium Partners?',
                answerNode: (
                    <ul className="space-y-1.5">
                        {[
                            'Luigi Giussani Foundation (Consortium Lead)',
                            'UNICEF (Consortium Co-lead)',
                            'Forum for Education NGOs in Uganda (FENU)',
                            'Teach For Uganda (T4U)',
                            'British Council',
                            'Edukans International Uganda',
                            'Brainwave Careers Uganda',
                            'VVOB – education for development',
                            'Promoting Equality in African Schools (PEAS)',
                            'STiR Education',
                        ].map((p) => (
                            <li key={p} className="flex items-start gap-2 text-gray-600 text-sm">
                                <span className="mt-1 shrink-0 h-1.5 w-1.5 rounded-full bg-[#035A7D]" aria-hidden="true" />
                                {p}
                            </li>
                        ))}
                    </ul>
                ),
            },
            {
                question: 'What areas does the program focus on?',
                answer:
                    'To strengthen the quality of teaching and learning, the Leaders in Teaching focuses on four pillars: (1) Teacher Recruitment, (2) Teacher Training, (3) Education Leadership Management, and (4) Teacher Motivation.',
            },
            {
                question: 'Which partners support each program pillar?',
                answerNode: (
                    <div className="space-y-4 text-sm text-gray-600">
                        {[
                            { pillar: 'Recruit Pillar', partners: ['Forum for Education NGOs in Uganda (FENU) – Pillar Lead', 'Teach For Uganda'] },
                            { pillar: 'Train Pillar', partners: ['British Council – Pillar Lead', 'Edukans International Uganda', 'Brainwave Careers Uganda'] },
                            { pillar: 'Lead Pillar', partners: ['VVOB – education for development – Pillar Lead', 'Promoting Equality in African Schools (PEAS)'] },
                            { pillar: 'Motivate Pillar', partners: ['STiR Education'] },
                            { pillar: 'ICT Integration', partners: ['UNICEF – Leads on ICT integration in teaching and learning across the program'] },
                        ].map(({ pillar, partners }) => (
                            <div key={pillar}>
                                <p className="font-semibold text-gray-800 mb-1">{pillar}</p>
                                <ul className="space-y-1 pl-1">
                                    {partners.map((p) => (
                                        <li key={p} className="flex items-start gap-2">
                                            <span className="mt-1.5 shrink-0 h-1.5 w-1.5 rounded-full bg-[#035A7D]" aria-hidden="true" />
                                            {p}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </div>
                ),
            },
            {
                question: 'What is the duration of the program?',
                answer:
                    'Leaders in Teaching is a five-year program, running from June 2025 to July 2030.',
            },
            {
                question: 'Who implements the programme?',
                answer:
                    'The programme is implemented by a consortium of ten organisations under the strategic leadership and oversight of the Ministry of Education and Sports. The consortium comprises: Luigi Giussani Foundation (LGF), UNICEF, British Council, VVOB, Edukans International, Brainwave Careers Uganda, STiR Education, PEAS Uganda, Teach For Uganda, and the Forum for Education NGOs in Uganda (FENU).',
            },
            {
                question: 'What are the four pillars of the LiT-Uganda Programme?',
                answer:
                    'The programme is anchored on four pillars: Recruit — identifying and attracting talented female students to STEM teaching; Train — supporting scholars through their BScEd with financial and material resources; Lead — building professional leadership capacity in schools; and Motivate — sustaining commitment to the teaching profession through mentorship, networks, and professional development.',
            },
            {
                question: 'How does this scholarship relate to the Mastercard Foundation\'s Young Africa Works strategy?',
                answer:
                    'The Young Africa Works strategy aims to enable 30 million young Africans to access dignified and fulfilling work by 2030, including 4.3 million in Uganda. By training and placing qualified female STEM teachers, the LiT-Uganda programme directly contributes to this goal while tackling the shortage of science teachers in Uganda\'s secondary schools.',
            },
            {
                question: 'What systemic changes does the program aim to achieve?',
                answerNode: (
                    <div className="space-y-2 text-sm text-gray-600">
                        <p className="leading-relaxed">Leaders in Teaching seeks to transform secondary education at the individual, school, and system levels through:</p>
                        <ul className="space-y-2 mt-2">
                            {[
                                'Increased number of qualified and gender-inclusive teachers contributing to improved learning outcomes and equitable access to quality education.',
                                'Improved teacher competencies and practices for effective Competency-Based Education delivery in Ugandan secondary schools.',
                                'Improved utilisation of effective leadership practices by school leaders for a better teaching and learning environment.',
                                'Improved teacher motivation reflected in increased satisfaction, professional growth, and retention enabled by recognition, mentorship, and career development pathways.',
                            ].map((p) => (
                                <li key={p} className="flex items-start gap-2">
                                    <span className="mt-1.5 shrink-0 h-1.5 w-1.5 rounded-full bg-[#035A7D]" aria-hidden="true" />
                                    {p}
                                </li>
                            ))}
                        </ul>
                    </div>
                ),
            },
            {
                question: 'How will communities and districts be engaged?',
                answer:
                    'Implementation of Leaders in Teaching will be in collaboration with District Local Governments, teacher training institutions, and community structures to ensure local ownership, coordination, and oversight of program activities.',
            },
            {
                question: 'How will the Leaders in Teaching ensure sustainability after the program ends?',
                answerNode: (
                    <div className="space-y-2 text-sm text-gray-600">
                        <p className="leading-relaxed">Sustainability will be built through:</p>
                        <ul className="space-y-1.5 mt-2">
                            {[
                                'Strengthening national systems rather than creating parallel structures',
                                'Building capacity within the Ministry of Education and Sports and district education offices',
                                'Embedding training programs within national institutions',
                                'Supporting long-term policy reforms',
                            ].map((p) => (
                                <li key={p} className="flex items-start gap-2">
                                    <span className="mt-1.5 shrink-0 h-1.5 w-1.5 rounded-full bg-[#035A7D]" aria-hidden="true" />
                                    {p}
                                </li>
                            ))}
                        </ul>
                    </div>
                ),
            },
        ],
    },
    {
        category: 'Eligibility & Who Can Apply',
        questions: [
            {
                question: 'Who is eligible to apply?',
                answer:
                    'The scholarship is open to female Ugandan citizens, females with Refugee Status in Uganda, and female youth with disabilities in Uganda. All applicants must be aged 18–35 years, hold or be pursuing an admission offer for a BScEd at a LiT partner institution, demonstrate financial need, and show a genuine commitment to teaching in secondary schools — particularly in rural or underserved areas.',
            },
            {
                question: 'Are there reserved places for specific groups?',
                answer:
                    'Yes. The programme places strong emphasis on equity and inclusion. Dedicated slots are reserved for: 7% Refugees and Internally Displaced Persons (IDPs), 5% Persons with Disabilities, and 20% for female in-service science, technology, and mathematics teachers wishing to upgrade to a full BScEd degree.',
            },
            {
                question: 'Which schools and regions are covered by the Program?',
                answer:
                    'Leaders in Teaching will be implemented in 2,091 secondary schools across Uganda, including 1,000 public and 1,091 private schools.',
            },
            {
                question: 'How were the schools participating in the program selected?',
                answer:
                    'Selection focused on schools that have consistently performed poorly in Uganda Certificate of Education examinations over several years. Priority was also given to schools that host refugees, Special Needs Education schools, and those located in rural and peri-urban communities. In addition, the selection considered the type of ownership, with preference for government, NGO-supported schools not benefiting from a previous intervention, and community-based schools.',
            },
            {
                question: 'Who are the primary participants in the Program?',
                answerNode: (
                    <div className="space-y-2 text-sm text-gray-600">
                        <p className="leading-relaxed">Leaders in Teaching directly targets:</p>
                        <ul className="space-y-1.5 mt-2">
                            {[
                                '67,000 in-service teachers',
                                '7,500 pre-service teachers',
                                '300 tutors from 10 universities and five teacher training institutions',
                                '6,273 school leaders nationwide',
                            ].map((p) => (
                                <li key={p} className="flex items-start gap-2">
                                    <span className="mt-1.5 shrink-0 h-1.5 w-1.5 rounded-full bg-[#035A7D]" aria-hidden="true" />
                                    {p}
                                </li>
                            ))}
                        </ul>
                    </div>
                ),
            },
            {
                question: 'How were the universities on the program selected?',
                answer:
                    'Using data from the National Council for Higher Education, the team evaluated universities based on STEM enrollment, support for Special Needs Education, strength in education leadership, affordability, laboratory facilities, and ownership. The selection ensured regional balance across Central, Eastern, Northern, and Western Uganda, with extra attention to underserved regions like Eastern and Northern, and included both public and faith-based institutions.',
            },
            {
                question: 'Which universities are eligible?',
                answer:
                    'Applicants must be admitted to one of the following LiT partner universities: Makerere University, Kyambogo University, Busitema University, Islamic University in Uganda, Gulu University, Muni University, Mountains of the Moon University, Mbarara University of Science and Technology, Uganda Martyrs University, or Kabale University. UNITE campuses at Kabale, Kaliro, Mubende, Muni, and Unyama are also eligible.',
            },
            {
                question: 'What STEM subjects does the scholarship cover?',
                answer:
                    'The scholarship supports students studying a BScEd with a focus on STEM subjects including Biology, Chemistry, Physics, Mathematics, Agriculture, and Computer Studies.',
            },
            {
                question: 'Do I need to already be enrolled to apply?',
                answer:
                    'No. You may apply if you have received, or are in the process of obtaining, an admission offer for a BScEd programme at a LiT partner institution. You will be required to provide proof of admission or an acceptance letter as part of your application.',
            },
            {
                question: 'Is the scholarship open to non-Ugandan applicants?',
                answer:
                    'The scholarship is open to female Ugandan citizens and to females with official Refugee Status in Uganda. Applicants without either of these statuses are not eligible.',
            },
        ],
    },
    {
        category: 'Scholarship Benefits',
        questions: [
            {
                question: 'Will the program offer scholarships?',
                answer:
                    'Yes. Leaders in Teaching will provide 1,000 scholarships to academically talented, economically disadvantaged female student teachers — including 5% with disabilities and 7% refugees or marginalised women — to train as STEM and ICT teachers.',
            },
            {
                question: 'What does the scholarship cover?',
                answer:
                    'Each scholarship award covers three key areas: (1) Tuition and functional fees for the full duration of the BScEd programme, (2) Accommodation costs at or near the institution, and (3) A laptop to support digital learning and academic work throughout the programme.',
            },
            {
                question: 'Are the services offered by the Program free of charge?',
                answer:
                    'Yes. All services, support, and capacity-building opportunities provided under the Leaders in Teaching program are offered at no cost to participating schools, teachers, tutors, institutions, and student teachers.',
            },
            {
                question: 'How will the program ensure inclusion in education?',
                answerNode: (
                    <div className="space-y-2 text-sm text-gray-600">
                        <p className="leading-relaxed">Leaders in Teaching prioritizes inclusion by:</p>
                        <ul className="space-y-1.5 mt-2">
                            {[
                                'Providing scholarship opportunities for marginalised female students',
                                'Supporting teachers with gender-responsive pedagogy training',
                                'Ensuring participation of individuals with disabilities and refugees',
                                'Building safer and more inclusive learning environments',
                            ].map((p) => (
                                <li key={p} className="flex items-start gap-2">
                                    <span className="mt-1.5 shrink-0 h-1.5 w-1.5 rounded-full bg-[#035A7D]" aria-hidden="true" />
                                    {p}
                                </li>
                            ))}
                        </ul>
                    </div>
                ),
            },
            {
                question: 'How will the program support in-service teachers?',
                answerNode: (
                    <div className="space-y-2 text-sm text-gray-600">
                        <p className="leading-relaxed">Teachers will receive training on:</p>
                        <ul className="space-y-1.5 mt-2">
                            {[
                                'Competency-Based Curriculum',
                                'Gender-responsive pedagogy',
                                'Career Guidance and Counseling',
                                'Integrating ICT in teaching and learning',
                            ].map((p) => (
                                <li key={p} className="flex items-start gap-2">
                                    <span className="mt-1.5 shrink-0 h-1.5 w-1.5 rounded-full bg-[#035A7D]" aria-hidden="true" />
                                    {p}
                                </li>
                            ))}
                        </ul>
                    </div>
                ),
            },
            {
                question: 'How will the program support STEM education?',
                answerNode: (
                    <div className="space-y-2 text-sm text-gray-600">
                        <p className="leading-relaxed">Leaders in Teaching will integrate and support STEM education by:</p>
                        <ul className="space-y-1.5 mt-2">
                            {[
                                'Strengthening the skills of in-service STEM teachers',
                                'Increasing the number of qualified STEM educators through scholarship opportunities to pre-service teachers',
                                'Enhancing access to ICT for teaching and learning in secondary schools',
                            ].map((p) => (
                                <li key={p} className="flex items-start gap-2">
                                    <span className="mt-1.5 shrink-0 h-1.5 w-1.5 rounded-full bg-[#035A7D]" aria-hidden="true" />
                                    {p}
                                </li>
                            ))}
                        </ul>
                    </div>
                ),
            },
            {
                question: 'How will the program support school leaders?',
                answer:
                    'Leaders in Teaching will co-design and deliver a blended, accredited school leader certification program for four cohorts, equipping leaders with effective management skills, Competency-Based Curriculum aligned instructional leadership practices, and tools to use gender-disaggregated data for school improvement.',
            },
            {
                question: 'How long does the scholarship last?',
                answer:
                    'The scholarship covers the full duration of the Bachelor of Science with Education (BScEd) programme, provided scholars maintain satisfactory academic progress and comply with programme requirements including the submission of periodic progress reports.',
            },
            {
                question: 'Is there a cash stipend provided?',
                answer:
                    'The scholarship covers tuition, accommodation, and a laptop. Specific details about any additional allowances or stipends are communicated to successful applicants during the onboarding process.',
            },
            {
                question: 'Is the scholarship renewable each year?',
                answer:
                    'Yes. The scholarship is renewed annually for the duration of the BScEd programme, subject to the scholar maintaining satisfactory academic progress and meeting reporting obligations. Scholars are required to submit academic progress reports each semester through their scholar portal.',
            },
        ],
    },
    {
        category: 'Application Process',
        questions: [
            {
                question: 'How do I apply?',
                answer:
                    'Applications are submitted online through this platform. Create an account, complete the multi-step application form covering your personal information, academic background, financial situation, and commitment to teaching, then upload all required supporting documents before submitting.',
            },
            {
                question: 'How many learners will benefit from the program?',
                answer:
                    'Leaders in Teaching is expected to indirectly reach at least 627,300 learners, based on an estimated 300 students per participating school.',
            },
            {
                question: 'What is the deadline for the current call?',
                answer:
                    'The deadline for the 2026/2027 academic year call is 15 July 2026. All completed applications and supporting documents must be submitted before this date. Late submissions will not be considered.',
            },
            {
                question: 'What documents will I need to submit?',
                answer:
                    'Required documents typically include: a national identity card or refugee documentation, proof of admission or acceptance letter from a LiT partner institution, academic transcripts or certificates (Uganda Certificate of Education / Uganda Advanced Certificate of Education), evidence of financial need, and any documentation relevant to disability or IDP status where applicable.',
            },
            {
                question: 'Can I save my application and return to it later?',
                answer:
                    'Yes. Once you create an account on this platform, your application is automatically saved as you progress through each section. You can log out and return at any time to continue where you left off, as long as you submit before the deadline.',
            },
            {
                question: 'Can I edit my application after submitting it?',
                answer:
                    'Once submitted, applications cannot be edited through the portal. If you need to correct critical information immediately after submission, contact the support team as soon as possible through the Contact page, and your request will be assessed on a case-by-case basis.',
            },
            {
                question: 'How long does the review process take?',
                answer:
                    'After the application deadline, the selection committee reviews all submitted applications. You will receive email notifications at each stage of the process and can track the status of your application through your dashboard. The full review timeline is communicated at the start of each call.',
            },
            {
                question: 'How are applications scored and selected?',
                answer:
                    'Applications are assessed based on eligibility, academic merit, demonstrated financial need, commitment to teaching — particularly in rural or underserved secondary schools — and alignment with the programme\'s equity goals. The committee prioritises applicants from marginalised groups including refugees, IDPs, and persons with disabilities.',
            },
        ],
    },
    {
        category: 'After Acceptance',
        questions: [
            {
                question: 'What support do scholars receive beyond the financial award?',
                answer:
                    'Scholars become part of the broader LiT-Uganda community and can access mentorship from experienced educators, professional development workshops, peer networks with fellow scholars and alumni, and career guidance to support their journey into teaching.',
            },
            {
                question: 'How do I submit academic progress reports?',
                answer:
                    'Log into your scholar dashboard and navigate to the Academic Progress section. Reports are submitted at the end of each semester and include your grades, course schedule, and a brief reflection on your academic journey. Keeping your progress reports up to date is a condition for continued scholarship renewal.',
            },
            {
                question: 'Is there a teaching commitment after graduation?',
                answer:
                    'Applicants must demonstrate a commitment to entering and remaining in the teaching profession in secondary schools, particularly in rural or underserved areas, for at least 2 years after graduation. This commitment is assessed as part of the selection process.',
            },
            {
                question: 'What happens if I withdraw from my programme?',
                answer:
                    'If you withdraw from your BScEd programme or no longer meet the scholarship conditions, the award will be reviewed and may be suspended or terminated. If you are facing challenges that could affect your studies, contact your scholarship coordinator early so appropriate support can be arranged.',
            },
        ],
    },
    {
        category: 'Technical Support',
        questions: [
            {
                question: 'I forgot my password. How do I reset it?',
                answer:
                    'On the login page, click "Forgot password?" and enter your registered email address. You will receive an email with a secure link to reset your password. If you do not receive the email within a few minutes, check your spam or junk folder.',
            },
            {
                question: 'The application form is not loading properly. What should I do?',
                answer:
                    'Try clearing your browser cache and cookies, or switch to a different browser. We recommend using an up-to-date version of Chrome, Firefox, or Safari. If the issue persists, contact us via the Contact page with a description of the problem and the device and browser you are using.',
            },
            {
                question: 'How do I upload supporting documents?',
                answer:
                    'Each section of the application form that requires a document has a dedicated upload button. Click the button, select your file (PDF or common image formats are accepted), and it will be automatically attached to your application. Ensure file sizes are within the stated limits.',
            },
            {
                question: 'I am having trouble creating an account. What should I do?',
                answer:
                    'Ensure you are using a valid email address that you have access to, as you will need to verify it. If you continue to have trouble, contact the support team through the Contact page or call (+256) 764 078712 for assistance.',
            },
        ],
    },
];

export default function FAQ() {
    const [openIndex, setOpenIndex] = useState(null);

    function toggle(categoryIndex, questionIndex) {
        const key = `${categoryIndex}-${questionIndex}`;
        setOpenIndex(prev => (prev === key ? null : key));
    }

    return (
        <>
            <Head title="FAQ — LiT-Uganda Scholarship" />

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

                <PublicHeader currentRoute="faq" />

                <main className="relative z-10 mx-auto w-full max-w-4xl px-6 py-12 lg:px-8">

                    {/* Heading */}
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
                            Everything you need to know about the LiT-Uganda Female STEM Student
                            Teachers' Scholarship — eligibility, benefits, applying, and more.
                        </p>
                    </motion.div>

                    {/* FAQ accordion */}
                    <div className="mt-12 space-y-10">
                        {faqs.map((category, ci) => (
                            <motion.section
                                key={ci}
                                initial={{ opacity: 0, y: 16 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.45, delay: ci * 0.07 }}
                                aria-labelledby={`cat-${ci}`}
                            >
                                <h2
                                    id={`cat-${ci}`}
                                    className="mb-4 text-xl font-bold text-gray-900 flex items-center gap-2"
                                >
                                    <span
                                        className="inline-block h-1 w-6 rounded-full bg-[#035A7D]"
                                        aria-hidden="true"
                                    />
                                    {category.category}
                                </h2>

                                <div className="space-y-2">
                                    {category.questions.map((item, qi) => {
                                        const key = `${ci}-${qi}`;
                                        const isOpen = openIndex === key;

                                        return (
                                            <div
                                                key={qi}
                                                className={`overflow-hidden rounded-xl border bg-white shadow-sm transition-colors ${
                                                    isOpen
                                                        ? 'border-[#035A7D]/30'
                                                        : 'border-gray-200'
                                                }`}
                                            >
                                                <button
                                                    onClick={() => toggle(ci, qi)}
                                                    aria-expanded={isOpen}
                                                    className="flex w-full items-center justify-between gap-4 px-6 py-5 text-left transition hover:bg-gray-50"
                                                >
                                                    <span className="font-semibold text-gray-900 leading-snug">
                                                        {item.question}
                                                    </span>
                                                    <span
                                                        className={`shrink-0 flex h-7 w-7 items-center justify-center rounded-full border transition-all duration-300 ${
                                                            isOpen
                                                                ? 'border-[#035A7D] bg-[#035A7D] text-white rotate-45'
                                                                : 'border-gray-300 text-gray-400'
                                                        }`}
                                                        aria-hidden="true"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 16 16"
                                                            fill="currentColor"
                                                            className="h-3.5 w-3.5"
                                                        >
                                                            <path d="M8 2a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 8 2Z" />
                                                        </svg>
                                                    </span>
                                                </button>

                                                <AnimatePresence initial={false}>
                                                    {isOpen && (
                                                        <motion.div
                                                            key="answer"
                                                            initial={{ height: 0, opacity: 0 }}
                                                            animate={{ height: 'auto', opacity: 1 }}
                                                            exit={{ height: 0, opacity: 0 }}
                                                            transition={{ duration: 0.25, ease: 'easeInOut' }}
                                                        >
                                                            <div className="border-t border-gray-100 bg-gray-50 px-6 py-5">
                                                                {item.answerNode ? (
                                                                    item.answerNode
                                                                ) : (
                                                                    <p className="text-gray-600 leading-relaxed">
                                                                        {item.answer}
                                                                    </p>
                                                                )}
                                                            </div>
                                                        </motion.div>
                                                    )}
                                                </AnimatePresence>
                                            </div>
                                        );
                                    })}
                                </div>
                            </motion.section>
                        ))}
                    </div>

                    {/* Still have questions CTA */}
                    <motion.section
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45, delay: 0.5 }}
                        className="mt-14 rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 to-[#035A7D]/5 p-8 text-center shadow-sm"
                        aria-labelledby="faq-cta"
                    >
                        <h2 id="faq-cta" className="text-2xl font-bold text-gray-900">
                            Still Have Questions?
                        </h2>
                        <p className="mt-2 text-gray-600 max-w-lg mx-auto">
                            Our team is happy to help. Reach out via the contact form or call us
                            directly at{' '}
                            <a
                                href="tel:+256764078712"
                                className="font-semibold text-[#035A7D] hover:underline"
                            >
                                (+256) 764 078712
                            </a>
                            .
                        </p>
                        <div className="mt-6 flex flex-wrap justify-center gap-4">
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
