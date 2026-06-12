import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

const sections = [
    {
        id: 'acceptance',
        title: '1. Acceptance of Terms',
        content: [
            {
                subtitle: null,
                text: 'By accessing or using the LGF Scholarship Platform ("Platform") operated by the Luigi Giussani Foundation ("Foundation", "we", "us", or "our"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree to these Terms, please do not use the Platform. These Terms apply to all visitors, applicants, scholarship recipients ("Scholars"), and any other users of the Platform.',
            },
        ],
    },
    {
        id: 'eligibility',
        title: '2. Eligibility',
        content: [
            {
                subtitle: null,
                text: 'To use the Platform and apply for the LGF Scholarship, you must:',
            },
            {
                subtitle: 'Age Requirement',
                text: 'Be at least 18 years of age at the time of application, or have verifiable parental or guardian consent if under 18.',
            },
            {
                subtitle: 'Programme Criteria',
                text: 'Meet the eligibility requirements set out in the current LGF Scholarship Programme guidelines, including enrolment (or intent to enrol) in an approved teacher training programme serving rural communities in Uganda.',
            },
            {
                subtitle: 'Accurate Information',
                text: 'Provide truthful, accurate, and complete information during registration and throughout the application process. Any misrepresentation or falsification of information may result in immediate disqualification or revocation of an awarded scholarship.',
            },
        ],
    },
    {
        id: 'accounts',
        title: '3. User Accounts',
        content: [
            {
                subtitle: '3.1 Registration',
                text: 'To access the applicant or scholar portal, you must create an account by providing a valid email address and creating a secure password. You are responsible for maintaining the confidentiality of your login credentials.',
            },
            {
                subtitle: '3.2 Account Security',
                text: 'You must notify us immediately at info@lgfug.org if you become aware of any unauthorised use of your account or any breach of security. The Foundation is not liable for any loss arising from your failure to safeguard your credentials.',
            },
            {
                subtitle: '3.3 One Account Per Applicant',
                text: 'Each individual may maintain only one account. Creating multiple accounts to circumvent application restrictions or limits is prohibited and may result in permanent disqualification.',
            },
        ],
    },
    {
        id: 'application-process',
        title: '4. Application Process',
        content: [
            {
                subtitle: '4.1 Submission',
                text: 'Applications must be submitted through the Platform before the advertised deadline. The Foundation reserves the right to reject late submissions at its sole discretion.',
            },
            {
                subtitle: '4.2 Accuracy of Information',
                text: 'All information provided in your application must be accurate and complete. You must promptly inform us of any changes to your circumstances that may affect your eligibility or the accuracy of your application.',
            },
            {
                subtitle: '4.3 Supporting Documents',
                text: 'You are responsible for submitting all required supporting documents in the formats specified. The Foundation may request additional documents or clarification at any stage of the evaluation process.',
            },
            {
                subtitle: '4.4 No Guarantee of Award',
                text: "Submission of a complete application does not guarantee the award of a scholarship. Decisions are made at the Foundation's sole discretion based on the selection criteria for the relevant programme cycle.",
            },
        ],
    },
    {
        id: 'scholarship-obligations',
        title: '5. Scholar Obligations',
        content: [
            {
                subtitle: null,
                text: 'If you are awarded a scholarship, you agree to the following ongoing obligations:',
            },
            {
                subtitle: 'Service Commitment',
                text: 'Upon completion of your studies, you commit to teaching in a rural school for the period specified in your scholarship award letter. Failure to fulfil this commitment may require repayment of scholarship funds as outlined in your award agreement.',
            },
            {
                subtitle: 'Academic Standards',
                text: 'You must maintain satisfactory academic performance as defined by the Foundation and your institution. You must submit academic progress reports through the Platform as required.',
            },
            {
                subtitle: 'Communication',
                text: 'You must keep your contact information current on the Platform and respond promptly to Foundation communications. Failure to do so may jeopardise your scholarship standing.',
            },
            {
                subtitle: 'Conduct',
                text: 'You must conduct yourself in a manner consistent with the values of the Foundation and comply with all applicable laws, institutional codes of conduct, and the terms of your scholarship award.',
            },
        ],
    },
    {
        id: 'acceptable-use',
        title: '6. Acceptable Use',
        content: [
            {
                subtitle: null,
                text: 'You agree not to use the Platform to:',
            },
            {
                subtitle: 'Prohibited Activities',
                text: '• Submit false, misleading, or fraudulent information\n• Impersonate another person or entity\n• Attempt to gain unauthorised access to other user accounts or Platform systems\n• Upload malicious code, viruses, or any software designed to disrupt or damage the Platform\n• Harass, threaten, or harm other users\n• Violate any applicable local, national, or international law or regulation\n• Use automated tools (bots, scrapers) to interact with the Platform without prior written consent',
            },
            {
                subtitle: null,
                text: 'The Foundation reserves the right to suspend or terminate your account immediately for any violation of these acceptable use provisions.',
            },
        ],
    },
    {
        id: 'intellectual-property',
        title: '7. Intellectual Property',
        content: [
            {
                subtitle: null,
                text: 'All content on the Platform — including text, graphics, logos, icons, and software — is the property of the Luigi Giussani Foundation or its licensors and is protected by applicable copyright and intellectual property laws. You may not reproduce, distribute, or create derivative works from Platform content without prior written permission from the Foundation.',
            },
            {
                subtitle: 'Your Content',
                text: 'By submitting application materials, essays, or other content to the Platform, you grant the Foundation a non-exclusive, royalty-free licence to use, store, and process that content solely for the purposes of evaluating your application and administering the scholarship programme. You retain ownership of your own content.',
            },
        ],
    },
    {
        id: 'disclaimers',
        title: '8. Disclaimers and Limitation of Liability',
        content: [
            {
                subtitle: 'Platform Availability',
                text: 'The Platform is provided "as is" and "as available." The Foundation does not guarantee uninterrupted or error-free access to the Platform and accepts no liability for downtime, data loss, or technical failures outside our reasonable control.',
            },
            {
                subtitle: 'Limitation of Liability',
                text: 'To the maximum extent permitted by applicable law, the Foundation shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of, or inability to use, the Platform or the scholarship programme.',
            },
        ],
    },
    {
        id: 'termination',
        title: '9. Termination',
        content: [
            {
                subtitle: null,
                text: 'The Foundation may suspend or terminate your access to the Platform at any time, with or without notice, for conduct that we believe violates these Terms, is harmful to other users, the Foundation, or third parties, or for any other reason at our sole discretion. You may also close your account at any time by contacting us at info@lgfug.org, subject to any ongoing scholarship obligations.',
            },
        ],
    },
    {
        id: 'governing-law',
        title: '10. Governing Law',
        content: [
            {
                subtitle: null,
                text: 'These Terms shall be governed by and construed in accordance with the laws of the Republic of Uganda. Any disputes arising under or in connection with these Terms shall be subject to the exclusive jurisdiction of the courts of Uganda.',
            },
        ],
    },
    {
        id: 'changes',
        title: '11. Changes to These Terms',
        content: [
            {
                subtitle: null,
                text: 'We reserve the right to modify these Terms at any time. We will notify registered users of material changes by email or through a notice on the Platform. Your continued use of the Platform following the effective date of revised Terms constitutes your acceptance of the changes. We encourage you to review these Terms periodically.',
            },
        ],
    },
    {
        id: 'contact',
        title: '12. Contact',
        content: [
            {
                subtitle: null,
                text: 'For questions or concerns regarding these Terms, please contact us:',
            },
            {
                subtitle: 'Luigi Giussani Foundation',
                text: 'Email: info@lgfug.org\nPhone: +256 (704) 567-890\nWebsite: lgfug.org',
            },
        ],
    },
];

export default function TermsOfService() {
    return (
        <>
            <Head title="Terms of Service" />

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

                <PublicHeader />

                <main className="relative z-10 mx-auto w-full max-w-7xl px-6 py-12 lg:px-8">
                    {/* Header */}
                    <motion.div
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45 }}
                    >
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                            Legal
                        </p>
                        <h1 className="mt-3 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">
                            Terms of Service
                        </h1>
                        <p className="mt-4 text-lg text-gray-600">
                            Please read these Terms of Service carefully before using the LGF
                            Scholarship Platform. They govern your access to and use of our services.
                        </p>
                        <p className="mt-2 text-sm text-gray-500">
                            Effective date: <span className="font-medium">January 1, 2025</span>
                        </p>
                    </motion.div>

                    <div className="mt-12 lg:grid lg:grid-cols-[260px_1fr] lg:gap-12">
                        {/* Table of Contents — sticky sidebar on large screens */}
                        <motion.aside
                            initial={{ opacity: 0, x: -12 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ duration: 0.45, delay: 0.1 }}
                            className="mb-8 lg:mb-0"
                        >
                            <div className="sticky top-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                <h2 className="text-sm font-semibold uppercase tracking-wider text-gray-500">
                                    Contents
                                </h2>
                                <nav className="mt-4 space-y-1">
                                    {sections.map((s) => (
                                        <a
                                            key={s.id}
                                            href={`#${s.id}`}
                                            className="block rounded-lg px-3 py-2 text-sm text-gray-600 transition hover:bg-gray-50 hover:text-[#035A7D]"
                                        >
                                            {s.title}
                                        </a>
                                    ))}
                                </nav>
                                <div className="mt-6 border-t border-gray-100 pt-4">
                                    <Link
                                        href={route('privacy')}
                                        className="block text-sm text-[#035A7D] hover:underline"
                                    >
                                        View Privacy Policy →
                                    </Link>
                                </div>
                            </div>
                        </motion.aside>

                        {/* Main content */}
                        <motion.div
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.15 }}
                            className="space-y-10"
                        >
                            {sections.map((section) => (
                                <section
                                    key={section.id}
                                    id={section.id}
                                    className="scroll-mt-8 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm"
                                >
                                    <h2 className="text-xl font-bold text-gray-900">
                                        {section.title}
                                    </h2>
                                    <div className="mt-4 space-y-4">
                                        {section.content.map((block, bIdx) => (
                                            <div key={bIdx}>
                                                {block.subtitle && (
                                                    <h3 className="font-semibold text-gray-800">
                                                        {block.subtitle}
                                                    </h3>
                                                )}
                                                <p
                                                    className={`text-gray-600 ${block.subtitle ? 'mt-1' : ''}`}
                                                    style={{ whiteSpace: 'pre-line' }}
                                                >
                                                    {block.text}
                                                </p>
                                            </div>
                                        ))}
                                    </div>
                                </section>
                            ))}

                            {/* Footer note */}
                            <div className="rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-[#035A7D]/5 p-8 text-center shadow-sm">
                                <p className="text-gray-700">
                                    Have questions about these Terms? We're happy to clarify.
                                </p>
                                <Link
                                    href={route('contact')}
                                    className="mt-4 inline-block rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                                >
                                    Contact Us
                                </Link>
                            </div>
                        </motion.div>
                    </div>
                </main>

                <PublicFooter />
            </div>
        </>
    );
}
