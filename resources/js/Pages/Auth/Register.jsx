import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PasswordInput from '@/Components/PasswordInput';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <>
            <Head title="Apply Now" />

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

                <header className="relative z-10 mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
                    <Link href="/" className="text-xl font-bold text-[#035A7D]">
                        LGF Scholarship
                    </Link>
                    <Link
                        href={route('login')}
                        className="rounded-full px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-white hover:text-[#035A7D]"
                    >
                        Log in
                    </Link>
                </header>

                <main className="relative z-10 mx-auto flex min-h-[calc(100vh-88px)] w-full max-w-7xl items-center px-6 pb-12 lg:px-8">
                    <div className="grid w-full gap-8 lg:grid-cols-2 lg:gap-12">
                        <motion.section
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45 }}
                            className="rounded-2xl border border-blue-100 bg-white/70 p-8 shadow-sm backdrop-blur"
                        >
                            <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#035A7D]">
                                LIT Scholar Platform
                            </p>
                            <h1 className="mt-3 text-3xl font-bold leading-tight text-gray-900 sm:text-4xl">
                                Start your scholarship application journey
                            </h1>
                            <p className="mt-4 text-gray-600">
                                Create your account to begin the 5-step LGF scholarship form with draft saving, scoring preview, and committee review.
                            </p>

                            <div className="mt-8 space-y-3">
                                {[
                                    'Personal and academic background',
                                    'Financial need and funding gap',
                                    'Guardian and family details',
                                    'Essay and rural teaching commitment',
                                ].map((item) => (
                                    <div
                                        key={item}
                                        className="flex items-start gap-3 rounded-lg border border-gray-200 bg-white p-3"
                                    >
                                        <span className="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-[#035A7D]">
                                            ✓
                                        </span>
                                        <span className="text-sm text-gray-700">{item}</span>
                                    </div>
                                ))}
                            </div>
                        </motion.section>

                        <motion.section
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.08 }}
                            className="rounded-2xl border border-gray-200 bg-white p-8 shadow-xl shadow-green-900/5"
                        >
                            <h2 className="text-2xl font-bold text-gray-900">Create Account</h2>
                            <p className="mt-1 text-sm text-gray-600">
                                Your account lets you save drafts and track application status.
                            </p>

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
                                        autoComplete="username"
                                        onChange={(e) => setData('email', e.target.value)}
                                        required
                                    />

                                    <InputError message={errors.email} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel
                                        htmlFor="password"
                                        value="Password"
                                        className="text-gray-800"
                                    />

                                    <PasswordInput
                                        id="password"
                                        name="password"
                                        value={data.password}
                                        className="mt-1 block w-full border-gray-300 focus:border-[#035A7D] focus:ring-[#035A7D]"
                                        autoComplete="new-password"
                                        onChange={(e) => setData('password', e.target.value)}
                                        required
                                    />

                                    <InputError message={errors.password} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel
                                        htmlFor="password_confirmation"
                                        value="Confirm Password"
                                        className="text-gray-800"
                                    />

                                    <PasswordInput
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        value={data.password_confirmation}
                                        className="mt-1 block w-full border-gray-300 focus:border-[#035A7D] focus:ring-[#035A7D]"
                                        autoComplete="new-password"
                                        onChange={(e) =>
                                            setData('password_confirmation', e.target.value)
                                        }
                                        required
                                    />

                                    <InputError
                                        message={errors.password_confirmation}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="pt-2">
                                    <PrimaryButton
                                        className="w-full justify-center rounded-full bg-[#035A7D] py-3 text-sm font-semibold tracking-wide text-white hover:bg-[#024a6b] focus:bg-[#024a6b]"
                                        disabled={processing}
                                    >
                                        {processing ? 'Creating Account...' : 'Create Account'}
                                    </PrimaryButton>
                                </div>

                                <p className="text-center text-sm text-gray-600">
                                    Already registered?{' '}
                                    <Link
                                        href={route('login')}
                                        className="font-semibold text-[#035A7D] underline-offset-2 hover:underline"
                                    >
                                        Sign in here
                                    </Link>
                                </p>
                            </form>
                        </motion.section>
                    </div>
                </main>
            </div>
        </>
    );
}
