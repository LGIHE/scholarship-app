<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;

class ApplicationsByDistrictChart extends ChartWidget
{
    protected static ?string $heading = 'Applications by Subregion';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_charts');
    }

    /**
     * Maps canonical district names (lowercase) to their Uganda subregion.
     *
     * Subregions used:
     *   Central:  Buganda Central, Buganda North, Buganda East, Buganda West
     *   Eastern:  Busoga, Bukedi, Bugisu, Teso, Sebei, Bugwere
     *   Northern: Acholi, Lango, West Nile, Karamoja
     *   Western:  Ankole, Bunyoro, Rwenzori, Kigezi, Tooro
     */
    private const DISTRICT_TO_SUBREGION = [
        // ── CENTRAL ──────────────────────────────────────────────────────────
        // Buganda Central (Kampala + immediate metro)
        'kampala'         => 'Buganda Central',
        'wakiso'          => 'Buganda Central',
        'mukono'          => 'Buganda Central',
        'mpigi'           => 'Buganda Central',
        'kalangala'       => 'Buganda Central',
        'buvuma'          => 'Buganda Central',

        // Buganda North
        'luweero'         => 'Buganda North',
        'nakaseke'        => 'Buganda North',
        'nakasongola'     => 'Buganda North',
        'kiboga'          => 'Buganda North',
        'kyankwanzi'      => 'Buganda North',
        'mubende'         => 'Buganda North',
        'mityana'         => 'Buganda North',

        // Buganda East / Ssese (Lakes)
        'kayunga'         => 'Buganda East',
        'buikwe'          => 'Buganda East',

        // Buganda West / South (Masaka sub-region)
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
        // Busoga
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

        // Bukedi
        'tororo'          => 'Bukedi',
        'busia'           => 'Bukedi',
        'pallisa'         => 'Bukedi',
        'kibuku'          => 'Bukedi',
        'butebo'          => 'Bukedi',
        'budaka'          => 'Bukedi',
        'butaleja'        => 'Bukedi',

        // Bugisu
        'mbale'           => 'Bugisu',
        'sironko'         => 'Bugisu',
        'bududa'          => 'Bugisu',
        'manafwa'         => 'Bugisu',
        'namisindwa'      => 'Bugisu',
        'bulambuli'       => 'Bugisu',

        // Sebei
        'kapchorwa'       => 'Sebei',
        'kween'           => 'Sebei',
        'bukwa'           => 'Sebei',

        // Teso
        'soroti'          => 'Teso',
        'kumi'            => 'Teso',
        'bukedea'         => 'Teso',
        'ngora'           => 'Teso',
        'serere'          => 'Teso',
        'amuria'          => 'Teso',
        'katakwi'         => 'Teso',
        'kaberamaido'     => 'Teso',

        // ── NORTHERN ─────────────────────────────────────────────────────────
        // Acholi
        'gulu'            => 'Acholi',
        'kitgum'          => 'Acholi',
        'pader'           => 'Acholi',
        'agago'           => 'Acholi',
        'amuru'           => 'Acholi',
        'nwoya'           => 'Acholi',
        'omoro'           => 'Acholi',
        'lamwo'           => 'Acholi',
        'oyam'            => 'Acholi',   // sometimes listed with Lango

        // Lango
        'lira'            => 'Lango',
        'apac'            => 'Lango',
        'kole'            => 'Lango',
        'alebtong'        => 'Lango',
        'amolatar'        => 'Lango',
        'dokolo'          => 'Lango',
        'otuke'           => 'Lango',

        // West Nile
        'arua'            => 'West Nile',
        'koboko'          => 'West Nile',
        'maracha'         => 'West Nile',
        'nebbi'           => 'West Nile',
        'zombo'           => 'West Nile',
        'pakwach'         => 'West Nile',
        'adjumani'        => 'West Nile',
        'moyo'            => 'West Nile',
        'obongi'          => 'West Nile',
        'terego'          => 'West Nile',
        'madi-okollo'     => 'West Nile',

        // Karamoja
        'moroto'          => 'Karamoja',
        'kaabong'         => 'Karamoja',
        'kotido'          => 'Karamoja',
        'napak'           => 'Karamoja',
        'abim'            => 'Karamoja',
        'amudat'          => 'Karamoja',

        // ── WESTERN ──────────────────────────────────────────────────────────
        // Ankole
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

        // Bunyoro
        'hoima'           => 'Bunyoro',
        'masindi'         => 'Bunyoro',
        'buliisa'         => 'Bunyoro',
        'kikuube'         => 'Bunyoro',
        'kiryandongo'     => 'Bunyoro',
        'kagadi'          => 'Bunyoro',
        'kakumiro'        => 'Bunyoro',
        'kibaale'         => 'Bunyoro',

        // Rwenzori
        'kasese'          => 'Rwenzori',
        'bundibugyo'      => 'Rwenzori',
        'ntoroko'         => 'Rwenzori',

        // Kigezi
        'kabale'          => 'Kigezi',
        'kisoro'          => 'Kigezi',
        'kanungu'         => 'Kigezi',
        'rukungiri'       => 'Kigezi',
        'rubanda'         => 'Kigezi',
        'rukiga'          => 'Kigezi',
        'rubirizi'        => 'Kigezi',

        // Tooro
        'kabarole'        => 'Tooro',
        'kamwenge'        => 'Tooro',
        'kyegegwa'        => 'Tooro',
        'kyenjojo'        => 'Tooro',
    ];

    protected function getData(): array
    {
        $grouped = [];

        Application::query()
            ->whereNotNull('personal_info')
            ->whereNotIn('status', ['draft'])
            ->get(['personal_info'])
            ->each(function ($app) use (&$grouped) {
                $raw = trim((string) ($app->personal_info['residence_district'] ?? ''));

                if ($raw === '') {
                    return;
                }

                // Normalise: collapse whitespace, lowercase, strip trailing " district"
                $key = strtolower(preg_replace('/\s+/', ' ', $raw));
                $key = preg_replace('/\s+district$/i', '', $key);

                $subregion = self::DISTRICT_TO_SUBREGION[$key] ?? 'Other';

                $grouped[$subregion] = ($grouped[$subregion] ?? 0) + 1;
            });

        // Sort by count descending, put "Other" last
        arsort($grouped);
        if (isset($grouped['Other'])) {
            $other = $grouped['Other'];
            unset($grouped['Other']);
            $grouped['Other'] = $other;
        }

        $labels = array_keys($grouped);
        $data   = array_values($grouped);

        $palette = [
            'rgb(59, 130, 246)',   // blue
            'rgb(34, 197, 94)',    // green
            'rgb(251, 191, 36)',   // yellow
            'rgb(239, 68, 68)',    // red
            'rgb(168, 85, 247)',   // purple
            'rgb(249, 115, 22)',   // orange
            'rgb(20, 184, 166)',   // teal
            'rgb(236, 72, 153)',   // pink
            'rgb(99, 102, 241)',   // indigo
            'rgb(156, 163, 175)',  // gray
            'rgb(245, 158, 11)',   // amber
            'rgb(16, 185, 129)',   // emerald
            'rgb(139, 92, 246)',   // violet
            'rgb(14, 165, 233)',   // sky
            'rgb(244, 63, 94)',    // rose
            'rgb(107, 114, 128)',  // cool-gray
        ];

        $colours = collect($labels)->map(fn ($_, $i) => $palette[$i % count($palette)])->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Applications',
                    'data'            => $data,
                    'backgroundColor' => $colours,
                ],
            ],
            'labels' => $labels ?: ['No data'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
