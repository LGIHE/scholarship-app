import { Link, usePage } from '@inertiajs/react';

export default function PublicHeader({ currentRoute = null }) {
    const { auth } = usePage().props;
    
    const navItems = [
        { name: 'About', route: 'about' },
        { name: 'Resources', route: 'resources' },
        { name: 'FAQ', route: 'faq' },
        { name: 'Contact', route: 'contact' },
    ];

    return (
        <header className="relative z-10 mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
            <Link href="/" className="flex items-center">
                <img 
                    src="/images/logo.png" 
                    alt="LGF Scholarship" 
                    className="h-[5rem] w-auto"
                />
            </Link>
            <nav className="flex items-center gap-4">
                {navItems.map((item) => (
                    <Link
                        key={item.route}
                        href={route(item.route)}
                        className={`text-l font-semibold transition ${
                            currentRoute === item.route
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
                    <>
                        <Link
                            href={route('login')}
                            className="rounded-full px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 transition hover:bg-white hover:text-[#035A7D]"
                        >
                            Log in
                        </Link>
                        <Link
                            href={route('register')}
                            className="rounded-full bg-[#035A7D] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#024a6b]"
                        >
                            Apply Now
                        </Link>
                    </>
                )}
            </nav>
        </header>
    );
}
