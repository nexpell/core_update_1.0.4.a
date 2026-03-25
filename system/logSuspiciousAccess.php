<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', '0');

if (!defined('NX_SUSPICIOUS_BLOCK_THRESHOLD')) {
    define('NX_SUSPICIOUS_BLOCK_THRESHOLD', 3);
}

if (!defined('NX_SUSPICIOUS_BLOCK_WINDOW')) {
    define('NX_SUSPICIOUS_BLOCK_WINDOW', 1800);
}

if (!defined('NX_SUSPICIOUS_BLOCK_DURATION')) {
    define('NX_SUSPICIOUS_BLOCK_DURATION', 3600);
}

function nx_get_client_ip(): string
{
    $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
    return $ip !== '' ? $ip : 'unknown';
}

function anonymize_ip(string $ip): string
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $parts = explode('.', $ip);
        if (count($parts) === 4) {
            $parts[3] = '0';
            return implode('.', $parts);
        }
    }

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        $parts = explode(':', $ip);
        $count = count($parts);
        if ($count >= 2) {
            for ($i = max(0, $count - 2); $i < $count; $i++) {
                $parts[$i] = '0000';
            }
            return implode(':', $parts);
        }
    }

    return $ip;
}

function maskSensitiveData(array $data): array
{
    foreach ($data as $key => &$value) {
        if (is_array($value)) {
            $value = maskSensitiveData($value);
            continue;
        }

        $keyString = strtolower((string)$key);
        if (preg_match('/pass|password|token|csrf|nonce|secret|hash/', $keyString)) {
            $value = '[HIDDEN]';
        }
    }

    return $data;
}

function nx_security_log_dir(): string
{
    $logDir = __DIR__ . '/../admin/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    return $logDir;
}

function nx_append_log(string $filename, string $content): void
{
    $path = nx_security_log_dir() . '/' . $filename;
    file_put_contents($path, $content, FILE_APPEND | LOCK_EX);
}

function logSuspiciousAccess(string $reason = '', array $details = []): void
{
    $realIp = nx_get_client_ip();
    $maskedGet = maskSensitiveData($_GET);
    $maskedPost = maskSensitiveData($_POST);
    $maskedDetails = maskSensitiveData($details);

    $logEntry  = date('Y-m-d H:i:s') . ' - Grund: ' . $reason . ' - IP: ' . anonymize_ip($realIp) . PHP_EOL;
    $logEntry .= 'URL: ' . (string)($_SERVER['REQUEST_URI'] ?? 'unknown') . PHP_EOL;
    $logEntry .= 'User Agent: ' . (string)($_SERVER['HTTP_USER_AGENT'] ?? 'unknown') . PHP_EOL;
    $logEntry .= 'GET: ' . json_encode($maskedGet, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    $logEntry .= 'POST: ' . json_encode($maskedPost, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

    if ($maskedDetails !== []) {
        $logEntry .= 'Details: ' . json_encode($maskedDetails, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }

    $logEntry .= str_repeat('-', 40) . PHP_EOL;

    nx_append_log('suspicious_access.log', $logEntry);
}

function nx_is_whitelisted_security_field(string $key): bool
{
    static $whitelist = [
        'message', 'post_text', 'comment', 'content', 'body', 'description',
        'csrf_token', 'token', 'site', 'action', 'search', 'q',
        'title', 'headline', 'text', 'html', 'intro', 'summary'
    ];

    $key = strtolower($key);
    if (in_array($key, $whitelist, true)) {
        return true;
    }

    return (bool)preg_match('/token|hash|nonce|password|pass|csrf/', $key);
}

function detectSuspiciousInput(array $input, string $path = ''): ?array
{
    $patterns = [
        'sql_union' => '/\bunion\b\s+\bselect\b/i',
        'sql_drop' => '/\bdrop\b\s+\b(table|database)\b/i',
        'sql_delete' => '/\bdelete\b\s+\bfrom\b/i',
        'sql_insert' => '/\binsert\b\s+\binto\b/i',
        'sql_update' => '/\bupdate\b\s+[a-z0-9_`]+\s+\bset\b/i',
        'sqli_comment_tail' => '/(?:--|#)\s*$/',
        'xss_script' => '/<\s*script\b/i',
        'xss_handler' => '/on(?:error|load|click|mouseover|focus|submit)\s*=/i',
        'php_wrapper' => '/(?:php:\/\/|data:text\/html|expect:\/\/|file:\/\/)/i',
        'path_traversal' => '/(?:\.\.\/|\.\.\\\\)/',
    ];

    foreach ($input as $key => $value) {
        $keyString = (string)$key;
        $currentPath = $path === '' ? $keyString : ($path . '.' . $keyString);

        if (nx_is_whitelisted_security_field($keyString)) {
            continue;
        }

        if (is_array($value)) {
            $nested = detectSuspiciousInput($value, $currentPath);
            if ($nested !== null) {
                return $nested;
            }
            continue;
        }

        $stringValue = trim((string)$value);
        if ($stringValue === '') {
            continue;
        }

        foreach ($patterns as $reason => $pattern) {
            if (preg_match($pattern, $stringValue)) {
                return [
                    'param' => $currentPath,
                    'value' => mb_substr($stringValue, 0, 300),
                    'reason' => $reason,
                ];
            }
        }
    }

    return null;
}

function nx_block_file_path(): string
{
    return nx_security_log_dir() . '/blocked_ips.json';
}

function nx_attempts_file_path(): string
{
    return nx_security_log_dir() . '/suspicious_attempts.json';
}

function nx_load_json_file(string $file): array
{
    if (!is_file($file)) {
        return [];
    }

    $raw = file_get_contents($file);
    if (!is_string($raw) || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function nx_store_json_file(string $file, array $data): void
{
    file_put_contents($file, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function nx_cleanup_blocks(array $blocked, int $now): array
{
    return array_values(array_filter($blocked, static function ($entry) use ($now) {
        return isset($entry['until']) && (int)$entry['until'] > $now;
    }));
}

function nx_cleanup_attempts(array $attempts, int $now): array
{
    return array_values(array_filter($attempts, static function ($entry) use ($now) {
        return isset($entry['created_at']) && ((int)$entry['created_at'] + NX_SUSPICIOUS_BLOCK_WINDOW) > $now;
    }));
}

function nx_is_ip_blocked(string $realIp, array $blocked): ?array
{
    foreach ($blocked as $entry) {
        if (($entry['ip'] ?? '') === $realIp) {
            return $entry;
        }
    }

    return null;
}

function blockIP(string $realIp, string $reason = '', string $level = 'warning', int $duration = NX_SUSPICIOUS_BLOCK_DURATION, array $details = []): void
{
    $blockfile = nx_block_file_path();
    $blocked = nx_cleanup_blocks(nx_load_json_file($blockfile), time());

    foreach ($blocked as $entry) {
        if (($entry['ip'] ?? '') === $realIp) {
            return;
        }
    }

    $entry = [
        'ip' => $realIp,
        'reason' => $reason,
        'level' => $level,
        'date' => date('Y-m-d H:i:s'),
        'until' => time() + $duration,
    ];

    $blocked[] = $entry;
    nx_store_json_file($blockfile, $blocked);

    $extra = '';
    if ($details !== []) {
        $extra = ' | Details: ' . json_encode(maskSensitiveData($details), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    $logEntry = date('Y-m-d H:i:s')
        . ' - IP blocked: ' . anonymize_ip($realIp)
        . ', reason: ' . $reason
        . ', level: ' . $level
        . ', until: ' . date('Y-m-d H:i:s', $entry['until'])
        . $extra
        . PHP_EOL;

    nx_append_log('block_log.txt', $logEntry);
}

function nx_register_suspicious_attempt(string $realIp, string $reason, array $details = []): int
{
    $attemptFile = nx_attempts_file_path();
    $now = time();
    $attempts = nx_cleanup_attempts(nx_load_json_file($attemptFile), $now);

    $attempts[] = [
        'ip' => $realIp,
        'reason' => $reason,
        'details' => maskSensitiveData($details),
        'created_at' => $now,
    ];

    nx_store_json_file($attemptFile, $attempts);

    $count = 0;
    foreach ($attempts as $attempt) {
        if (($attempt['ip'] ?? '') === $realIp) {
            $count++;
        }
    }

    return $count;
}

$realIp = nx_get_client_ip();
$now = time();

$blocked = nx_cleanup_blocks(nx_load_json_file(nx_block_file_path()), $now);
nx_store_json_file(nx_block_file_path(), $blocked);

$activeBlock = nx_is_ip_blocked($realIp, $blocked);
if ($activeBlock !== null) {
    $entry = date('Y-m-d H:i:s')
        . ' - Blocked access from IP: ' . anonymize_ip($realIp)
        . ', reason: ' . (string)($activeBlock['reason'] ?? 'unknown')
        . ', level: ' . (string)($activeBlock['level'] ?? 'warning')
        . PHP_EOL;
    nx_append_log('block_log.txt', $entry);
    http_response_code(403);
    exit;
}

foreach (['GET' => $_GET, 'POST' => $_POST] as $method => $data) {
    $suspicious = detectSuspiciousInput(is_array($data) ? $data : []);
    if ($suspicious === null) {
        continue;
    }

    $reason = 'Verdächtige Eingabe in ' . $method;
    logSuspiciousAccess($reason, $suspicious);

    $attemptCount = nx_register_suspicious_attempt($realIp, $reason, $suspicious);
    if ($attemptCount >= NX_SUSPICIOUS_BLOCK_THRESHOLD) {
        blockIP($realIp, $reason, 'critical', NX_SUSPICIOUS_BLOCK_DURATION, $suspicious);
    }

    http_response_code(403);
    exit;
}
