import { useState } from 'react';
import Modal from '@/Components/Modal';
import PrimaryButton from '@/Components/PrimaryButton';
import InputError from '@/Components/InputError';

const HEARING_SOURCE_OPTIONS = [
    { value: 'organization_website',      label: 'Organization website' },
    { value: 'social_media',              label: 'Social media (e.g., WhatsApp, Facebook, Twitter, Instagram)' },
    { value: 'referral',                  label: 'Referral from a friend or colleague' },
    { value: 'advertisement',             label: 'Advertisement (TV, radio, newspaper)' },
    { value: 'professional_network',      label: 'Professional network or industry contacts' },
    { value: 'email_newsletter',          label: 'Email newsletter or scholarship alert' },
    { value: 'walk_in',                   label: 'Walk-in / Direct visit to the organization' },
    { value: 'other',                     label: 'Other' },
];

/**
 * Dialog shown to returning applicants who have already submitted their application
 * but have not yet provided the "How did you hear about us?" answer.
 *
 * The dialog cannot be closed without filling in the required field.
 */
export default function HearingSourceDialog({ onSave }) {
    const [source, setSource]       = useState('');
    const [otherText, setOtherText] = useState('');
    const [saving, setSaving]       = useState(false);
    const [errors, setErrors]       = useState({});

    const validate = () => {
        const errs = {};
        if (!source) {
            errs.source = 'Please select how you heard about the scholarship.';
        }
        if (source === 'other' && !otherText.trim()) {
            errs.otherText = 'Please specify how you heard about the scholarship.';
        }
        return errs;
    };

    const handleSave = async () => {
        const errs = validate();
        if (Object.keys(errs).length > 0) {
            setErrors(errs);
            return;
        }

        setSaving(true);
        setErrors({});

        try {
            await onSave({
                hearing_source: source,
                hearing_source_other: source === 'other' ? otherText.trim() : '',
            });
        } catch {
            setErrors({ source: 'Failed to save. Please try again.' });
        } finally {
            setSaving(false);
        }
    };

    return (
        <Modal show={true} closeable={false} maxWidth="lg">
            <div className="p-6">
                {/* Header */}
                <div className="mb-5">
                    <h2 className="text-lg font-semibold text-gray-900">
                        One Quick Question Before You Continue
                    </h2>
                    <p className="mt-1 text-sm text-gray-600">
                        We'd like to know how you found out about the Leaders in Teaching scholarship. This helps us
                        reach more eligible students. Please fill in this required field to continue.
                    </p>
                </div>

                {/* Question */}
                <fieldset className="space-y-3">
                    <legend className="text-sm font-semibold text-gray-800">
                        How did you hear about this scholarship?
                        <span className="ml-1 text-red-500">*</span>
                    </legend>

                    <div className="mt-2 space-y-2">
                        {HEARING_SOURCE_OPTIONS.map((opt) => (
                            <label
                                key={opt.value}
                                className={`flex items-start gap-3 cursor-pointer rounded-md border px-4 py-3 transition-colors ${
                                    source === opt.value
                                        ? 'border-emerald-500 bg-emerald-50'
                                        : 'border-gray-200 bg-white hover:border-emerald-300 hover:bg-emerald-50/30'
                                }`}
                            >
                                <input
                                    type="radio"
                                    name="hearing_source"
                                    value={opt.value}
                                    checked={source === opt.value}
                                    onChange={() => {
                                        setSource(opt.value);
                                        setErrors((prev) => ({ ...prev, source: undefined }));
                                    }}
                                    className="mt-0.5 h-4 w-4 shrink-0 border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                />
                                <span className="text-sm text-gray-700">{opt.label}</span>
                            </label>
                        ))}
                    </div>

                    <InputError message={errors.source} className="mt-1" />

                    {/* "Other" free-text field */}
                    {source === 'other' && (
                        <div className="mt-3">
                            <label
                                htmlFor="hearing_source_other"
                                className="block text-sm font-medium text-gray-700"
                            >
                                Please specify
                                <span className="ml-1 text-red-500">*</span>
                            </label>
                            <input
                                id="hearing_source_other"
                                type="text"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                value={otherText}
                                onChange={(e) => {
                                    setOtherText(e.target.value);
                                    setErrors((prev) => ({ ...prev, otherText: undefined }));
                                }}
                                placeholder="Please describe how you heard about the scholarship..."
                                maxLength={500}
                                autoFocus
                            />
                            <InputError message={errors.otherText} className="mt-1" />
                        </div>
                    )}
                </fieldset>

                {/* Footer */}
                <div className="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                    <p className="flex-1 text-xs text-gray-400 italic">
                        This field is required and cannot be skipped.
                    </p>
                    <PrimaryButton onClick={handleSave} disabled={saving}>
                        {saving ? 'Saving...' : 'Save & Continue'}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    );
}
