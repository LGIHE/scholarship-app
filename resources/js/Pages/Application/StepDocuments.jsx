import InputError from '@/Components/InputError';
import { RequiredLabel } from './FormComponents';

const DOCUMENT_LIST = [
    {
        key: 'exam_results',
        label: 'Photocopy of Examination Results (PLE, UCE, UACE) / Academic Transcript for Diploma',
        required: true,
        hint: 'Upload your PLE, UCE, UACE results or Diploma transcript',
    },
    {
        key: 'national_id',
        label: 'Photocopy of National ID Card (back and front) / NIN',
        required: true,
        hint: 'Upload both sides of your National ID card or NIN confirmation',
    },
    {
        key: 'birth_certificate',
        label: 'Photocopy of National Birth Certificate',
        required: false,
        hint: 'Upload your national birth certificate',
    },
    {
        key: 'admission_letter',
        label: 'Photocopy of Admission Letter to LiT Partner University / UNITE Campus',
        required: false,
        hint: 'Upload your university admission letter',
    },
    {
        key: 'recommendation_lc1',
        label: 'Recommendation Letter from LC1 Chairperson or Person of Reputable Standing',
        required: false,
        hint: 'Upload recommendation from your area LC1 or equivalent',
    },
    {
        key: 'recommendation_school',
        label: 'Recommendation Letter from Former School',
        required: false,
        hint: 'Upload recommendation from your former school',
    },
    {
        key: 'refugee_number',
        label: 'Photocopy of Refugee Number (for those living in Uganda with refugee status)',
        required: false,
        hint: 'Only required if you have refugee status in Uganda',
    },
];

export default function StepDocuments({ data, errors, stepErrors, setData, hasChanged, isLocked }) {
    const docs = data.documents;

    return (
        <div className="space-y-5">
            <div className="rounded-md border border-blue-200 bg-blue-50 p-4">
                <h4 className="text-sm font-semibold text-blue-900">Required & Optional Attachments</h4>
                <p className="mt-1 text-xs text-blue-700">
                    Please submit clear, legible copies. Accepted formats: PDF, JPG, PNG (max 5MB per file).
                </p>
            </div>

            {DOCUMENT_LIST.map(({ key, label, required, hint }) => (
                <div key={key}>
                    <RequiredLabel htmlFor={key} value={label} required={required} />
                    <p className="mt-1 text-xs text-gray-500 mb-2">{hint}</p>
                    <input
                        id={key}
                        type="file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                        onChange={(e) => {
                            hasChanged.current = true;
                            setData('documents', { ...docs, [key]: e.target.files[0] });
                        }}
                        disabled={isLocked}
                    />
                    {docs[key] && (
                        <p className="mt-2 text-sm text-green-600">
                            ✓ {typeof docs[key] === 'string' ? 'Previously uploaded' : docs[key].name}
                        </p>
                    )}
                    <InputError message={errors[`documents.${key}`] || stepErrors[`documents.${key}`]} className="mt-2" />
                </div>
            ))}
        </div>
    );
}
