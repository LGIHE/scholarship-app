import { Link } from '@inertiajs/react';

export default function PublicFooter() {
    return (
        <footer className="bg-gray-900 text-gray-400 py-16">
            <div className="mx-auto max-w-7xl px-6 lg:px-8">
                <div className="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                    <div>
                        <img 
                            src="/images/logo-white.png" 
                            alt="LGF Scholarship" 
                            className="h-10 w-auto mb-4"
                        />
                        <p className="text-sm text-gray-400">
                            Empowering future educators to serve rural communities through
                            comprehensive scholarship support.
                        </p>
                    </div>
                    <div>
                        <h3 className="text-white font-semibold mb-4">Quick Links</h3>
                        <ul className="space-y-2 text-sm">
                            <li>
                                <Link href={route('about')} className="hover:text-white transition">
                                    About Us
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href={route('resources')}
                                    className="hover:text-white transition"
                                >
                                    Resources
                                </Link>
                            </li>
                            <li>
                                <Link href={route('faq')} className="hover:text-white transition">
                                    FAQ
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href={route('register')}
                                    className="hover:text-white transition"
                                >
                                    Apply Now
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 className="text-white font-semibold mb-4">Contact</h3>
                        <ul className="space-y-2 text-sm">
                            <li>Email: scholarship@lgf.org</li>
                            <li>Phone: +1 (234) 567-890</li>
                            <li>
                                <Link
                                    href={route('contact')}
                                    className="hover:text-white transition"
                                >
                                    Contact Form
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 className="text-white font-semibold mb-4">Legal</h3>
                        <ul className="space-y-2 text-sm">
                            <li>
                                <a href="#" className="hover:text-white transition">
                                    Privacy Policy
                                </a>
                            </li>
                            <li>
                                <a href="#" className="hover:text-white transition">
                                    Terms of Service
                                </a>
                            </li>
                            <li>
                                <Link href={route('login')} className="hover:text-white transition">
                                    Scholar Login
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div className="border-t border-gray-800 pt-8 text-center">
                    <p className="text-sm">
                        &copy; {new Date().getFullYear()} Luigi Giussani Foundation. All rights
                        reserved.
                    </p>
                </div>
            </div>
        </footer>
    );
}
