<?php

namespace App\Support;

/**
 * Shared district/region lookup used by both dashboard widgets and report exports.
 */
class DistrictHelper
{
    /**
     * Maps canonical district names (lowercase, no trailing "district") to Uganda subregion.
     */
    public const DISTRICT_TO_SUBREGION = [
        // ── CENTRAL ──────────────────────────────────────────────────────────
        'kampala'         => 'Buganda Central',
        'wakiso'          => 'Buganda Central',
        'mukono'          => 'Buganda Central',
        'mpigi'           => 'Buganda Central',
        'kalangala'       => 'Buganda Central',
        'buvuma'          => 'Buganda Central',

        'luweero'         => 'Buganda North',
        'nakaseke'        => 'Buganda North',
        'nakasongola'     => 'Buganda North',
        'kiboga'          => 'Buganda North',
        'kyankwanzi'      => 'Buganda North',
        'mubende'         => 'Buganda North',
        'mityana'         => 'Buganda North',

        'kayunga'         => 'Buganda East',
        'buikwe'          => 'Buganda East',

        'masaka'          => 'Buganda South',
        'kalungu'         => 'Buganda South',
        'bukomansimbi'    => 'Buganda South',
        'gomba'           => 'Buganda South',
        'lwengo'          => 'Buganda South',
        'lyantonde'       => 'Buganda South',
        'rakai'           => 'Buganda South',
        'kyotera'         => 'Buganda South',
        'sembabule'       => 'Buganda South',

        // ── EASTERN ──────────────────────────────────────────────────────────
        'jinja'           => 'Busoga',
        'iganga'          => 'Busoga',
        'kamuli'          => 'Busoga',
        'kaliro'          => 'Busoga',
        'luuka'           => 'Busoga',
        'namutumba'       => 'Busoga',
        'bugweri'         => 'Busoga',
        'buyende'         => 'Busoga',
        'mayuge'          => 'Busoga',
        'namayingo'       => 'Busoga',

        'tororo'          => 'Bukedi',
        'busia'           => 'Bukedi',
        'pallisa'         => 'Bukedi',
        'kibuku'          => 'Bukedi',
        'butebo'          => 'Bukedi',
        'budaka'          => 'Bukedi',
        'butaleja'        => 'Bukedi',

        'mbale'           => 'Bugisu',
        'sironko'         => 'Bugisu',
        'bududa'          => 'Bugisu',
        'manafwa'         => 'Bugisu',
        'namisindwa'      => 'Bugisu',
        'bulambuli'       => 'Bugisu',

        'kapchorwa'       => 'Sebei',
        'kween'           => 'Sebei',
        'bukwa'           => 'Sebei',

        'soroti'          => 'Teso',
        'kumi'            => 'Teso',
        'bukedea'         => 'Teso',
        'ngora'           => 'Teso',
        'serere'          => 'Teso',
        'amuria'          => 'Teso',
        'katakwi'         => 'Teso',
        'kaberamaido'     => 'Teso',

        // ── NORTHERN ─────────────────────────────────────────────────────────
        'gulu'            => 'Acholi',
        'kitgum'          => 'Acholi',
        'pader'           => 'Acholi',
        'agago'           => 'Acholi',
        'amuru'           => 'Acholi',
        'nwoya'           => 'Acholi',
        'omoro'           => 'Acholi',
        'lamwo'           => 'Acholi',
        'oyam'            => 'Acholi',

        'lira'            => 'Lango',
        'apac'            => 'Lango',
        'kole'            => 'Lango',
        'alebtong'        => 'Lango',
        'amolatar'        => 'Lango',
        'dokolo'          => 'Lango',
        'otuke'           => 'Lango',

        'arua'            => 'West Nile',
        'koboko'          => 'West Nile',
        'maracha'         => 'West Nile',
        'nebbi'           => 'West Nile',
        'yumbe'           => 'West Nile',
        'zombo'           => 'West Nile',
        'pakwach'         => 'West Nile',
        'adjumani'        => 'West Nile',
        'moyo'            => 'West Nile',
        'obongi'          => 'West Nile',
        'terego'          => 'West Nile',
        'madi-okollo'     => 'West Nile',

        'moroto'          => 'Karamoja',
        'kaabong'         => 'Karamoja',
        'kotido'          => 'Karamoja',
        'nakapiripirit'   => 'Karamoja',
        'napak'           => 'Karamoja',
        'abim'            => 'Karamoja',
        'nabilatuk'       => 'Karamoja',
        'karenga'         => 'Karamoja',
        'amudat'          => 'Karamoja',

        // ── WESTERN ──────────────────────────────────────────────────────────
        'mbarara'         => 'Ankole',
        'bushenyi'        => 'Ankole',
        'ntungamo'        => 'Ankole',
        'isingiro'        => 'Ankole',
        'ibanda'          => 'Ankole',
        'kiruhura'        => 'Ankole',
        'rwampara'        => 'Ankole',
        'mitooma'         => 'Ankole',
        'sheema'          => 'Ankole',
        'buhweju'         => 'Ankole',

        'hoima'           => 'Bunyoro',
        'masindi'         => 'Bunyoro',
        'buliisa'         => 'Bunyoro',
        'kikuube'         => 'Bunyoro',
        'kiryandongo'     => 'Bunyoro',
        'kagadi'          => 'Bunyoro',
        'kakumiro'        => 'Bunyoro',
        'kibaale'         => 'Bunyoro',

        'kasese'          => 'Rwenzori',
        'bundibugyo'      => 'Rwenzori',
        'ntoroko'         => 'Rwenzori',

        'kabale'          => 'Kigezi',
        'kisoro'          => 'Kigezi',
        'kanungu'         => 'Kigezi',
        'rukungiri'       => 'Kigezi',
        'rubanda'         => 'Kigezi',
        'rukiga'          => 'Kigezi',
        'rubirizi'        => 'Kigezi',

        'kabarole'        => 'Tooro',
        'kamwenge'        => 'Tooro',
        'kyegegwa'        => 'Tooro',
        'kyenjojo'        => 'Tooro',
    ];

    /**
     * Subregion → major region grouping.
     */
    public const SUBREGION_TO_REGION = [
        'Buganda Central' => 'Central',
        'Buganda North'   => 'Central',
        'Buganda East'    => 'Central',
        'Buganda South'   => 'Central',
        'Busoga'          => 'Eastern',
        'Bukedi'          => 'Eastern',
        'Bugisu'          => 'Eastern',
        'Sebei'           => 'Eastern',
        'Teso'            => 'Eastern',
        'Acholi'          => 'Northern',
        'Lango'           => 'Northern',
        'West Nile'       => 'Northern',
        'Karamoja'        => 'Northern',
        'Ankole'          => 'Western',
        'Bunyoro'         => 'Western',
        'Rwenzori'        => 'Western',
        'Kigezi'          => 'Western',
        'Tooro'           => 'Western',
    ];

    /**
     * Normalise a raw district string to its canonical lowercase key.
     */
    public static function normaliseKey(string $raw): string
    {
        $key = strtolower(preg_replace('/\s+/', ' ', trim($raw)));
        return (string) preg_replace('/\s+district$/i', '', $key);
    }

    /**
     * Resolve a raw district string to its subregion name.
     */
    public static function subregion(string $raw): string
    {
        if ($raw === '') return 'Unknown';
        return self::DISTRICT_TO_SUBREGION[self::normaliseKey($raw)] ?? 'Other';
    }

    /**
     * Resolve a subregion to its major region (Central / Eastern / Northern / Western).
     */
    public static function region(string $subregion): string
    {
        return self::SUBREGION_TO_REGION[$subregion] ?? 'Other';
    }
}
