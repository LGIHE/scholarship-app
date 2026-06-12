import { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';

export default function PublicHeader({ currentRoute = null }) {
    const { auth } = usePage().props;
    const [menuOpen, setMenuOpen] = useState(false);

    // Close menu on resize to desktop
    useEffect(() => {
        const onResize = () => {
            if (window.innerWidth >= 1024) setMenuOpen(false);
        };
        window.addEventListener('resize', onResize);
        return () => window.removeEventListener('resize', onResize);
    }, []);

    // Prevent body scroll when mobile menu is open
    useEffect(() => {
        document.body.style.overflow = menuOpen ? 'hidden' : '';
        return () => { document.body.style.overflow = ''; };
    }, [menuOpen]);

    const navItems = [
        { name: 'Home', route: 'home' },
        { name: 'About', route: 'about' },
        { name: 'Scholarship', route: 'scholarship' },
        { name: 'Resources', route: 'resources' },
        { name: 'FAQ', route: 'faq' },
        { name: 'Contact', route: 'contact' },
    ];

    const isActive = (r) => currentRoute === r;

    return (
        <>
            <header className="relative z-20 mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
                {/* Logo */}
                <Link href="/" className="flex items-center" onClick={() => setMenuOpen(false)}>
                    <img
                        src="/images/logo.png"
                        alt="LGF Scholarship"
                        className="h-16 w-auto lg:h-20"
                    />
                </Link>

                {/* Desktop nav */}
                <nav className="hidden lg:flex items-center gap-4">
                    {navItems.map((item) => (
                        <Link
                            key={item.route}
                            href={route(item.route)}
                            className={`text-sm font-semibold transition ${
                                isActive(item.route)
                                    ? 'text-[#035A7D]'
                                    : 'text-gray-700 hover:text-[#035A7D]'
                            }`}
                        >
                            {item.name}
                        </Link>
                    ))}
                    {auth?.user ? (
                        <Link
                            href={route('portal')}
                            className="rounded-full bg-[#035A7D] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                        >
                            Dashboard
                        </Link>
                    ) : (
                        <Link
                            href={route('login')}
                            className="rounded-full px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-white hover:text-[#035A7D]"
                        >
                            Log in
                        </Link>
                    )}
                </nav>

                {/* Mobile hamburger button */}
                <button
                    type="button"
                    className="lg:hidden flex flex-col items-center justify-center w-10 h-10 rounded-lg text-gray-700 hover:bg-gray-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#035A7D]"
                    aria-label={menuOpen ? 'Close menu' : 'Open menu'}
                    aria-expanded={menuOpen}
                    onClick={() => setMenuOpen((prev) => !prev)}
                >
                    <span
                        className={`block h-0.5 w-5 bg-current transition-all duration-300 ${
                            menuOpen ? 'translate-y-1.5 rotate-45' : ''
                        }`}
                    />
                    <span
                        className={`block h-0.5 w-5 bg-current my-1 transition-all duration-300 ${
                            menuOpen ? 'opacity-0' : ''
                        }`}
                    />
                    <span
                        className={`block h-0.5 w-5 bg-current transition-all duration-300 ${
                            menuOpen ? '-translate-y-1.5 -rotate-45' : ''
                        }`}
                    />
                </button>
            </header>

            {/* Mobile menu overlay */}
            <AnimatePresence>
                {menuOpen && (
                    <>
                        {/* Backdrop */}
                        <motion.div
                            key="backdrop"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            transition={{ duration: 0.2 }}
                            className="fixed inset-0 z-10 bg-black/30 backdrop-blur-sm lg:hidden"
                            onClick={() => setMenuOpen(false)}
                            aria-hidden="true"
                        />

                        {/* Slide-down panel */}
                        <motion.div
                            key="panel"
                            initial={{ opacity: 0, y: -12 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -12 }}
                            transition={{ duration: 0.25, ease: 'easeOut' }}
                            className="fixed inset-x-0 top-0 z-20 bg-white shadow-xl rounded-b-2xl px-6 pt-24 pb-8 lg:hidden"
                        >
                            {/* Close button inside panel (top-right) */}
                            <button
                                type="button"
                                className="absolute top-5 right-6 flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:bg-gray-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#035A7D]"
                                aria-label="Close menu"
                                onClick={() => setMenuOpen(false)}
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            {/* Logo inside panel */}
                            <div className="absolute top-4 left-6">
                                <Link href="/" onClick={() => setMenuOpen(false)}>
                                    <img src="/images/logo.png" alt="LGF Scholarship" className="h-14 w-auto" />
                                </Link>
                            </div>

                            <nav className="flex flex-col gap-1">
                                {navItems.map((item, i) => (
                                    <motion.div
                                        key={item.route}
                                        initial={{ opacity: 0, x: -10 }}
                                        animate={{ opacity: 1, x: 0 }}
                                        transition={{ duration: 0.2, delay: 0.05 * i }}
                                    >
                                        <Link
                                            href={route(item.route)}
                                            onClick={() => setMenuOpen(false)}
                                            className={`flex items-center rounded-xl px-4 py-3 text-base font-semibold transition ${
                                                isActive(item.route)
                                                    ? 'bg-[#035A7D]/10 text-[#035A7D]'
                                                    : 'text-gray-700 hover:bg-gray-50 hover:text-[#035A7D]'
                                            }`}
                                        >
                                            {item.name}
                                        </Link>
                                    </motion.div>
                                ))}

                                <div className="mt-4 border-t border-gray-100 pt-4">
                                    {auth?.user ? (
                                        <Link
                                            href={route('portal')}
                                            onClick={() => setMenuOpen(false)}
                                            className="flex items-center justify-center rounded-full bg-[#035A7D] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                                        >
                                            Dashboard
                                        </Link>
                                    ) : (
                                        <Link
                                            href={route('login')}
                                            onClick={() => setMenuOpen(false)}
                                            className="flex items-center justify-center rounded-full px-6 py-3 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 hover:text-[#035A7D]"
                                        >
                                            Log in
                                        </Link>
                                    )}
                                </div>
                            </nav>
                        </motion.div>
                    </>
                )}
            </AnimatePresence>
        </>
    );
}
