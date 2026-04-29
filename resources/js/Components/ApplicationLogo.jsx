export default function ApplicationLogo({ className = '', white = false, ...props }) {
    const logoSrc = white ? '/images/logo-white.png' : '/images/logo.png';
    
    return (
        <img
            src={logoSrc}
            alt="LGF Scholarship Logo"
            className={className}
            {...props}
        />
    );
}
