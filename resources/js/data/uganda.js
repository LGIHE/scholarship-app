/**
 * Uganda administrative regions and their districts.
 * Source: Uganda Bureau of Statistics / Ministry of Local Government.
 */
export const UGANDA_REGIONS = [
    'Central',
    'Eastern',
    'Northern',
    'Western',
];

export const DISTRICTS_BY_REGION = {
    Central: [
        'Buikwe', 'Bukomansimbi', 'Butambala', 'Buvuma', 'Gomba',
        'Kalangala', 'Kalungu', 'Kampala', 'Kassanda', 'Kayunga', 'Kiboga',
        'Kyankwanzi', 'Kyotera', 'Luweero', 'Lwengo', 'Lyantonde',
        'Masaka', 'Mityana', 'Mpigi', 'Mubende', 'Mukono',
        'Nakaseke', 'Nakasongola', 'Rakai', 'Sembabule', 'Wakiso',
    ],
    Eastern: [
        'Amuria', 'Budaka', 'Bududa', 'Bugiri', 'Bugweri',
        'Bukedea', 'Bukwa', 'Bulambuli', 'Busia', 'Butebo',
        'Buyende', 'Iganga', 'Jinja', 'Kaberamaido', 'Kaliro',
        'Kamuli', 'Kapchorwa', 'Kapelebyong', 'Katakwi', 'Kibuku',
        'Kumi', 'Kween', 'Luuka', 'Manafwa', 'Mayuge', 'Mbale',
        'Namayingo', 'Namisindwa', 'Namutumba', 'Ngora', 'Pallisa',
        'Serere', 'Sironko', 'Soroti', 'Tororo',
    ],
    Northern: [
        'Abim', 'Adjumani', 'Agago', 'Alebtong', 'Amolatar',
        'Amudat', 'Amuru', 'Apac', 'Arua', 'Dokolo',
        'Gulu', 'Kaabong', 'Karenga', 'Kitgum', 'Koboko', 'Kole',
        'Kotido', 'Kwania', 'Lamwo', 'Lira', 'Madi-Okollo', 'Maracha',
        'Moroto', 'Moyo', 'Nabilatuk', 'Nakapiripirit', 'Napak', 'Nebbi',
        'Nwoya', 'Obongi', 'Omoro', 'Otuke', 'Oyam', 'Pader',
        'Pakwach', 'Terego', 'Yumbe', 'Zombo',
    ],
    Western: [
        'Buhweju', 'Buliisa', 'Bundibugyo', 'Bushenyi',
        'Hoima', 'Ibanda', 'Isingiro', 'Kabale', 'Kabarole',
        'Kagadi', 'Kakumiro', 'Kamwenge', 'Kanungu', 'Kasese',
        'Kazo', 'Kibaale', 'Kikuube', 'Kiruhura', 'Kiryandongo',
        'Kisoro', 'Kitagwenda', 'Kyegegwa', 'Kyenjojo', 'Masindi',
        'Mbarara', 'Mitooma', 'Ntoroko', 'Ntungamo', 'Rubanda',
        'Rubirizi', 'Rukiga', 'Rukungiri', 'Rwampara', 'Sheema',
    ],
};

/** Flat sorted list of all Uganda districts (for normalisation lookups). */
export const ALL_UGANDA_DISTRICTS = [
    ...new Set(Object.values(DISTRICTS_BY_REGION).flat()),
].sort((a, b) => a.localeCompare(b));

/**
 * Maps common misspellings / partial names to the canonical district name.
 * Used in data-cleanup scripts and validation helpers.
 */
export const DISTRICT_ALIASES = {
    // Central
    'kampala city': 'Kampala',
    'kla': 'Kampala',
    'kasanda': 'Kassanda',
    'kassanda district': 'Kassanda',
    'kasanda district': 'Kassanda',
    'wakiso district': 'Wakiso',
    'entebbe': 'Wakiso',
    'muyenga': 'Kampala',
    'ntinda': 'Kampala',
    'mukono district': 'Mukono',
    'masaka district': 'Masaka',
    'mubende district': 'Mubende',
    'mityana district': 'Mityana',
    'luwero': 'Luweero',
    'luweero district': 'Luweero',
    'mpigi district': 'Mpigi',
    'kayunga district': 'Kayunga',
    'rakai district': 'Rakai',
    // Eastern
    'mbale district': 'Mbale',
    'jinja district': 'Jinja',
    'tororo district': 'Tororo',
    'iganga district': 'Iganga',
    'busia district': 'Busia',
    'soroti district': 'Soroti',
    'kumi district': 'Kumi',
    'kamuli district': 'Kamuli',
    'pallisa district': 'Pallisa',
    'sironko district': 'Sironko',
    'bugiri district': 'Bugiri',
    'bududa district': 'Bududa',
    'manafwa district': 'Manafwa',
    // Northern
    'gulu district': 'Gulu',
    'lira district': 'Lira',
    'arua district': 'Arua',
    'moroto district': 'Moroto',
    'kitgum district': 'Kitgum',
    'apac district': 'Apac',
    'pader district': 'Pader',
    'kotido district': 'Kotido',
    'adjumani district': 'Adjumani',
    'moyo district': 'Moyo',
    'nebbi district': 'Nebbi',
    'zombo district': 'Zombo',
    'yumbe district': 'Yumbe',
    'yumbe': 'Yumbe',
    'kwania district': 'Kwania',
    'nabilatuk district': 'Nabilatuk',
    'nakapiripirit': 'Nakapiripirit',
    'nakapiripirit district': 'Nakapiripirit',
    'karenga district': 'Karenga',
    'madi okollo': 'Madi-Okollo',
    'madi-okollo district': 'Madi-Okollo',
    // Western
    'mbarara district': 'Mbarara',
    'kasese district': 'Kasese',
    'kabale district': 'Kabale',
    'hoima district': 'Hoima',
    'masindi district': 'Masindi',
    'bushenyi district': 'Bushenyi',
    'ntungamo district': 'Ntungamo',
    'rukungiri district': 'Rukungiri',
    'kanungu district': 'Kanungu',
    'kabarole district': 'Kabarole',
    'kamwenge district': 'Kamwenge',
    'kyenjojo district': 'Kyenjojo',
    'bundibugyo district': 'Bundibugyo',
    'kisoro district': 'Kisoro',
    'ibanda district': 'Ibanda',
    'isingiro district': 'Isingiro',
    'kiruhura district': 'Kiruhura',
    'kiryandongo district': 'Kiryandongo',
    'buliisa district': 'Buliisa',
    'kazo district': 'Kazo',
    'kitagwenda district': 'Kitagwenda',
    'kitagwenda': 'Kitagwenda',
};
