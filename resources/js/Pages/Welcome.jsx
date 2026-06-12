import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

export default function Welcome({ auth }) {
    return (
        <>
            <Head title="Welcome - LGF Scholarship" />
            <div className="min-h-screen bg-gray-50 text-gray-900 font-sans selection:bg-[#035A7D] selection:text-white">
                <PublicHeader />

                <main>
                    {/* Hero section */}
                    <div className="relative isolate pt-14 pb-20">
                        <div
                            className="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
                            aria-hidden="true"
                        >
                            <div
                                className="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#4A90E2] to-[#035A7D] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                                style={{
                                    clipPath:
                                        'polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)',
                                }}
                            />
                        </div>

                        <div className="py-24 sm:py-32 lg:pb-16 text-center px-6">
                            <motion.div 
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.6 }}
                                className="mx-auto max-w-2xl"
                            >
                                <h1 className="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                                    LIT-Uganda Program Female STEM Student Teachers’ Scholarship 
                                    {/* <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#035A7D] to-[#4A90E2]">Tomorrow</span> */}
                                </h1>
                                <p className="mt-6 text-lg leading-8 text-gray-600">
                                    The Female STEM Student Teachers’ Scholarship is committed to supporting exceptional students demonstrating financial need, academic merit, and a strong commitment to their communities.
                                </p>
                                <div className="mt-10 flex items-center justify-center gap-x-6">
                                    <a href="#benefits" className="text-sm font-semibold leading-6 text-gray-900 hover:text-[#035A7D] transition">
                                        Learn more <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </motion.div>
                        </div>
                    </div>

                    {/* Overview section */}
                    <div id="overview" className="bg-white py-18 sm:py-20">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="mx-auto max-w-6xl lg:text-center">
                                <p className="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                                    Program Overview
                                </p>
                                <p className="mt-6 text-lg leading-8 text-gray-600">
                                    The Leaders in Teaching Uganda program is a five-year Mastercard Foundation initiative aimed at transforming secondary education in Uganda. The program is implemented through a consortium of 10 partners: Luigi Giussani Foundation (LGF), UNICEF, British Council, VVOB, Edukans International, Brainwave Careers Uganda, STiR Education, PEAS Uganda, Teach for Uganda, and Forum for Education NGOs in Uganda (FENU). The initiative is implemented under the strategic leadership and oversight of the Ministry of Education and Sports, and is aligned with Mastercard Foundation’s Young Africa Works strategy, which seeks to enable 30 million young people across Africa to access dignified and fulfilling work by 2030, including a target of 4.3 million young people in Uganda.
                                </p>
                                <p className="mt-6 text-lg leading-8 text-gray-600">
                                    To contribute to this vision, the program focuses on improving the quality of teaching and learning in secondary schools through inclusive, gender-responsive, and innovative approaches to education. Anchored on four core pillars; Recruit, Train, Lead, and Motivate, the program is implementing a range of interventions designed to increase both the quality and quantity of teachers in Uganda's secondary education system. One of these interventions is the provision of 1,000 scholarships for female students pursuing a Bachelor of Science with Education (BScEd) at selected universities and UNITE campuses across the country. 
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    {/* Objectives Section */}
                    <div id="objectives" className="bg-gray-50 py-18 sm:py-20">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="mx-auto max-w-6xl">
                                <div className="lg:text-center mb-12">
                                    <h2 className="text-base font-semibold leading-7 text-[#035A7D] uppercase tracking-widest">Purpose</h2>
                                    <p className="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                                        Objective of the Scholarships Program
                                    </p>
                                    <p className="mt-6 text-lg leading-8 text-gray-600">
                                        The scholarship program for 1,000 females pursuing a Bachelor of Science with Education (BScEd) under the Leaders in Teaching (LiT) Uganda Program is designed to:
                                    </p>
                                </div>
                                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                                    {[
                                        {
                                            icon: '👩‍🔬',
                                            title: 'Increase Female Participation in STEM Teaching',
                                            description: 'Grow the number of women entering STEM teaching roles in secondary education across Uganda.',
                                        },
                                        {
                                            icon: '⚖️',
                                            title: 'Promote Equity and Inclusion in Education',
                                            description: 'Break down barriers and create pathways that ensure every woman has equal access to quality education.',
                                        },
                                        {
                                            icon: '🏫',
                                            title: 'Expand the Supply of Female Qualified Science Teachers',
                                            description: 'Address the critical shortage of qualified female science teachers in Uganda\'s secondary schools.',
                                        },
                                        {
                                            icon: '🌟',
                                            title: 'Empower Female Role Models in Education',
                                            description: 'Inspire the next generation of girls by cultivating visible, confident female leaders in STEM education.',
                                        },
                                    ].map((objective, index) => (
                                        <motion.div
                                            key={objective.title}
                                            initial={{ opacity: 0, y: 20 }}
                                            whileInView={{ opacity: 1, y: 0 }}
                                            viewport={{ once: true }}
                                            transition={{ duration: 0.5, delay: index * 0.1 }}
                                            className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition"
                                        >
                                            <div className="h-12 w-12 flex items-center justify-center rounded-lg bg-blue-50 text-3xl mb-4">
                                                {objective.icon}
                                            </div>
                                            <h3 className="text-base font-semibold text-gray-900 mb-2">{objective.title}</h3>
                                            <p className="text-sm text-gray-600 leading-6">{objective.description}</p>
                                        </motion.div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Stats Section */}
                    <div className="bg-gradient-to-r from-[#035A7D] to-[#024a6b] py-24 sm:py-32">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="mb-12 text-center">
                                <h2 className="text-3xl font-bold text-white">Our Vision & Goals</h2>
                                <p className="mt-2 text-blue-100">Projected impact as we launch this transformative program</p>
                            </div>
                            <div className="grid grid-cols-1 gap-y-16 text-center sm:grid-cols-2 lg:grid-cols-4">
                                {[
                                    { stat: '1000', label: 'Scholars' },
                                    { stat: '100%', label: 'Young women (18-35 years)' },
                                    { stat: '7%', label: 'Refugees and Internally Displaced Persons' },
                                    { stat: '5%', label: 'Persons with Disabilities' }
                                ].map((item, i) => (
                                    <motion.div 
                                        key={item.label}
                                        initial={{ opacity: 0, y: 10 }}
                                        whileInView={{ opacity: 1, y: 0 }}
                                        viewport={{ once: true }}
                                        transition={{ delay: i * 0.1 }}
                                        className="text-white"
                                    >
                                        <div className="text-5xl font-bold">{item.stat}</div>
                                        <div className="mt-2 text-blue-100">{item.label}</div>
                                    </motion.div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Features section */}
                    <div id="benefits" className="bg-white py-18 sm:py-20">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="mx-auto max-w-2xl lg:text-center">
                                <h2 className="text-base font-semibold leading-7 text-[#035A7D] uppercase tracking-widest">What's Covered</h2>
                                <p className="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                                    Scholarship Award Coverage
                                </p>
                                <p className="mt-6 text-lg leading-8 text-gray-600">
                                    The scholarship award will cover tuition and functional fees, accommodation costs, and a laptop to support each program participant's academic studies.
                                </p>
                            </div>
                            <div className="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                                <dl className="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                                    {[
                                        {
                                            name: 'Tuition & Functional Fees',
                                            description: 'Full coverage of tuition and functional fees for the duration of the Bachelor of Science with Education (BScEd) program.',
                                            icon: '🎓'
                                        },
                                        {
                                            name: 'Accommodation Costs',
                                            description: 'Accommodation expenses are fully covered, giving you a safe and stable living environment to focus on your studies.',
                                            icon: '🏠'
                                        },
                                        {
                                            name: 'Laptop',
                                            description: 'Each scholar receives a laptop to support their academic work and ensure access to digital learning resources throughout the program.',
                                            icon: '💻'
                                        },
                                    ].map((feature, index) => (
                                        <motion.div 
                                            key={feature.name} 
                                            initial={{ opacity: 0, y: 20 }}
                                            whileInView={{ opacity: 1, y: 0 }}
                                            viewport={{ once: true }}
                                            transition={{ duration: 0.5, delay: index * 0.2 }}
                                            className="flex flex-col"
                                        >
                                            <dt className="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900">
                                                <div className="h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100 text-2xl shadow-sm">
                                                    {feature.icon}
                                                </div>
                                                {feature.name}
                                            </dt>
                                            <dd className="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600">
                                                <p className="flex-auto">{feature.description}</p>
                                            </dd>
                                        </motion.div>
                                    ))}
                                </dl>
                            </div>
                        </div>
                    </div>

                    {/* Eligibility Section */}
                    <div className="bg-gray-50 py-24 sm:py-26">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="mx-auto max-w-2xl lg:text-center mb-16">
                                <h2 className="text-base font-semibold leading-7 text-[#035A7D] uppercase tracking-widest">Eligibility</h2>
                                <p className="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                                    Who Can Apply?
                                </p>
                            </div>

                            <div className="max-w-5xl mx-auto space-y-8">

                                {/* Primary Support Group */}
                                <motion.div
                                    initial={{ opacity: 0, y: 20 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true }}
                                    transition={{ duration: 0.5 }}
                                    className="bg-gray-50 p-8 rounded-xl border border-gray-200"
                                >
                                    <h3 className="text-lg font-semibold text-gray-900 mb-3">1. Primary Support Group</h3>
                                    <p className="text-gray-600 mb-6">
                                        Applicants must be female resident Ugandan citizens, females with Refugee Status, or female youth with disabilities in Uganda. The program places strong emphasis on equity and inclusion, reserving slots for marginalised groups with a focus on:
                                    </p>
                                    <ul className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        {[
                                            { stat: '100%', label: 'Young women aged 18–35 years' },
                                            { stat: '7%', label: 'Refugees and Internally Displaced Persons' },
                                            { stat: '5%', label: 'Persons with Disabilities' },
                                            { stat: '20%', label: 'Young female in-service science, technology and mathematics teachers intending to upgrade to a BScEd Degree' },
                                        ].map((item) => (
                                            <li key={item.label} className="flex items-start gap-3 bg-white rounded-lg border border-gray-100 px-4 py-3">
                                                <span className="text-[#035A7D] font-bold text-sm mt-0.5 shrink-0">{item.stat}</span>
                                                <span className="text-gray-600 text-sm">{item.label}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </motion.div>

                                {/* Academic Eligibility */}
                                <motion.div
                                    initial={{ opacity: 0, y: 20 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true }}
                                    transition={{ duration: 0.5, delay: 0.1 }}
                                    className="bg-gray-50 p-8 rounded-xl border border-gray-200"
                                >
                                    <h3 className="text-lg font-semibold text-gray-900 mb-3">2. Academic Eligibility</h3>
                                    <p className="text-gray-600 mb-4">
                                        Candidates must hold an admission offer for a Bachelor of Science with Education (BScEd) from one of the following LiT partner universities:
                                    </p>
                                    <ul className="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-6">
                                        {[
                                            'Makerere University',
                                            'Kyambogo University',
                                            'Busitema University',
                                            'Islamic University in Uganda',
                                            'Gulu University',
                                            'Muni University',
                                            'Mountains of the Moon University',
                                            'Mbarara University of Science & Technology',
                                            'Uganda Martyrs University',
                                            'Kabale University',
                                        ].map((uni) => (
                                            <li key={uni} className="flex items-center gap-2">
                                                <span className="text-[#035A7D] font-bold">✓</span>
                                                <span className="text-gray-600 text-sm">{uni}</span>
                                            </li>
                                        ))}
                                    </ul>
                                    <p className="text-gray-600 mb-3 text-sm">
                                        Or from one of the following Uganda National Institute for Teacher Education (UNITE) campuses:
                                    </p>
                                    <div className="flex flex-wrap gap-2">
                                        {['Kabale', 'Kaliro', 'Mubende', 'Muni', 'Unyama'].map((campus) => (
                                            <span key={campus} className="bg-blue-50 text-[#035A7D] text-sm font-medium px-3 py-1 rounded-full border border-blue-100">
                                                {campus}
                                            </span>
                                        ))}
                                    </div>
                                </motion.div>

                                {/* Commitment to Education */}
                                <motion.div
                                    initial={{ opacity: 0, y: 20 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true }}
                                    transition={{ duration: 0.5, delay: 0.2 }}
                                    className="bg-gray-50 p-8 rounded-xl border border-gray-200"
                                >
                                    <h3 className="text-lg font-semibold text-gray-900 mb-3">3. Commitment to Education</h3>
                                    <div className="flex items-start gap-3">
                                        <span className="text-[#035A7D] font-bold mt-0.5">✓</span>
                                        <p className="text-gray-600">
                                            Applicants must demonstrate a strong, passionate commitment to entering and remaining in the teaching profession, particularly in secondary schools, and must be willing to serve in rural or underserved areas for at least <strong>2 years</strong>.
                                        </p>
                                    </div>
                                </motion.div>

                                {/* Financial Need */}
                                <motion.div
                                    initial={{ opacity: 0, y: 20 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true }}
                                    transition={{ duration: 0.5, delay: 0.3 }}
                                    className="bg-gray-50 p-8 rounded-xl border border-gray-200"
                                >
                                    <h3 className="text-lg font-semibold text-gray-900 mb-3">4. Financial Need</h3>
                                    <div className="flex items-start gap-3">
                                        <span className="text-[#035A7D] font-bold mt-0.5">✓</span>
                                        <p className="text-gray-600">
                                            Applicants must demonstrate genuine financial need.
                                        </p>
                                    </div>
                                </motion.div>

                            </div>
                        </div>
                    </div>

                    {/* Application Timeline */}
                    {/* <div className="bg-gray-50 py-24 sm:py-32">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="mx-auto max-w-2xl lg:text-center mb-16">
                                <h2 className="text-base font-semibold leading-7 text-[#035A7D] uppercase tracking-widest">Process</h2>
                                <p className="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                                    Our Application Journey
                                </p>
                            </div>
                            <div className="mx-auto max-w-4xl">
                                {[
                                    { step: 1, title: 'Create Account', description: 'Register with your email and personal information.' },
                                    { step: 2, title: 'Fill Application', description: 'Complete our comprehensive multi-step form with personal, financial, and essay sections.' },
                                    { step: 3, title: 'Auto-Scoring', description: 'Your application is automatically scored based on our transparent algorithm.' },
                                    { step: 4, title: 'Committee Review', description: 'Our selection committee reviews and evaluates all qualified applications.' },
                                    { step: 5, title: 'Receive Decision', description: 'Get notified via email with the committee\'s decision.' },
                                    { step: 6, title: 'Onboarding', description: 'Complete final setup and begin your journey as an LGF Scholar.' }
                                ].map((item, idx) => (
                                    <motion.div 
                                        key={item.step}
                                        initial={{ opacity: 0 }}
                                        whileInView={{ opacity: 1 }}
                                        viewport={{ once: true }}
                                        transition={{ delay: idx * 0.1 }}
                                        className="relative pb-12 last:pb-0"
                                    >
                                        {idx < 5 && (
                                            <div className="absolute left-5 top-12 h-8 w-0.5 bg-blue-200"></div>
                                        )}
                                        <div className="flex gap-6">
                                            <div className="relative">
                                                <div className="h-10 w-10 rounded-full bg-[#035A7D] text-white flex items-center justify-center font-bold text-sm">
                                                    {item.step}
                                                </div>
                                            </div>
                                            <div className="pt-1">
                                                <h3 className="text-lg font-semibold text-gray-900">{item.title}</h3>
                                                <p className="mt-2 text-gray-600">{item.description}</p>
                                            </div>
                                        </div>
                                    </motion.div>
                                ))}
                            </div>
                        </div>
                    </div> */}

                    {/* Testimonials Section */}
                    {/* <div className="bg-white py-24 sm:py-32">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="mx-auto max-w-2xl lg:text-center mb-16">
                                <h2 className="text-base font-semibold leading-7 text-[#035A7D] uppercase tracking-widest">Testimonials</h2>
                                <p className="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                                    Stories from Our Scholars
                                </p>
                            </div>
                            <div className="grid grid-cols-1 gap-8 md:grid-cols-3">
                                {[
                                    { name: 'Sarah M.', field: 'Engineering', quote: 'This scholarship didn\'t just pay for my tuition—it gave me hope and a clear path to my dreams.' },
                                    { name: 'James K.', field: 'Medicine', quote: 'The mentorship and support network made all the difference in my academic journey.' },
                                    { name: 'Amara O.', field: 'Business', quote: 'I went from worrying about bills to focusing on my passion and making an impact.' }
                                ].map((testimonial, idx) => (
                                    <motion.div 
                                        key={testimonial.name}
                                        initial={{ opacity: 0, y: 20 }}
                                        whileInView={{ opacity: 1, y: 0 }}
                                        viewport={{ once: true }}
                                        transition={{ delay: idx * 0.15 }}
                                        className="bg-gray-50 p-8 rounded-lg border border-gray-200 hover:shadow-lg transition"
                                    >
                                        <div className="flex gap-1 mb-4">
                                            {[...Array(5)].map((_, i) => (
                                                <span key={i} className="text-yellow-400">★</span>
                                            ))}
                                        </div>
                                        <p className="text-gray-600 italic mb-6">"{testimonial.quote}"</p>
                                        <div className="border-t border-gray-200 pt-4">
                                            <p className="font-semibold text-gray-900">{testimonial.name}</p>
                                            <p className="text-sm text-[#035A7D]">{testimonial.field} Scholar</p>
                                        </div>
                                    </motion.div>
                                ))}
                            </div>
                        </div>
                    </div> */}

                    {/* Final CTA Section */}
                    <div className="relative bg-gradient-to-r from-[#035A7D] via-[#024a6b] to-[#4A90E2] py-24 sm:py-32 overflow-hidden">
                        <div className="absolute inset-0 opacity-10" style={{
                            backgroundImage: 'radial-gradient(circle at 1px 1px, white 1px, transparent 1px)',
                            backgroundSize: '40px 40px'
                        }}></div>
                        <div className="relative mx-auto max-w-3xl text-center px-6">
                            <motion.div 
                                initial={{ opacity: 0, y: 20 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.6 }}
                            >
                                <h2 className="text-4xl font-bold tracking-tight text-white sm:text-5xl">
                                    Ready to Transform Your Future?
                                </h2>
                                <p className="mt-6 text-lg leading-8 text-blue-50">
                                    Join thousands of scholars who have changed their lives through the Luigi Giussani Foundation Scholarship. Take the first step today.
                                </p>
                                <div className="mt-10 flex items-center justify-center gap-x-6">
                                    {!auth.user && (
                                        <Link
                                            href={route('register')}
                                            className="rounded-full bg-white px-8 py-3 text-lg font-semibold text-[#035A7D] shadow-lg hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition"
                                        >
                                            Start Your Application
                                        </Link>
                                    )}
                                    <a href="#benefits" className="text-lg font-semibold leading-6 text-white hover:text-blue-100 transition">
                                        Learn More <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </motion.div>
                        </div>
                    </div>
                </main>
                
                <PublicFooter />
            </div>
        </>
    );
}
