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

                        <div className="py-24 sm:py-32 lg:pb-40 text-center px-6">
                            <motion.div 
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.6 }}
                                className="mx-auto max-w-2xl"
                            >
                                <h1 className="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
                                    Empowering the Leaders of <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#035A7D] to-[#4A90E2]">Tomorrow</span>
                                </h1>
                                <p className="mt-6 text-lg leading-8 text-gray-600">
                                    The Luigi Giussani Foundation Scholarship is committed to supporting exceptional students demonstrating financial need, academic merit, and a strong commitment to their communities.
                                </p>
                                <div className="mt-10 flex items-center justify-center gap-x-6">
                                    {auth.user ? (
                                        <Link
                                            href={route('portal')}
                                            className="rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#024a6b] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#035A7D] transition"
                                        >
                                            Go to Dashboard
                                        </Link>
                                    ) : (
                                        <Link
                                            href={route('register')}
                                            className="rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white shadow-sm shadow-[#035A7D]/30 hover:shadow-[#024a6b]/40 hover:bg-[#024a6b] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#035A7D] transition"
                                        >
                                            Start Your Application
                                        </Link>
                                    )}
                                    <a href="#benefits" className="text-sm font-semibold leading-6 text-gray-900 hover:text-[#035A7D] transition">
                                        Learn more <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </motion.div>
                        </div>
                    </div>

                    {/* Features section */}
                    <div id="benefits" className="bg-white py-24 sm:py-32">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="mx-auto max-w-2xl lg:text-center">
                                <h2 className="text-base font-semibold leading-7 text-[#035A7D] uppercase tracking-widest">Why Apply?</h2>
                                <p className="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                                    Comprehensive Support for Your Education
                                </p>
                                <p className="mt-6 text-lg leading-8 text-gray-600">
                                    Our scholarship provides more than just financial assistance. We offer a holistic program designed to help you succeed academically and professionally.
                                </p>
                            </div>
                            <div className="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                                <dl className="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                                    {[
                                        {
                                            name: 'Full Tuition Coverage',
                                            description: 'We cover 100% of your tuition fees for the duration of your chosen degree program, ensuring you graduate debt-free.',
                                            icon: '🎓'
                                        },
                                        {
                                            name: 'Living Stipend',
                                            description: 'Receive a monthly allowance to cover accommodation, books, and other living expenses so you can focus on your studies.',
                                            icon: '💰'
                                        },
                                        {
                                            name: 'Mentorship & Networking',
                                            description: 'Connect with industry professionals and alumni who will guide you through your academic journey and early career.',
                                            icon: '🤝'
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
                    {/* Stats Section */}
                    <div className="bg-gradient-to-r from-[#035A7D] to-[#024a6b] py-24 sm:py-32">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="mb-12 text-center">
                                <h2 className="text-3xl font-bold text-white">Our Vision & Goals</h2>
                                <p className="mt-2 text-blue-100">Projected impact as we launch this transformative program</p>
                            </div>
                            <div className="grid grid-cols-1 gap-y-16 text-center sm:grid-cols-2 lg:grid-cols-4">
                                {[
                                    { stat: '100+', label: 'Scholars (Year 1 Goal)' },
                                    { stat: '$500K', label: 'Funding Target' },
                                    { stat: '50+', label: 'Rural Schools to Serve' },
                                    { stat: '5+', label: 'Partner Universities' }
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

                    {/* Eligibility Section */}
                    <div className="bg-white py-24 sm:py-32">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <div className="mx-auto max-w-2xl lg:text-center mb-16">
                                <h2 className="text-base font-semibold leading-7 text-[#035A7D] uppercase tracking-widest">Eligibility</h2>
                                <p className="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                                    Who Can Apply?
                                </p>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                                {[
                                    { title: 'Academic Requirements', items: ['Minimum GPA of 2.5', 'Active enrollment in an accredited institution', 'Pursuing undergraduate or graduate degree'] },
                                    { title: 'Financial Need', items: ['Annual household income below $85,000', 'Demonstrated financial hardship', 'Unable to fully fund education without assistance'] },
                                    { title: 'Character & Commitment', items: ['Strong moral character', 'Community engagement history', 'Clear career goals aligned with scholarship mission'] },
                                    { title: 'Documentation', items: ['Valid identification', 'Academic transcripts', 'Financial documents & community references'] }
                                ].map((section, idx) => (
                                    <motion.div 
                                        key={section.title}
                                        initial={{ opacity: 0, x: idx % 2 === 0 ? -20 : 20 }}
                                        whileInView={{ opacity: 1, x: 0 }}
                                        viewport={{ once: true }}
                                        transition={{ duration: 0.5, delay: idx * 0.1 }}
                                        className="bg-gray-50 p-8 rounded-lg border border-gray-200"
                                    >
                                        <h3 className="text-lg font-semibold text-gray-900 mb-4">{section.title}</h3>
                                        <ul className="space-y-3">
                                            {section.items.map((item) => (
                                                <li key={item} className="flex items-start">
                                                    <span className="text-[#035A7D] mr-3 font-bold">✓</span>
                                                    <span className="text-gray-600">{item}</span>
                                                </li>
                                            ))}
                                        </ul>
                                    </motion.div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Application Timeline */}
                    <div className="bg-gray-50 py-24 sm:py-32">
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
                    </div>

                    {/* Testimonials Section */}
                    <div className="bg-white py-24 sm:py-32">
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
                    </div>

                    {/* FAQ Section */}
                    <div className="bg-gray-50 py-24 sm:py-32">
                        <div className="mx-auto max-w-3xl px-6 lg:px-8">
                            <div className="mx-auto max-w-2xl lg:text-center mb-16">
                                <h2 className="text-base font-semibold leading-7 text-[#035A7D] uppercase tracking-widest">FAQ</h2>
                                <p className="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                                    Frequently Asked Questions
                                </p>
                            </div>
                            <div className="space-y-6">
                                {[
                                    { q: 'Can I apply if I\'m already a full-time student?', a: 'Yes! We accept applications from current students, new enrollees, and those planning to enroll.' },
                                    { q: 'Is there a deadline to apply?', a: 'Applications are accepted on a rolling basis until our annual deadline. Early applications are encouraged.' },
                                    { q: 'Will the scholarship cover only tuition?', a: 'Our scholarship includes full tuition coverage plus a monthly living stipend to support your overall education.' },
                                    { q: 'Can I apply if my GPA is below 2.5?', a: 'We require a minimum 2.5 GPA, but we also consider upward trends and personal circumstances.' },
                                    { q: 'How long does the review process take?', a: 'Typically 4-6 weeks from submission to final decision. You\'ll receive email updates throughout.' },
                                    { q: 'What if I\'m denied? Can I reapply?', a: 'Yes, you can reapply in the next cycle. We encourage you to reach out for feedback to strengthen your application.' }
                                ].map((item, idx) => (
                                    <motion.div 
                                        key={item.q}
                                        initial={{ opacity: 0 }}
                                        whileInView={{ opacity: 1 }}
                                        viewport={{ once: true }}
                                        transition={{ delay: idx * 0.05 }}
                                        className="bg-white p-6 rounded-lg border border-gray-200"
                                    >
                                        <h3 className="font-semibold text-gray-900 mb-2">{item.q}</h3>
                                        <p className="text-gray-600">{item.a}</p>
                                    </motion.div>
                                ))}
                            </div>
                        </div>
                    </div>

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
