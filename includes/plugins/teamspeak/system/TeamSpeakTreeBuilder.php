<?php
declare(strict_types=1);

class TeamSpeakTreeBuilder
{
    public static function build(array $channels, array $clients): array
    {
        $map = [];

        /* ==========================
           1) CHANNELS NORMALISIEREN
        ========================== */
        foreach ($channels as $ch) {

            $data = is_string($ch)
                ? self::parseKeyValueString($ch)
                : (array)$ch;

            if (!isset($data['cid'])) {
                continue;
            }

$channelName = self::decodeTsString(
    $data['channel_name'] ?? $data['name'] ?? ''
);

/* cspacer-Tag entfernen */
$isTextChannel = false;

if (preg_match('/^\s*\[(?:\*|c|l|r|-)?spacer[^\]]*\]\s*/i', $channelName)) {
    $channelName = preg_replace(
        '/^\s*\[(?:\*|c|l|r|-)?spacer[^\]]*\]\s*/i',
        '',
        $channelName
    );
    $isTextChannel = true;
}

if (trim($channelName) === '') {
    continue;
}









            $cid = (int)$data['cid'];

$map[$cid] = [
    'cid'          => $cid,
    'pid'          => (int)($data['pid'] ?? 0),
    'name'         => $channelName,
    'is_text'      => $isTextChannel,   // 👈 von vorher
    'clients'      => [],
    'client_count' => 0,                // 🔥 FEHLTE → FIX
    'children'     => [],
    'locked'       => (
        !empty($data['channel_flag_password']) ||
        ((int)($data['channel_needed_join_power'] ?? 0) > 0)
    ),
    'default'      => !empty($data['channel_flag_default']),
];


        }

        /* ==========================
           2) CLIENTS ZUORDNEN
        ========================== */
        foreach ($clients as $cl) {

            $data = is_string($cl)
                ? self::parseKeyValueString($cl)
                : (array)$cl;

            // 🔴 nur echte User (keine ServerQuery / Bots)
            if ((int)($data['client_type'] ?? 1) !== 0) {
                continue;
            }

            $cid = (int)($data['cid'] ?? 0);
            if (!isset($map[$cid])) {
                continue;
            }

            $groups = [];
            if (!empty($data['client_servergroups'])) {
                $groups = array_map('trim', explode(',', (string)$data['client_servergroups']));
            }

            $country = strtolower((string)($data['client_country'] ?? ''));
            if (!preg_match('/^[a-z]{2}$/', $country)) {
                $country = '';
            }

            $map[$cid]['clients'][] = [
                'nickname' => self::decodeTsString($data['client_nickname'] ?? 'Unbekannt'),

                'away'                => !empty($data['client_away']),
                'client_flag_talking' => !empty($data['client_flag_talking']),
                'client_input_muted'  => !empty($data['client_input_muted']),
                'client_output_muted' => !empty($data['client_output_muted']),

                'country'  => $country,
                'is_admin' => in_array('6', $groups, true),

                'idle_time' => (int)($data['client_idle_time'] ?? 0),
                'created'   => (int)($data['client_created'] ?? 0),
                'lastconn'  => (int)($data['client_lastconnected'] ?? 0),
            ];

            $map[$cid]['client_count']++;
        }

        /* ==========================
           3) BAUM AUFBAUEN
        ========================== */
        $tree = [];

        foreach ($map as $cid => &$channel) {
            if ($channel['pid'] === 0) {
                $tree[] = &$channel;
            } elseif (isset($map[$channel['pid']])) {
                $map[$channel['pid']]['children'][] = &$channel;
            }
        }

        return $tree;
    }

    /* ==========================
       HILFSFUNKTIONEN
    ========================== */

    private static function parseKeyValueString(string $input): array
    {
        preg_match_all('/(\w+)=(".*?"|\S+)/', $input, $matches, PREG_SET_ORDER);

        $out = [];
        foreach ($matches as $set) {
            $out[$set[1]] = trim($set[2], '"');
        }

        return $out;
    }

    private static function decodeTsString(string $value): string
    {
        return str_replace(
            ['\\s', '\\p', '\\/', '\\\\'],
            [' ', '|', '/', '\\'],
            $value
        );
    }
}
