import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

const sections = [
    {
        id: 'information-we-collect',
        title: '1. Information We Collect',
        content: [
            {
                subtitle: '1.1 Information You Provide',
                text: 'When you apply for the LGF Scholarship or create an account on our platform, we collect information you provide directly, including your full name, email address, phone number, date of birth, national identification details, academic records and transcripts, personal statements and essays, financial information relevant to scholarship eligibility, and correspondence you send to us.',
            },
            {
                subtitle: '1.2 Information Collected Automatically',
                text: "When you access our platform, we may automatically collect certain technical information, including your IP address, browser type and version, operating system, pages visited and time spent, referring URLs, and device identifiers. This data is used solely to improve our platform's performance and user experience.",
            },
            {
                subtitle: '1.3 Information from Third Parties',
                text: 'With your consent, we may receive information from academic institutions, referees, or other parties you designate as part of the scholarship verification process.',
            },
        ],
    },
    {
        id: 'how-we-use',
        title: '2. How We Use Your Information',
        content: [
            {
                subtitle: null,
                text: 'We use the information we collect for the following purposes:',
            },
            {
                subtitle: 'Scholarship Administration',
                text: 'To process your application, assess eligibility, communicate decisions, disburse awards, and monitor academic progress throughout the scholarship period.',
            },
            {
                subtitle: 'Communication',
                text: 'To send you important updates about your application status, scholarship requirements, renewal deadlines, programme announcements, and other relevant information.',
            },
            {
                subtitle: 'Platform Operation',
                text: 'To maintain and improve our online portal, authenticate users, prevent fraud, and ensure the security and integrity of our systems.',
            },
            {
                subtitle: 'Legal Compliance',
                text: 'To meet our legal and regulatory obligations, resolve disputes, and enforce our agreements.',
            },
            {
                subtitle: 'Reporting and Accountability',
                text: 'To produce anonymised statistical reports on scholarship outcomes for internal evaluation and reporting to donors and partners. Individual identities are never disclosed in public reports.',
            },
        ],
    },
    {
        id: 'sharing',
        title: '3. Sharing of Your Information',
        content: [
            {
                subtitle: null,
                text: 'The Luigi Giussani Foundation does not sell, rent, or trade your personal information. We may share your data only in the following limited circumstances:',
            },
            {
                subtitle: 'Partner Institutions',
                text: 'We may share relevant application information with accredited universities, teaching training colleges, or placement schools as necessary to fulfil scholarship commitments.',
            },
            {
                subtitle: 'Service Providers',
                text: 'We engage trusted third-party service providers (e.g., email delivery, cloud hosting) who process data on our behalf under strict confidentiality agreements and may not use your data for any other purpose.',
            },
            {
                subtitle: 'Legal Requirements',
                text: 'We may disclose information where required by law, court order, or government authority, or where we believe disclosure is necessary to protect the rights, property, or safety of the Foundation, our scholars, or others.',
            },
        ],
    },
    {
        id: 'data-retention',
        title: '4. Data Retention',
        content: [
            {
                subtitle: null,
                text: 'We retain your personal information for as long as necessary to fulfil the purposes described in this Policy, including for the duration of any active scholarship award and for a reasonable period thereafter to meet legal and administrative obligations. Application records for unsuccessful applicants are retained for a period of two years before being securely deleted or anonymised.',
            },
        ],
    },
    {
        id: 'security',
        title: '5. Data Security',
        content: [
            {
                subtitle: null,
                text: 'We implement appropriate technical and organisational measures to protect your personal information against unauthorised access, accidental loss, destruction, or disclosure. These measures include encrypted data transmission (TLS/SSL), access controls limiting data to authorised personnel, regular security assessments, and secure data storage practices. While we strive to protect your information, no internet transmission is completely secure, and we cannot guarantee absolute security.',
            },
        ],
    },
    {
        id: 'your-rights',
        title: '6. Your Rights',
        content: [
            {
                subtitle: null,
                text: 'You have the following rights regarding your personal information:',
            },
            {
                subtitle: 'Access',
                text: 'You may request a copy of the personal information we hold about you.',
            },
            {
                subtitle: 'Correction',
                text: 'You may request that we correct inaccurate or incomplete information.',
            },
            {
                subtitle: 'Deletion',
                text: 'You may request deletion of your data where it is no longer necessary for the purposes for which it was collected, subject to any legal retention obligations.',
            },
            {
                subtitle: 'Objection',
                text: 'You may object to certain uses of your data, including direct marketing communications.',
            },
            {
                subtitle: 'Withdrawal of Consent',
                text: 'Where processing is based on your consent, you may withdraw it at any time without affecting the lawfulness of prior processing.',
            },
            {
                subtitle: null,
                text: 'To exercise any of these rights, please contact us at info@lgfug.org. We will respond to your request within 30 days.',
            },
        ],
    },
    {
        id: 'cookies',
        title: '7. Cookies',
        content: [
            {
                subtitle: null,
                text: 'Our platform uses essential cookies to maintain your session and ensure secure login. We do not use cookies for advertising or cross-site tracking. You may configure your browser to refuse cookies, though this may affect platform functionality.',
            },
        ],
    },
    {
        id: 'minors',
        title: '8. Children and Minors',
        content: [
            {
                subtitle: null,
                text: 'Our scholarship programme is intended for post-secondary students who are typically 18 years of age or older. We do not knowingly collect personal information from individuals under 18 without verifiable parental or guardian consent. If you believe we have inadvertently collected such information, please contact us immediately.',
            },
        ],
    },
    {
        id: 'changes',
        title: '9. Changes to This Policy',
        content: [
            {
                subtitle: null,
                text: 'We may update this Privacy Policy from time to time to reflect changes in our practices or applicable law. We will notify you of material changes by posting the updated policy on this page with a revised effective date. Continued use of our platform after such changes constitutes acceptance of the updated policy.',
            },
        ],
    },
    {
        id: 'contact',
        title: '10. Contact Us',
        content: [
            {
                subtitle: null,
                text: 'If you have questions or concerns about this Privacy Policy or how we handle your personal information, please contact us:',
            },
            {
                subtitle: 'Luigi Giussani Foundation',
                text: 'Email: info@lgfug.org\nPhone: (+256) 764 078712\nWebsite: lgfug.org',
            },
        ],
    },
];

export default function PrivacyPolicy() {
    return (
        <>
            <Head title="Privacy Policy" />

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
                            Privacy Policy
                        </h1>
                        <p className="mt-4 text-lg text-gray-600">
                            This Privacy Policy explains how the Luigi Giussani Foundation collects,
                            uses, and protects your personal information when you use the LGF
                            Scholarship Platform.
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
                                        href={route('terms')}
                                        className="block text-sm text-[#035A7D] hover:underline"
                                    >
                                        View Terms of Service →
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
                            {sections.map((section, sIdx) => (
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
                                    Questions about this policy? We're here to help.
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
