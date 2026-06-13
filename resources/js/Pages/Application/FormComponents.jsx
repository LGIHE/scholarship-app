import InputLabel from '@/Components/InputLabel';

// Label with an optional red asterisk for required fields
export const RequiredLabel = ({ htmlFor, value, required = false }) => (
    <InputLabel
        htmlFor={htmlFor}
        value={
            <span>
                {value}
                {required && <span className="ml-1 text-red-500">*</span>}
            </span>
        }
    />
);

// Styled checkbox
export const CheckboxField = ({ id, label, checked, onChange, disabled }) => (
    <label className="flex items-center gap-2 cursor-pointer">
        <input
            type="checkbox"
            id={id}
            checked={!!checked}
            onChange={(e) => onChange(e.target.checked)}
            disabled={disabled}
            className="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
        />
        <span className="text-sm text-gray-700">{label}</span>
    </label>
);

// Styled radio button
export const RadioField = ({ name, value, label, checked, onChange, disabled }) => (
    <label className="flex items-center gap-2 cursor-pointer">
        <input
            type="radio"
            name={name}
            value={value}
            checked={checked}
            onChange={() => onChange(value)}
            disabled={disabled}
            className="h-4 w-4 border-gray-300 text-emerald-600 focus:ring-emerald-500"
        />
        <span className="text-sm text-gray-700">{label}</span>
    </label>
);
