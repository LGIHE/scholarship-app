import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import TextInput from '@/Components/TextInput';
import ALL_COUNTRIES from '@/data/countries';
import { UGANDA_REGIONS, DISTRICTS_BY_REGION } from '@/data/uganda';

/**
 * AddressFields
 *
 * A reusable set of four address sub-fields (country → region → district → village).
 * When Uganda is selected as the country the region and district become dropdowns
 * populated with Uganda's administrative data. For all other countries they fall
 * back to plain text inputs so applicants can type freely.
 *
 * Props
 * ─────
 * @param {string}   countryValue   – current country value
 * @param {string}   regionValue    – current region value
 * @param {string}   districtValue  – current district/state value
 * @param {string}   villageValue   – current village/parish/sub-county value
 * @param {function} onCountryChange  (value: string) => void
 * @param {function} onRegionChange   (value: string) => void
 * @param {function} onDistrictChange (value: string) => void
 * @param {function} onVillageChange  (value: string) => void
 * @param {object}   errors         – field-keyed error messages
 * @param {string}   countryKey     – error key for country field
 * @param {string}   regionKey      – error key for region field
 * @param {string}   districtKey    – error key for district field
 * @param {string}   villageKey     – error key for village field
 * @param {boolean}  disabled       – lock all fields (read-only mode)
 */
export default function AddressFields({
    countryValue  = '',
    regionValue   = '',
    districtValue = '',
    villageValue  = '',
    onCountryChange,
    onRegionChange,
    onDistrictChange,
    onVillageChange,
    errors        = {},
    countryKey    = 'country',
    regionKey     = 'region',
    districtKey   = 'district',
    villageKey    = 'village',
    disabled      = false,
}) {
    const isUganda         = countryValue === 'Uganda';
    const regionDistricts  = isUganda && regionValue ? (DISTRICTS_BY_REGION[regionValue] ?? []) : [];

    const selectClass =
        'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 ' +
        'focus:ring-emerald-500 text-sm disabled:bg-gray-100 disabled:cursor-not-allowed uppercase';

    // When region changes, clear the district so stale values don't persist
    function handleRegionChange(value) {
        onRegionChange(value);
        if (districtValue) onDistrictChange('');
    }

    // When country changes, clear region + district
    function handleCountryChange(value) {
        onCountryChange(value);
        if (regionValue)   onRegionChange('');
        if (districtValue) onDistrictChange('');
    }

    return (
        <div className="grid grid-cols-2 gap-3 mt-2 md:grid-cols-4">
            {/* ── Country ─────────────────────────────────────────── */}
            <div>
                <InputLabel value="Country" className="text-xs text-gray-500" />
                <select
                    className={selectClass}
                    value={countryValue}
                    onChange={(e) => handleCountryChange(e.target.value)}
                    disabled={disabled}
                >
                    <option value="">— Select —</option>
                    <option value="Uganda">Uganda</option>
                    <optgroup label="Other Countries">
                        {ALL_COUNTRIES.map((c) => (
                            <option key={c} value={c}>{c}</option>
                        ))}
                    </optgroup>
                </select>
                <InputError message={errors[countryKey]} className="mt-1" />
            </div>

            {/* ── Region ──────────────────────────────────────────── */}
            <div>
                <InputLabel value="Region" className="text-xs text-gray-500" />
                {isUganda ? (
                    <select
                        className={selectClass}
                        value={regionValue}
                        onChange={(e) => handleRegionChange(e.target.value)}
                        disabled={disabled}
                    >
                        <option value="">— Select —</option>
                        {UGANDA_REGIONS.map((r) => (
                            <option key={r} value={r}>{r}</option>
                        ))}
                    </select>
                ) : (
                    <TextInput
                        className="mt-1 block w-full text-sm uppercase"
                        value={regionValue}
                        onChange={(e) => onRegionChange(e.target.value)}
                        disabled={disabled}
                        placeholder="Region / State / Province"
                    />
                )}
                <InputError message={errors[regionKey]} className="mt-1" />
            </div>

            {/* ── District / State ────────────────────────────────── */}
            <div>
                <InputLabel
                    value={isUganda ? 'District' : 'District / State'}
                    className="text-xs text-gray-500"
                />
                {isUganda ? (
                    <select
                        className={selectClass}
                        value={districtValue}
                        onChange={(e) => onDistrictChange(e.target.value)}
                        disabled={disabled || !regionValue}
                    >
                        <option value="">
                            {regionValue ? '— Select district —' : '— Select region first —'}
                        </option>
                        {regionDistricts.map((d) => (
                            <option key={d} value={d}>{d}</option>
                        ))}
                    </select>
                ) : (
                    <TextInput
                        className="mt-1 block w-full text-sm uppercase"
                        value={districtValue}
                        onChange={(e) => onDistrictChange(e.target.value)}
                        disabled={disabled}
                        placeholder="District / State"
                    />
                )}
                <InputError message={errors[districtKey]} className="mt-1" />
            </div>

            {/* ── Village / Parish / Sub-county ───────────────────── */}
            <div>
                <InputLabel value="Village/Parish/Sub-county" className="text-xs text-gray-500" />
                <TextInput
                    className="mt-1 block w-full text-sm uppercase"
                    value={villageValue}
                    onChange={(e) => onVillageChange(e.target.value)}
                    disabled={disabled}
                />
                <InputError message={errors[villageKey]} className="mt-1" />
            </div>
        </div>
    );
}
