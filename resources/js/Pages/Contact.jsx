import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import PublicHeader from '@/Components/PublicHeader';
import PublicFooter from '@/Components/PublicFooter';

export default function Contact({ status }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        subject: '',
        message: '',
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('contact.submit'), {
            onSuccess: () => reset(),
        });
    };

    return (
        <>
            <Head title="Contact Us" />

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

                <PublicHeader currentRoute="contact" />

                <main className="relative z-10 mx-auto w-full max-w-7xl px-6 py-12 lg:px-8">
                    <motion.div
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45 }}
                    >
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                            Contact Us
                        </p>
                        <h1 className="mt-3 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">
                            Get in Touch
                        </h1>
                        <p className="mt-4 text-lg text-gray-600">
                            Have questions or need assistance? We're here to help you succeed.
                        </p>
                    </motion.div>

                    <div className="mt-12 grid gap-8 lg:grid-cols-3">
                        <motion.div
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.1 }}
                            className="space-y-6 lg:col-span-1"
                        >
                            <div className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-2xl">
                                    📧
                                </div>
                                <h3 className="mt-4 font-bold text-gray-900">Email Us</h3>
                                <p className="mt-2 text-sm text-gray-600">
                                    Send us an email and we'll respond within 24 hours.
                                </p>
                                <a
                                    href="mailto:scholarship@lgf.org"
                                    className="mt-3 block text-sm font-semibold text-[#035A7D] hover:underline"
                                >
                                    scholarships@lgfug.org
                                </a>
                            </div>

                            <div className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-2xl">
                                    📞
                                </div>
                                <h3 className="mt-4 font-bold text-gray-900">Call Us</h3>
                                <p className="mt-2 text-sm text-gray-600">
                                    Speak with our team Monday to Friday, 9am-5pm.
                                </p>
                                <a
                                    href="tel:+256704567890"
                                    className="mt-3 block text-sm font-semibold text-[#035A7D] hover:underline"
                                >
                                    +256 (704) 567-890
                                </a>
                            </div>

                            <div className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-2xl">
                                    📍
                                </div>
                                <h3 className="mt-4 font-bold text-gray-900">Visit Us</h3>
                                <p className="mt-2 text-sm text-gray-600">
                                    Sentamu Jagenda Road
                                    <br />
                                    Luzira, Kampala
                                    <br />
                                    Uganda
                                </p>
                            </div>

                            <div className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-2xl">
                                    ⏰
                                </div>
                                <h3 className="mt-4 font-bold text-gray-900">Office Hours</h3>
                                <p className="mt-2 text-sm text-gray-600">
                                    Monday - Friday: 9:00 AM - 5:00 PM
                                    <br />
                                    Saturday - Sunday: Closed
                                </p>
                            </div>
                        </motion.div>

                        <motion.div
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.2 }}
                            className="lg:col-span-2"
                        >
                            <div className="rounded-2xl border border-gray-200 bg-white p-8 shadow-xl shadow-green-900/5">
                                <h2 className="text-2xl font-bold text-gray-900">
                                    Send Us a Message
                                </h2>
                                <p className="mt-1 text-sm text-gray-600">
                                    Fill out the form below and we'll get back to you as soon as
                                    possible.
                                </p>

                                {status && (
                                    <div className="mt-4 rounded-lg bg-blue-50 p-3 text-sm font-medium text-[#035A7D]">
                                        {status}
                                    </div>
                                )}

                                <form onSubmit={submit} className="mt-6 space-y-4">
                                    <div>
                                        <InputLabel
                                            htmlFor="name"
                                            value="Full Name"
                                            className="text-gray-800"
                                        />

                                        <TextInput
                                            id="name"
                                            name="name"
                                            value={data.name}
                                            className="mt-1 block w-full border-gray-300 focus:border-[#035A7D] focus:ring-[#035A7D]"
                                            autoComplete="name"
                                            isFocused={true}
                                            onChange={(e) => setData('name', e.target.value)}
                                            required
                                        />

                                        <InputError message={errors.name} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel
                                            htmlFor="email"
                                            value="Email Address"
                                            className="text-gray-800"
                                        />

                                        <TextInput
                                            id="email"
                                            type="email"
                                            name="email"
                                            value={data.email}
                                            className="mt-1 block w-full border-gray-300 focus:border-[#035A7D] focus:ring-[#035A7D]"
                                            autoComplete="email"
                                            onChange={(e) => setData('email', e.target.value)}
                                            required
                                        />

                                        <InputError message={errors.email} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel
                                            htmlFor="subject"
                                            value="Subject"
                                            className="text-gray-800"
                                        />

                                        <TextInput
                                            id="subject"
                                            name="subject"
                                            value={data.subject}
                                            className="mt-1 block w-full border-gray-300 focus:border-[#035A7D] focus:ring-[#035A7D]"
                                            onChange={(e) => setData('subject', e.target.value)}
                                            required
                                        />

                                        <InputError message={errors.subject} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel
                                            htmlFor="message"
                                            value="Message"
                                            className="text-gray-800"
                                        />

                                        <textarea
                                            id="message"
                                            name="message"
                                            value={data.message}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#035A7D] focus:ring-[#035A7D]"
                                            rows="6"
                                            onChange={(e) => setData('message', e.target.value)}
                                            required
                                        />

                                        <InputError message={errors.message} className="mt-2" />
                                    </div>

                                    <div className="pt-2">
                                        <PrimaryButton
                                            className="w-full justify-center rounded-full bg-[#035A7D] py-3 text-sm font-semibold tracking-wide text-white hover:bg-[#024a6b] focus:bg-[#024a6b]"
                                            disabled={processing}
                                        >
                                            {processing ? 'Sending...' : 'Send Message'}
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                        </motion.div>
                    </div>

                    <motion.section
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45, delay: 0.3 }}
                        className="mt-8 rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 to-[#035A7D]/5 p-8 text-center shadow-sm"
                    >
                        <h2 className="text-2xl font-bold text-gray-900">
                            Ready to Start Your Journey?
                        </h2>
                        <p className="mt-2 text-gray-600">
                            Don't wait! Begin your scholarship application today and take the first
                            step toward making a difference in rural education.
                        </p>
                        <div className="mt-6 flex justify-center gap-4">
                            <Link
                                href={route('register')}
                                className="rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                            >
                                Apply Now
                            </Link>
                            <Link
                                href={route('faq')}
                                className="rounded-full bg-white px-6 py-3 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50"
                            >
                                View FAQ
                            </Link>
                        </div>
                    </motion.section>
                </main>

                <PublicFooter />
            </div>
        </>
    );
}
