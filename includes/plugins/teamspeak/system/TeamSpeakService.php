<?php
declare(strict_types=1);

require_once __DIR__ . '/TeamSpeakCache.php';

class TeamSpeakService
{
    private array $cfg;

    public function __construct(array $serverRow)
    {
        $this->cfg = $serverRow;
    }

    public function getServerData(): array
    {
        $serverIp = $this->cfg['host'] ?? '';

        $data = [
            'online'   => false,
            'server'   => [
                'name' => '',
                'used' => 0,
                'max'  => 0,
                'ip'   => $serverIp,
            ],
            'channels' => [],
            'clients'  => []
        ];

        if ((int)$this->cfg['enabled'] !== 1) {
            return $data;
        }

        $host       = $this->cfg['host'];
        $queryPort  = (int)$this->cfg['query_port'];
        $serverPort = (int)$this->cfg['server_port'];
        $user       = $this->cfg['query_user'];
        $pass       = $this->cfg['query_pass'];
        $cacheTime  = max(5, (int)$this->cfg['cache_time']); // 🔒 min. 5s Cache

        /* ========= CACHE ========= */
        $cacheKey = 'ts_' . md5($host . ':' . $queryPort . ':' . $serverPort);
        $cache = new TeamSpeakCache($cacheKey, $cacheTime);

        if ($cached = $cache->get()) {
            return $cached;
        }

        /* ========= CONNECT ========= */
        set_error_handler(static function () {
            return true;
        });

        try {
            $fp = fsockopen($host, $queryPort, $errno, $errstr, 2);
        } finally {
            restore_error_handler();
        }

        if (!$fp || !is_resource($fp)) {
            return $data;
        }

        // 🔒 HARTE TIMEOUTS
        stream_set_timeout($fp, 2);

        $this->readResponse($fp);

        fwrite($fp, "login {$user} {$pass}\n");
        $this->readResponse($fp);

        fwrite($fp, "use port={$serverPort}\n");
        $this->readResponse($fp);

        /* ========= SERVERINFO ========= */
        fwrite($fp, "serverinfo\n");
        $serverInfo = $this->parseLine($this->readResponse($fp));

        /* ========= CHANNELS ========= */
        fwrite($fp, "channellist -icon -flags -voice\n");
        $channels = $this->parseList($this->readResponse($fp));

        /* ========= CLIENTS (LIVE ONLY) ========= */
        fwrite($fp, "clientlist -uid -away -voice -times\n");
        $clients = $this->parseList($this->readResponse($fp));

        fclose($fp);

        /* ========= MAP DATA ========= */
        $data['server']['name'] = $serverInfo['virtualserver_name'] ?? '';
        $data['server']['used'] = (int)($serverInfo['virtualserver_clientsonline'] ?? 0);
        $data['server']['max']  = (int)($serverInfo['virtualserver_maxclients'] ?? 0);
        $data['server']['ip']   = $serverIp;

        $data['channels'] = $channels;
        $data['clients']  = $clients;
        $data['online']   = true;

        $cache->set($data);
        return $data;
    }

    /* ========= HELPER ========= */

private function readResponse($fp): string
{
    $out = '';
    $start = microtime(true);

    while (true) {
        $line = fgets($fp, 4096);
        if ($line === false) {
            break;
        }

        $out .= $line;

        if (strpos($line, 'error id=') !== false) {
            break;
        }

        // 🔴 HARTE OBERGRENZE: max. 500 ms
        if ((microtime(true) - $start) > 0.5) {
            break;
        }
    }

    return trim($out);
}


    private function parseList(string $raw): array
    {
        if (($pos = strpos($raw, 'error id=')) !== false) {
            $raw = substr($raw, 0, $pos);
        }

        $rows = [];
        foreach (explode('|', trim($raw)) as $line) {
            if ($line !== '') {
                $rows[] = $this->parseLine($line);
            }
        }

        return $rows;
    }

    private function parseLine(string $line): array
    {
        $data = [];

        if (($pos = strpos($line, 'error id=')) !== false) {
            $line = substr($line, 0, $pos);
        }

        foreach (explode(' ', trim($line)) as $pair) {
            if (strpos($pair, '=') === false) {
                continue;
            }

            [$k, $v] = explode('=', $pair, 2);
            $v = str_replace('\s', ' ', $v);

            $data[$k] = is_numeric($v) ? (int)$v : $v;
        }

        return $data;
    }
}
