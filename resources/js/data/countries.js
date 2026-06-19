/**
 * Sorted list of world countries for nationality dropdowns.
 * African nations are listed first for regional relevance.
 */
const AFRICAN_COUNTRIES = [
    'Burundi', 'Democratic Republic of Congo', 'Ethiopia', 'Kenya', 'Rwanda',
    'South Sudan', 'Sudan', 'Tanzania', 'Uganda',
    'Angola', 'Benin', 'Botswana', 'Burkina Faso', 'Cameroon', 'Cape Verde',
    'Central African Republic', 'Chad', 'Comoros', 'Djibouti', 'Egypt',
    'Equatorial Guinea', 'Eritrea', 'Eswatini', 'Gabon', 'Gambia', 'Ghana',
    'Guinea', 'Guinea-Bissau', 'Ivory Coast', 'Lesotho', 'Liberia', 'Libya',
    'Madagascar', 'Malawi', 'Mali', 'Mauritania', 'Mauritius', 'Morocco',
    'Mozambique', 'Namibia', 'Niger', 'Nigeria', 'Republic of Congo',
    'Sao Tome and Principe', 'Senegal', 'Sierra Leone', 'Somalia',
    'South Africa', 'Togo', 'Tunisia', 'Zambia', 'Zimbabwe',
];

const OTHER_COUNTRIES = [
    'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Antigua and Barbuda',
    'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan',
    'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium',
    'Belize', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Brazil',
    'Brunei', 'Bulgaria', 'Cambodia', 'Canada', 'Chile', 'China', 'Colombia',
    'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark',
    'Dominica', 'Dominican Republic', 'Ecuador', 'El Salvador', 'Estonia',
    'Fiji', 'Finland', 'France', 'Georgia', 'Germany', 'Greece', 'Grenada',
    'Guatemala', 'Guyana', 'Haiti', 'Honduras', 'Hungary', 'Iceland', 'India',
    'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy', 'Jamaica',
    'Japan', 'Jordan', 'Kazakhstan', 'Kiribati', 'Kuwait', 'Kyrgyzstan',
    'Laos', 'Latvia', 'Lebanon', 'Liechtenstein', 'Lithuania', 'Luxembourg',
    'Malaysia', 'Maldives', 'Malta', 'Marshall Islands', 'Mexico', 'Micronesia',
    'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Myanmar', 'Nauru', 'Nepal',
    'Netherlands', 'New Zealand', 'Nicaragua', 'North Korea', 'North Macedonia',
    'Norway', 'Oman', 'Pakistan', 'Palau', 'Palestine', 'Panama',
    'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal',
    'Qatar', 'Romania', 'Russia', 'Saint Kitts and Nevis', 'Saint Lucia',
    'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Saudi Arabia',
    'Serbia', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands',
    'South Korea', 'Spain', 'Sri Lanka', 'Suriname', 'Sweden', 'Switzerland',
    'Syria', 'Taiwan', 'Tajikistan', 'Thailand', 'Timor-Leste', 'Tonga',
    'Trinidad and Tobago', 'Turkey', 'Turkmenistan', 'Tuvalu',
    'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States',
    'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela',
    'Vietnam', 'Yemen',
];

// Deduplicate and exclude Uganda (handled separately as "Ugandan" / "yes")
const ALL_COUNTRIES = [...new Set([...AFRICAN_COUNTRIES, ...OTHER_COUNTRIES])]
    .filter(c => c !== 'Uganda')
    .sort((a, b) => a.localeCompare(b));

export default ALL_COUNTRIES;
