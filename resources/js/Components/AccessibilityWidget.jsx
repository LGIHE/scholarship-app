import { createContext, useContext, useEffect, useRef, useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';

// ─── Context ─────────────────────────────────────────────────────────────────

const defaultSettings = {
    fontSize: 100,        // % — applied as html font-size
    contrast: 'normal',   // 'normal' | 'high' | 'inverted'
    letterSpacing: 0,     // extra px
    lineHeight: 'normal', // 'normal' | 'relaxed' | 'loose'
    dyslexiaFont: false,
    hideImages: false,
    focusOutline: false,
    reducedMotion: false,
};

const STORAGE_KEY = 'a11y_settings';

function loadSettings() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (raw) return { ...defaultSettings, ...JSON.parse(raw) };
    } catch {/* ignore */}
    return defaultSettings;
}

const A11yContext = createContext({ settings: defaultSettings, update: () => {} });

export function useA11y() {
    return useContext(A11yContext);
}

// ─── Apply settings to <html> ─────────────────────────────────────────────────

function applySettings(settings) {
    const html = document.documentElement;

    // Font size
    html.style.fontSize = settings.fontSize + '%';

    // Contrast
    html.classList.remove('a11y-high-contrast', 'a11y-inverted');
    if (settings.contrast === 'high') html.classList.add('a11y-high-contrast');
    if (settings.contrast === 'inverted') html.classList.add('a11y-inverted');

    // Letter spacing
    html.style.setProperty('--a11y-letter-spacing', settings.letterSpacing + 'px');

    // Line height
    html.classList.remove('a11y-line-relaxed', 'a11y-line-loose');
    if (settings.lineHeight === 'relaxed') html.classList.add('a11y-line-relaxed');
    if (settings.lineHeight === 'loose') html.classList.add('a11y-line-loose');

    // Dyslexia font
    html.classList.toggle('a11y-dyslexia', settings.dyslexiaFont);

    // Hide images
    html.classList.toggle('a11y-hide-images', settings.hideImages);

    // Focus outline
    html.classList.toggle('a11y-focus-outline', settings.focusOutline);

    // Reduced motion
    html.classList.toggle('a11y-reduced-motion', settings.reducedMotion);
}

// ─── Provider (wrap around <App>) ─────────────────────────────────────────────

export function AccessibilityProvider({ children }) {
    const [settings, setSettings] = useState(loadSettings);

    // Apply on mount and whenever settings change
    useEffect(() => {
        applySettings(settings);
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(settings)); } catch {/* ignore */}
    }, [settings]);

    function update(key, value) {
        setSettings(prev => ({ ...prev, [key]: value }));
    }

    return (
        <A11yContext.Provider value={{ settings, update }}>
            {children}
            <AccessibilityWidget />
        </A11yContext.Provider>
    );
}

// ─── Floating Widget ──────────────────────────────────────────────────────────

const BRAND = '#035A7D';

export default function AccessibilityWidget() {
    const { settings, update } = useA11y();
    const [open, setOpen] = useState(false);
    const panelRef = useRef(null);
    const triggerRef = useRef(null);

    // Close on outside click
    useEffect(() => {
        if (!open) return;
        function handleClick(e) {
            if (
                panelRef.current && !panelRef.current.contains(e.target) &&
                triggerRef.current && !triggerRef.current.contains(e.target)
            ) {
                setOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClick);
        return () => document.removeEventListener('mousedown', handleClick);
    }, [open]);

    // Close on Escape
    useEffect(() => {
        function handleKey(e) {
            if (e.key === 'Escape') setOpen(false);
        }
        document.addEventListener('keydown', handleKey);
        return () => document.removeEventListener('keydown', handleKey);
    }, []);

    // Lock focus inside panel when open
    useEffect(() => {
        if (open && panelRef.current) {
            const focusable = panelRef.current.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            focusable[0]?.focus();
        }
    }, [open]);

    function resetAll() {
        const fresh = { ...defaultSettings };
        Object.keys(fresh).forEach(k =>
            update(k, fresh[k])
        );
    }

    return (
        <>
            {/* Floating trigger button */}
            <motion.button
                ref={triggerRef}
                onClick={() => setOpen(v => !v)}
                aria-label="Open accessibility options"
                aria-expanded={open}
                aria-controls="a11y-panel"
                whileHover={{ scale: 1.08 }}
                whileTap={{ scale: 0.95 }}
                className="fixed bottom-6 right-6 z-[9999] flex h-14 w-14 items-center justify-center rounded-full shadow-lg focus:outline-none focus-visible:ring-4 focus-visible:ring-offset-2"
                style={{ backgroundColor: BRAND, color: '#fff', focusRingColor: BRAND }}
            >
                {/* Universal access icon (SVG inline so no external dep) */}
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    className="h-7 w-7"
                    aria-hidden="true"
                >
                    <circle cx="12" cy="4" r="2" />
                    <path d="M19 8.5c-2.3-.8-4.6-1.2-7-1.2s-4.7.4-7 1.2l.6 1.9C7.4 9.6 9.7 9.3 12 9.3s4.6.3 6.4.9L19 8.5z" />
                    <path d="M16.2 10.1l-1.5 5.3-2.7-3.1-2.7 3.1-1.5-5.3-2 .5 2 7 4.2-4.8 4.2 4.8 2-7-2-.5z" />
                </svg>
                <span className="sr-only">Accessibility</span>
            </motion.button>

            {/* Side panel */}
            <AnimatePresence>
                {open && (
                    <>
                        {/* Mobile backdrop */}
                        <motion.div
                            key="backdrop"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="fixed inset-0 z-[9998] bg-black/30 lg:hidden"
                            aria-hidden="true"
                            onClick={() => setOpen(false)}
                        />

                        {/* Panel */}
                        <motion.aside
                            key="panel"
                            id="a11y-panel"
                            ref={panelRef}
                            role="dialog"
                            aria-modal="true"
                            aria-label="Accessibility Settings"
                            initial={{ x: '100%', opacity: 0 }}
                            animate={{ x: 0, opacity: 1 }}
                            exit={{ x: '100%', opacity: 0 }}
                            transition={{ type: 'spring', stiffness: 320, damping: 32 }}
                            className="fixed right-0 top-0 z-[9999] flex h-full w-full flex-col bg-white shadow-2xl sm:w-96 overflow-y-auto"
                        >
                            {/* Header */}
                            <div
                                className="flex items-center justify-between px-5 py-4 border-b border-gray-100"
                                style={{ backgroundColor: BRAND }}
                            >
                                <h2 className="text-lg font-semibold text-white flex items-center gap-2">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        className="h-5 w-5"
                                        aria-hidden="true"
                                    >
                                        <circle cx="12" cy="4" r="2" />
                                        <path d="M19 8.5c-2.3-.8-4.6-1.2-7-1.2s-4.7.4-7 1.2l.6 1.9C7.4 9.6 9.7 9.3 12 9.3s4.6.3 6.4.9L19 8.5z" />
                                        <path d="M16.2 10.1l-1.5 5.3-2.7-3.1-2.7 3.1-1.5-5.3-2 .5 2 7 4.2-4.8 4.2 4.8 2-7-2-.5z" />
                                    </svg>
                                    Accessibility
                                </h2>
                                <button
                                    onClick={() => setOpen(false)}
                                    aria-label="Close accessibility panel"
                                    className="rounded-md p-1.5 text-white/80 hover:text-white hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white transition"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2} aria-hidden="true">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {/* Body */}
                            <div className="flex-1 px-5 py-6 space-y-7">

                                {/* Font Size */}
                                <Section title="Text Size">
                                    <div className="flex items-center gap-3">
                                        <StepButton
                                            label="Decrease text size"
                                            onClick={() => update('fontSize', Math.max(80, settings.fontSize - 10))}
                                            disabled={settings.fontSize <= 80}
                                        >
                                            <span className="text-lg font-bold leading-none select-none" aria-hidden="true">A−</span>
                                        </StepButton>

                                        <div className="flex-1 text-center">
                                            <span className="text-2xl font-semibold tabular-nums" aria-live="polite">
                                                {settings.fontSize}%
                                            </span>
                                        </div>

                                        <StepButton
                                            label="Increase text size"
                                            onClick={() => update('fontSize', Math.min(150, settings.fontSize + 10))}
                                            disabled={settings.fontSize >= 150}
                                        >
                                            <span className="text-lg font-bold leading-none select-none" aria-hidden="true">A+</span>
                                        </StepButton>
                                    </div>

                                    <input
                                        type="range"
                                        min="80"
                                        max="150"
                                        step="10"
                                        value={settings.fontSize}
                                        onChange={e => update('fontSize', Number(e.target.value))}
                                        aria-label="Text size slider"
                                        className="w-full mt-2 accent-[#035A7D]"
                                    />
                                    <div className="flex justify-between text-xs text-gray-400 mt-0.5">
                                        <span>80%</span><span>115%</span><span>150%</span>
                                    </div>
                                </Section>

                                {/* Contrast */}
                                <Section title="Colour Contrast">
                                    <div className="grid grid-cols-3 gap-2" role="radiogroup" aria-label="Colour contrast">
                                        {[
                                            { value: 'normal',   label: 'Default',  bg: 'bg-white', border: 'border-gray-200', text: 'text-gray-800' },
                                            { value: 'high',     label: 'High',     bg: 'bg-black', border: 'border-gray-900', text: 'text-yellow-300' },
                                            { value: 'inverted', label: 'Inverted', bg: 'bg-gray-200', border: 'border-gray-400', text: 'text-black' },
                                        ].map(opt => (
                                            <ContrastOption
                                                key={opt.value}
                                                {...opt}
                                                selected={settings.contrast === opt.value}
                                                onSelect={() => update('contrast', opt.value)}
                                            />
                                        ))}
                                    </div>
                                </Section>

                                {/* Letter Spacing */}
                                <Section title="Letter Spacing">
                                    <div className="flex items-center gap-3">
                                        <StepButton
                                            label="Decrease letter spacing"
                                            onClick={() => update('letterSpacing', Math.max(0, settings.letterSpacing - 1))}
                                            disabled={settings.letterSpacing <= 0}
                                        >
                                            <span aria-hidden="true" className="font-mono text-sm font-bold">A·B−</span>
                                        </StepButton>
                                        <span className="flex-1 text-center text-xl font-semibold tabular-nums" aria-live="polite">
                                            +{settings.letterSpacing}px
                                        </span>
                                        <StepButton
                                            label="Increase letter spacing"
                                            onClick={() => update('letterSpacing', Math.min(6, settings.letterSpacing + 1))}
                                            disabled={settings.letterSpacing >= 6}
                                        >
                                            <span aria-hidden="true" className="font-mono text-sm font-bold">A·B+</span>
                                        </StepButton>
                                    </div>
                                </Section>

                                {/* Line Height */}
                                <Section title="Line Height">
                                    <div className="flex gap-2" role="radiogroup" aria-label="Line height">
                                        {[
                                            { value: 'normal',  label: 'Normal' },
                                            { value: 'relaxed', label: 'Relaxed' },
                                            { value: 'loose',   label: 'Loose' },
                                        ].map(opt => (
                                            <ToggleChip
                                                key={opt.value}
                                                label={opt.label}
                                                selected={settings.lineHeight === opt.value}
                                                onSelect={() => update('lineHeight', opt.value)}
                                            />
                                        ))}
                                    </div>
                                </Section>

                                {/* Toggle options */}
                                <Section title="Reading Aids">
                                    <div className="space-y-3">
                                        <ToggleRow
                                            label="Dyslexia-friendly font"
                                            description="Switches to OpenDyslexic font"
                                            checked={settings.dyslexiaFont}
                                            onChange={v => update('dyslexiaFont', v)}
                                        />
                                        <ToggleRow
                                            label="Hide images"
                                            description="Removes decorative images from view"
                                            checked={settings.hideImages}
                                            onChange={v => update('hideImages', v)}
                                        />
                                        <ToggleRow
                                            label="Enhanced focus outline"
                                            description="Adds bold outlines to focused elements"
                                            checked={settings.focusOutline}
                                            onChange={v => update('focusOutline', v)}
                                        />
                                        <ToggleRow
                                            label="Reduce animations"
                                            description="Minimises motion across the site"
                                            checked={settings.reducedMotion}
                                            onChange={v => update('reducedMotion', v)}
                                        />
                                    </div>
                                </Section>
                            </div>

                            {/* Footer */}
                            <div className="border-t border-gray-100 px-5 py-4">
                                <button
                                    onClick={resetAll}
                                    className="w-full rounded-lg border border-gray-300 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#035A7D] transition"
                                >
                                    Reset to defaults
                                </button>
                                <p className="mt-3 text-center text-xs text-gray-400">
                                    Settings are saved automatically in your browser.
                                </p>
                            </div>
                        </motion.aside>
                    </>
                )}
            </AnimatePresence>
        </>
    );
}

// ─── Small reusable sub-components ────────────────────────────────────────────

function Section({ title, children }) {
    return (
        <div>
            <h3 className="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400">
                {title}
            </h3>
            {children}
        </div>
    );
}

function StepButton({ label, onClick, disabled, children }) {
    return (
        <button
            type="button"
            onClick={onClick}
            disabled={disabled}
            aria-label={label}
            className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-gray-200 text-gray-700 transition hover:border-[#035A7D] hover:text-[#035A7D] disabled:cursor-not-allowed disabled:opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#035A7D]"
        >
            {children}
        </button>
    );
}

function ContrastOption({ value, label, bg, border, text, selected, onSelect }) {
    return (
        <button
            type="button"
            role="radio"
            aria-checked={selected}
            onClick={onSelect}
            className={`flex flex-col items-center gap-1.5 rounded-lg border-2 p-3 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#035A7D] ${border} ${bg} ${
                selected ? 'ring-2 ring-[#035A7D] ring-offset-1' : 'hover:ring-1 hover:ring-[#035A7D]/40'
            }`}
        >
            <span className={`text-xs font-semibold ${text}`}>{label}</span>
            <span className={`block h-1.5 w-full rounded-full ${value === 'normal' ? 'bg-gray-300' : value === 'high' ? 'bg-yellow-300' : 'bg-gray-500'}`} aria-hidden="true" />
        </button>
    );
}

function ToggleChip({ label, selected, onSelect }) {
    return (
        <button
            type="button"
            role="radio"
            aria-checked={selected}
            onClick={onSelect}
            className={`flex-1 rounded-lg border py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#035A7D] ${
                selected
                    ? 'border-[#035A7D] bg-[#035A7D] text-white'
                    : 'border-gray-200 text-gray-600 hover:border-[#035A7D]/50 hover:text-[#035A7D]'
            }`}
        >
            {label}
        </button>
    );
}

function ToggleRow({ label, description, checked, onChange }) {
    const id = `a11y-toggle-${label.replace(/\s+/g, '-').toLowerCase()}`;
    return (
        <div className="flex items-start gap-3">
            <button
                type="button"
                id={id}
                role="switch"
                aria-checked={checked}
                onClick={() => onChange(!checked)}
                className={`relative mt-0.5 inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#035A7D] focus-visible:ring-offset-2 ${
                    checked ? 'bg-[#035A7D]' : 'bg-gray-200'
                }`}
            >
                <span
                    className={`inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ${
                        checked ? 'translate-x-5' : 'translate-x-0'
                    }`}
                    aria-hidden="true"
                />
            </button>
            <label htmlFor={id} className="cursor-pointer select-none" onClick={() => onChange(!checked)}>
                <span className="block text-sm font-medium text-gray-800">{label}</span>
                <span className="block text-xs text-gray-400">{description}</span>
            </label>
        </div>
    );
}
