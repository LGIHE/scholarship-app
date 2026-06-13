import InputError from '@/Components/InputError';
import { RequiredLabel } from './FormComponents';

function countWords(text) {
    const normalized = (text || '').trim();
    if (!normalized) return 0;
    return normalized.split(/\s+/).length;
}

export default function StepSectionB6({ data, errors, stepErrors, updateSection, isLocked }) {
    const wordCount = countWords(data.essay.motivation);

    return (
        <div className="space-y-6">
            <div className="rounded-md border border-gray-200 p-4">
                <h4 className="mb-4 font-semibold text-gray-800 text-base border-b pb-2">
                    Motivation Statement
                </h4>
                <div>
                    <RequiredLabel
                        htmlFor="motivation"
                        value="20. Write a 250-word motivation, expressing why you need this scholarship offer and how you intend to use it to improve yourself and the community around you."
                        required
                    />
                    <textarea
                        id="motivation"
                        rows={12}
                        className="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                        value={data.essay.motivation}
                        onChange={(e) => updateSection('essay', 'motivation', e.target.value)}
                        disabled={isLocked}
                        placeholder="Write your motivation here (target: 250 words)..."
                    />
                    <div className="mt-2 flex items-center justify-between">
                        <div className="text-xs text-gray-500">
                            Word count:{' '}
                            <span className={wordCount >= 250 ? 'text-green-600 font-semibold' : 'text-amber-600'}>
                                {wordCount} / 250
                            </span>
                        </div>
                        {wordCount >= 250 && (
                            <span className="text-xs text-green-600 font-medium">✓ Target reached</span>
                        )}
                    </div>
                    <InputError message={errors['essay.motivation'] || stepErrors['essay.motivation']} className="mt-2" />
                </div>
            </div>
        </div>
    );
}
