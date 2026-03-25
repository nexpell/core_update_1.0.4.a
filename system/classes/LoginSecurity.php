<?php

namespace nexpell;

use nexpell\Email;

class LoginSecurity 
{


    // Key dynamisch aus Konstante holen
    private static function getAesKey(): string
    {
        if (!defined('AES_KEY') || strlen(AES_KEY) !== 32) {
            throw new RuntimeException('AES_KEY ist nicht definiert oder hat nicht die korrekte Länge von 32 Zeichen.');
        }
        return AES_KEY;
    }

    public static function encryptPepper(string $plain_pepper): ?string
    {
        $key = self::getAesKey();
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($iv_length);
        $encrypted = openssl_encrypt($plain_pepper, 'aes-256-cbc', $key, 0, $iv);
        if ($encrypted === false) {
            return null;
        }
        return base64_encode($iv . $encrypted);
    }

    public static function decryptPepper(string $encrypted_pepper): ?string
    {
        $key = self::getAesKey();
        $data = base64_decode($encrypted_pepper);
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $iv_length);
        $ciphertext = substr($data, $iv_length);
        return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);
    }

    public static function createPasswordHash(string $password_hash, string $email, string $pepper): string {
        return password_hash($password_hash . $email . $pepper, PASSWORD_DEFAULT);
    }

    public static function verifyPassword(string $password_hash, string $email, string $pepper, string $hash): bool {
        return password_verify($password_hash . $email . $pepper, $hash);
    }

    // Methode zum Generieren eines lesbaren Passworts
    public static function generateReadablePassword(int $length = 10): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'; // gut lesbare Zeichen
        $password_hash = '';

        for ($i = 0; $i < $length; $i++) {
            $password_hash .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password_hash;
    }
    

    public static function verifyLogin($email, $password_hash, $ip, $is_active , $banned): array
{
    // Zuerst prüfen, ob IP gesperrt ist
    $isIpBanned = self::isIpBanned($ip); // IP-Überprüfung
    if ($isIpBanned) {
        return [
            'success'   => false,
            'ip_banned' => true,
            'error'     => 'Deine IP-Adresse wurde gesperrt.'
        ];
    }

    // Benutzer aus der Datenbank abrufen
    $query = "SELECT * FROM `users` WHERE `email` = '" . self::escape($email) . "'";
    $result = safe_query($query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_array($result);

        // Überprüfen, ob das Konto aktiv ist
        if ($user['is_active'] == 0) {
            return [
                'success'   => false,
                'ip_banned' => false,
                'error'     => 'Dein Konto wurde noch nicht aktiviert. Bitte überprüfe deine E-Mail.'
            ];
        }

        // Überprüfen, ob der User gebannt ist
        if (isset($user['banned']) && $user['banned'] == 1) {
            return [
                'success'   => false,
                'ip_banned' => false,
                'error'     => 'Dein Konto wurde gesperrt. Bitte überprüfe deine E-Mail.'
            ];
        }

        // Entschlüsseln des Peppers
        $pepper_plain = self::decryptPepper($user['password_pepper']);
        if (!$pepper_plain) {
            return [
                'success'   => false,
                'ip_banned' => false,
                'error'     => 'Fehler beim Entschlüsseln des Peppers.'
            ];
        }

        // Passwort prüfen
        if (password_verify($password_hash . $email . $pepper_plain, $user['password_hash'])) {
            // ✅ PATCH: userID mit zurückgeben
            return [
                'success'   => true,
                'ip_banned' => false,
                'userID'    => (int)$user['userID']
            ];
        } else {
            return [
                'success'   => false,
                'ip_banned' => false,
                'error'     => 'Ungültige E-Mail-Adresse oder Passwort.'
            ];
        }

    } else {
        return [
            'success'   => false,
            'ip_banned' => false,
            'error'     => 'Ungültige E-Mail-Adresse oder Passwort.'
        ];
    }
}




    public static function handleLoginError(array $loginResult, int $failCount, string $ip, ?int $userID, string $email): array
    {
        $isIpBanned = false;
        $message_zusatz = '';

        // Konto nicht aktiviert
        if (str_contains($loginResult['error'], 'noch nicht aktiviert')) {
            $isIpBanned = true;
            $message_zusatz .= '<div class="alert alert-warning" role="alert">Dein Konto wurde noch nicht aktiviert. Bitte überprüfe deine E-Mail.</div>';
        }

        // Benutzer gebannt
        elseif (str_contains($loginResult['error'], 'gebannt')) {
            $isIpBanned = true;
            $message_zusatz .= '<div class="alert alert-danger" role="alert">Dein Konto wurde gesperrt. Bitte kontaktiere den Support.</div>';
        }

        // Fehlversuche prüfen
        else {
            if ($failCount >= 5) {
                self::banIp($ip, $userID, "Zu viele Fehlversuche", $email);
                $message_zusatz .= '<div class="alert alert-danger" role="alert">Zu viele Fehlversuche – Deine IP wurde gesperrt.</div>';
                $isIpBanned = true;
            } else {
                $message_zusatz .= '<div class="alert alert-danger" role="alert">Versuche: ' . $failCount . ' / 5</div>';
            }
        }

        return [
            'isIpBanned' => $isIpBanned,
            'message_zusatz' => $message_zusatz
        ];
    }

    public static function logFailedLogin(int $userID, string $ip, string $reason, ?string $email = null): void
    {
        global $_database;

        $stmt = $_database->prepare("
            INSERT INTO failed_login_attempts (userID, ip, attempt_time, status, reason, email)
            VALUES (?, ?, NOW(), 'failed', ?, ?)
        ");
        $stmt->bind_param("isss", $userID, $ip, $reason, $email);
        $stmt->execute();
        $stmt->close();
    }

    public static function isEmailBanned(string $email, string $ip): bool
    {
        global $_database;

        // Zuerst fehlgeschlagene Login-Versuche für diese IP löschen
        $query = "DELETE FROM failed_login_attempts WHERE ip = '" . self::escape($ip) . "'";
        safe_query($query);

        $stmt = $_database->prepare("SELECT 1 FROM banned_ips WHERE email = ? AND (deltime IS NULL OR deltime > NOW())");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res && $res->num_rows > 0;
    }

    public static function isEmailOrIpBanned(string $email, string $ip): bool
    {
        global $_database;

        $stmt = $_database->prepare("SELECT 1 FROM banned_ips WHERE (email = ? OR ip = ?) AND (deltime IS NULL OR deltime > NOW())");
        $stmt->bind_param("ss", $email, $ip);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res && $res->num_rows > 0;
    }


    // Fehlversuche löschen
    public static function clearFailedAttempts(string $ip): void {
        safe_query("DELETE FROM failed_login_attempts WHERE ip = '" . escape($ip) . "'");
    }

    // Generierung eines zufälligen Peppers
    public static function generatePepper(int $length = 16): string {
        if (!is_int($length) || $length <= 0) {
            throw new \InvalidArgumentException('Länge des Peppers muss eine positive Ganzzahl sein.');
        }

        return bin2hex(random_bytes($length)); // Hexadezimale Darstellung eines zufälligen Bytes
    }

    // Passwort zurücksetzen und neuen Pepper und Hash speichern
    public static function resetPassword(int $userID, string $newPassword): void {
        // Neuer Pepper wird generiert
        $pepper = self::generatePepper();
        // Neues Passwort wird gehasht mit password_hash()
        $newPasswordHash = password_hash($newPassword . $pepper, PASSWORD_BCRYPT);

        // Passwort-Hash und Pepper in der Datenbank aktualisieren
        safe_query("
            UPDATE users
            SET password_hash = '" . escape($newPasswordHash) . "', password_pepper = '" . escape($pepper) . "'
            WHERE userID = " . intval($userID)
        );
    }

    public static function getFailCount(string $ip, string $email): int
    {
        global $_database;

        // Zähle die fehlgeschlagenen Login-Versuche für eine bestimmte IP und E-Mail
        $stmt = $_database->prepare("SELECT COUNT(*) FROM failed_login_attempts WHERE ip = ? AND email = ? AND status = 'failed' AND attempt_time > NOW() - INTERVAL 24 HOUR");
        $stmt->bind_param("ss", $ip, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return (int) $row['COUNT(*)'];
    }

    // Funktion zum Verfolgen eines fehlgeschlagenen Logins
    public static function trackFailedLogin(?int $userID, string $email, string $ip): void
    {
        global $_database;

        // Benutzer-ID ermitteln, falls nicht vorhanden
        if (is_null($userID)) {
            $stmt = $_database->prepare("SELECT userID FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $row = $result->fetch_assoc()) {
                $userID = (int)$row['userID'];
            } else {
                $userID = 0; // Wenn Benutzer nicht gefunden wurde
            }
            $stmt->close();
        }

        $reason = "Login fehlgeschlagen";
        $status = "failed";

        $stmt = $_database->prepare("INSERT INTO failed_login_attempts (userID, email, ip, status, reason) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userID, $email, $ip, $status, $reason);
        $stmt->execute();
        $stmt->close();
    }


    // Funktion zum Sperren der IP
    public static function banIp($ip, $userID, $reason = "", $email = "") {
        global $_database;

        // Zuerst fehlgeschlagene Login-Versuche für diese IP löschen
        $query = "DELETE FROM `failed_login_attempts` WHERE `ip` = '" . self::escape($ip) . "'";
        safe_query($query);

        // Bannzeit auf 3 Stunden setzen
        $banTime = date('Y-m-d H:i:s', strtotime('+3 hours'));  // Sperre für 3 Stunden

        // SQL-Abfrage zum Sperren der IP
        $query = "INSERT INTO `banned_ips` (ip, userID, reason, email, deltime) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $_database->prepare($query)) {
            // Benutzung des richtigen Typs für die Parameter
            $stmt->bind_param("sisss", $ip, $userID, $reason, $email, $banTime);

            if ($stmt->execute()) {
                // Erfolgreich gespeichert
                return true;
            } else {
                // Fehler bei der Ausführung der SQL-Abfrage
                echo "Fehler beim Ausführen der Abfrage: " . $stmt->error;
                return false;
            }
        } else {
            // Fehler beim Vorbereiten der Abfrage
            echo "Fehler beim Vorbereiten der Abfrage: " . $_database->error;
            return false;
        }
    }


    public static function isIpAlreadyBanned($ip): bool {
        $query = "SELECT 1 FROM `banned_ips` WHERE `ip` = '" . self::escape($ip) . "' LIMIT 1";
        $result = safe_query($query);
        return mysqli_num_rows($result) > 0;
    }

    
    private static function anonymize_ip(string $ip): string {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            $parts[3] = '0';
            return implode('.', $parts);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            $parts[count($parts) - 1] = '0000';
            return implode(':', $parts);
        }

        return $ip;
    }

    // Funktion zum Speichern der Session nach erfolgreichem Login
    public static function saveSession(int $userID): void {
        global $_database;

        $sessionID   = session_id();
        $userIP      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userIP      = self::anonymize_ip($userIP); // IP anonymisieren
        $browser     = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $sessionData = serialize($_SESSION); // optional
        $lastActivity = time();

        // Prüfen, ob bereits eine Session mit dieser ID existiert
        $checkStmt = $_database->prepare("SELECT id FROM user_sessions WHERE session_id = ?");
        $checkStmt->bind_param('s', $sessionID);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Update bestehender Session
            $updateStmt = $_database->prepare("
                UPDATE user_sessions 
                SET userID = ?, user_ip = ?, session_data = ?, browser = ?, last_activity = ?
                WHERE session_id = ?
            ");
            $updateStmt->bind_param('isssis', $userID, $userIP, $sessionData, $browser, $lastActivity, $sessionID);
            $updateStmt->execute();
        } else {
            // Neue Session speichern
            $insertStmt = $_database->prepare("
                INSERT INTO user_sessions (session_id, userID, user_ip, session_data, browser, last_activity)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $insertStmt->bind_param('sisssi', $sessionID, $userID, $userIP, $sessionData, $browser, $lastActivity);
            $insertStmt->execute();
        }

        // ==========================
        // Alte Sessions löschen (> 30 Tage)
        // ==========================
        $deleteOldStmt = $_database->prepare("
            DELETE FROM user_sessions
            WHERE last_activity < ?
        ");
        $threshold = time() - (30 * 24 * 60 * 60); // 30 Tage
        $deleteOldStmt->bind_param('i', $threshold);
        $deleteOldStmt->execute();
    }

    // Funktion zur Überprüfung, ob die IP-Adresse des Nutzers gesperrt ist
    public static function isIpBanned(string $ip, ?string $email = null): bool {
        global $_database;

        $stmt = $_database->prepare("SELECT COUNT(*) FROM banned_ips WHERE ip = ? OR email = ?");
        $stmt->bind_param('ss', $ip, $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        return ($count > 0);
    }

    public static function cleanupExpiredBans() {
        global $_database;

        // Bereinigen von Banns, die abgelaufen sind
        $now = date('Y-m-d H:i:s');
        $_database->query("DELETE FROM banned_ips WHERE deltime <= '$now'");
    }

    public static function tooManyFailedAttempts(int $userID, string $ip, int $max = 5): bool
    {
        global $_database;

        $stmt = $_database->prepare("
            SELECT COUNT(*) FROM failed_login_attempts
            WHERE userID = ? AND ip = ? AND attempt_time > (NOW() - INTERVAL 15 MINUTE)
        ");
        $stmt->bind_param('is', $userID, $ip);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        return ($count >= $max);
    }

    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    public static function generateRandomPepper($length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    public static function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Erzeugt ein zufälliges Token
        }
        return $_SESSION['csrf_token'];
    }

    // ==== BEGIN: 2FA + Remember + Mailer (alles in LoginSecurity) ====

/** 2FA-Konfig */
private const TWOFA_CODE_LEN    = 6;
public const TWOFA_TTL_MIN      = 10;
private const TWOFA_RESEND_COOL = 30; // Sekunden
private const TWOFA_MAX_FAILS   = 5;
private const TWOFA_LOCK_MIN    = 15;

/** Remember-Device-Konfig */
public const RDV_COOKIE = 'rdv';
private const RDV_SAMESITE = 'Lax';

public static function cookieName(): string {
    return self::RDV_COOKIE;
}

/** PHPMailer einbinden + konfigurieren */
private static function makeMailer(): \PHPMailer\PHPMailer\PHPMailer
{
    // Klassen laden (v6-Struktur)
    if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
        require_once __DIR__ . '/../../components/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../../components/PHPMailer/src/SMTP.php';
        require_once __DIR__ . '/../../components/PHPMailer/src/Exception.php';
    }

    $m = new \PHPMailer\PHPMailer\PHPMailer(true);

    // === DEV-DEBUG: ausführliches SMTP-Log ins PHP-Errorlog ===
    if (getenv('SMTP_DEBUG') === '1') {
        $m->SMTPDebug  = 2;
        $m->Debugoutput = static function($s){ error_log('[SMTP] '.$s); };
    }

    // === ENV zwingend prüfen (klare Fehlermeldung statt Silent-Fail) ===
    $env = static function(string $k, bool $required = true, $default = null) {
        $v = getenv($k);
        if ($v === false || $v === '') {
            if ($required) {
                throw new \RuntimeException("Env '$k' fehlt/leer");
            }
            return $default;
        }
        return $v;
    };

    $host = (string)$env('SMTP_HOST');
    $port = (int)($env('SMTP_PORT', false, 587));
    $user = (string)$env('SMTP_USER');
    $pass = (string)$env('SMTP_PASS');
    $from = (string)$env('SMTP_FROM_EMAIL', false, $user);
    $name = (string)$env('SMTP_FROM_NAME', false, 'nexpell');

    $m->isSMTP();
    $m->Host       = $host;
    $m->Port       = $port;
    $m->SMTPAuth   = true;
    $m->Username   = $user;
    $m->Password   = $pass;
    $m->CharSet    = 'UTF-8';

    // === Encryption passend zum Port wählen ===
    if ($port === 465) {
        $m->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS; // Implicit TLS
    } else {
        $m->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS (z.B. 587)
    }

    // DEV (Self-Signed) – nur lokal verwenden!
    if (getenv('SMTP_ALLOW_SELF_SIGNED') === '1') {
        $m->SMTPOptions = ['ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ]];
    }

    // Viele Provider verlangen: From == Auth-User
    if (!$from) {
        throw new \RuntimeException("Absenderadresse leer. Setze SMTP_FROM_EMAIL oder SMTP_USER.");
    }
    $m->setFrom($from, $name);

    return $m;
}

/** App-Secret (für Remember-HMAC) */
private static function appSecret(): string {
    // 1) Umgebung – klappt in CLI/Worker, wenn korrekt gesetzt
    $s = getenv('APP_SECRET');
    if (!$s) {
        // 2) Webserver-Varianten (php-fpm clears env häufig)
        $s = $_SERVER['APP_SECRET'] ?? ($_ENV['APP_SECRET'] ?? '');
    }
    if (!$s && defined('APP_SECRET_CONST')) {
        // 3) Fester Fallback aus Config (falls du das nutzt)
        $s = APP_SECRET_CONST;
    }
    if (!$s) {
        // 4) Letzter Fallback: stabiler, projektspezifischer Fixwert
        // (besser ist natürlich: APP_SECRET in Server-Config fix setzen!)
        $s = hash('sha256', __DIR__ . '|' . (php_ini_loaded_file() ?: 'no-ini'));
    }
    return $s;
}

// Zentral: HTTPS/Proxy-sichere Erkennung
private static function isHttps(): bool {
    return (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '') === 'on')
    );
}

/**
 * Normalisiert den Salt:
 * - Wenn Hex im DB-Feld: in Raw-Bytes umwandeln
 * - Wenn Raw im DB-Feld: künftig als Hex persistieren
 * - Wenn leer: neu generieren und als Hex persistieren
 * Gibt IMMER Raw-Bytes zurück.
 */
private static function normalizeSalt(\mysqli $db, int $userId, ?string $stored): string {
    $stored = (string)($stored ?? '');
    if ($stored !== '' && ctype_xdigit($stored) && (strlen($stored) % 2 === 0)) {
        $raw = hex2bin($stored);
        if ($raw !== false && $raw !== '') return $raw;
    }
    if ($stored !== '') {
        // Legacy/raw -> künftig Hex speichern
        $raw = $stored;
    } else {
        // neu erzeugen
        $raw = random_bytes(32);
    }
    $hex = bin2hex($raw);
    $up  = $db->prepare("UPDATE users SET remember_device_salt=? WHERE userID=?");
    $up->bind_param("si", $hex, $userId);
    $up->execute();
    $up->close();
    return $raw;
}

/** ===== E-Mail 2FA ===== */

private static function gen2faCode(int $len = self::TWOFA_CODE_LEN): string {
    $n = random_int(0, (10 ** $len) - 1);
    return str_pad((string)$n, $len, '0', STR_PAD_LEFT);
}

public static function startEmail2faForUser(\mysqli $db, int $userId, string $email, ?string $subjectOverride = null, ?string $htmlOverride = null): void
{
    // Resend-Cooldown prüfen
    $stmt = $db->prepare("SELECT twofa_email_last_sent_at FROM users WHERE userID=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!empty($row['twofa_email_last_sent_at'])) {
        $last = new \DateTimeImmutable($row['twofa_email_last_sent_at']);
        if ((time() - $last->getTimestamp()) < self::TWOFA_RESEND_COOL) {
            throw new \RuntimeException('Bitte warte kurz, bevor du einen neuen Code anforderst.');
        }
    }

    // Code generieren und speichern
    $code   = self::gen2faCode();
    $hash   = password_hash($code, PASSWORD_DEFAULT);
    $expiry = (new \DateTimeImmutable('now +' . self::TWOFA_TTL_MIN . ' minutes'))->format('Y-m-d H:i:s');

    $stmt = $db->prepare("
        UPDATE users SET
          twofa_email_code_hash = ?,
          twofa_email_code_expires_at = ?,
          twofa_email_last_sent_at = NOW()
        WHERE userID = ?
    ");
    $stmt->bind_param("ssi", $hash, $expiry, $userId);
    $stmt->execute();
    $stmt->close();

    // Einstellungen (nur was wir brauchen)
    $hp_title = 'nexpell';
    $admin_email = 'info@' . ($_SERVER['HTTP_HOST'] ?? 'example.com');
    if ($res = $db->query("SELECT hptitle, adminemail FROM `settings` LIMIT 1")) {
        if ($s = $res->fetch_assoc()) {
            $hp_title    = $s['hptitle']    ?: $hp_title;
            $admin_email = $s['adminemail'] ?: $admin_email;
        }
        $res->free();
    }

    // --- Mailinhalt: ausschließlich Overrides nutzen ---
    if ($subjectOverride === null || $htmlOverride === null) {
    $subject = 'Your login code';
    $html    = '<p>Code: <strong>' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</strong></p>';
    } else {
        $subject    = $subjectOverride;
        $codePretty = htmlspecialchars(chunk_split($code, 3, ' '), ENT_QUOTES, 'UTF-8');
        $html       = str_replace('{CODE}', $codePretty, $htmlOverride);
    }

    // --- Versand ---
    $ok = false;
    try {
        $sendResult = Email::sendEmail($admin_email, $hp_title, $email, $subject, $html);

        if (is_bool($sendResult)) {
            $ok = $sendResult;
        } elseif (is_array($sendResult)) {
            $status = $sendResult['result'] ?? ($sendResult['status'] ?? null);
            $ok = ($status === 'success' || $status === 'ok' || $status === true);
        }

        error_log('[2FA] sendEmail() return: ' . var_export($sendResult, true));
    } catch (\Throwable $e) {
        error_log('[2FA] sendEmail() Exception: ' . $e->getMessage());
        $ok = false;
    }

    if (!$ok) {
        error_log('[2FA] WARN: Mailversand unbestätigt / fehlgeschlagen (aber Flow geht weiter).');
    }
}

public static function verifyEmail2fa(\mysqli $db, int $userId, string $inputCode): bool {
    $stmt = $db->prepare("
        SELECT twofa_email_code_hash, twofa_email_code_expires_at,
               twofa_failed_attempts, twofa_locked_until
        FROM users WHERE userID=?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$u) return false;

    // Lock prüfen
    if (!empty($u['twofa_locked_until']) &&
        new \DateTimeImmutable($u['twofa_locked_until']) > new \DateTimeImmutable('now')) {
        return false;
    }

    // Ablauf prüfen
    if (empty($u['twofa_email_code_expires_at']) ||
        new \DateTimeImmutable($u['twofa_email_code_expires_at']) < new \DateTimeImmutable('now')) {
        return false;
    }

    $clean = preg_replace('/\s+/', '', $inputCode ?? '');
    $ok = !empty($u['twofa_email_code_hash']) && password_verify($clean, $u['twofa_email_code_hash']);

    if ($ok) {
        $stmt = $db->prepare("
            UPDATE users SET
              twofa_email_code_hash=NULL,
              twofa_email_code_expires_at=NULL,
              twofa_failed_attempts=0,
              twofa_locked_until=NULL
            WHERE userID=?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
        return true;
    }

    // Fehlversuch zählen / sperren
    $fails = (int)$u['twofa_failed_attempts'] + 1;
    $lock  = null;
    if ($fails >= self::TWOFA_MAX_FAILS) {
        $lockAt = (new \DateTimeImmutable('now +' . self::TWOFA_LOCK_MIN . ' minutes'))->format('Y-m-d H:i:s');
        $lock   = $lockAt;
        $fails  = 0;
    }
    $stmt = $db->prepare("UPDATE users SET twofa_failed_attempts=?, twofa_locked_until=? WHERE userID=?");
    $stmt->bind_param("isi", $fails, $lock, $userId);
    $stmt->execute();
    $stmt->close();

    return false;
}

/** ===== Remember this device ===== */

public static function issueRememberDeviceCookie(\mysqli $db, int $userId, string $ttl = '+30 days'): void {
    // Salt laden & normalisieren (Raw-Bytes garantiert)
    $stmt = $db->prepare("SELECT remember_device_salt FROM users WHERE userID=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    $rawSalt = self::normalizeSalt($db, $userId, $row['remember_device_salt'] ?? null);

    // Ablaufzeitpunkt (Integer-Timestamp)
    $expObj = new \DateTimeImmutable($ttl);
    $exp    = (int)$expObj->format('U');

    // Payload & Signatur
    $payload = json_encode(['uid' => $userId, 'exp' => $exp], JSON_UNESCAPED_SLASHES);
    $sig     = hash_hmac('sha256', $payload, self::appSecret() . $rawSalt, true);

    // Cookie-Wert: base64(payload) + '.' + base64(sig)
    $cookie = base64_encode($payload) . '.' . base64_encode($sig);

    // Secure-Flag sauber erkennen (HTTPS/Proxy)
    $secure = self::isHttps();

    // WICHTIG: vor Output!
    setcookie(self::RDV_COOKIE, $cookie, [
        'expires'  => $exp,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => self::RDV_SAMESITE,
    ]);
}

public static function checkRememberDeviceCookie(?string $salt, int $userId): bool {
    // Cookie & Salt vorhanden?
    if (empty($_COOKIE[self::RDV_COOKIE]) || $salt === null) return false;
    $salt = trim((string)$salt);
    if ($salt === '') return false;

    // Salt vorbereiten (HEX → raw), sonst raw übernehmen
    $rawSalt = (ctype_xdigit($salt) && (strlen($salt) % 2 === 0)) ? hex2bin($salt) : $salt;
    if ($rawSalt === false || $rawSalt === '') return false;

    // Cookie-Teile holen
    $parts = explode('.', $_COOKIE[self::RDV_COOKIE], 2);
    if (count($parts) !== 2) return false;
    [$b64payload, $b64sig] = $parts;

    // Base64 strikt decodieren
    $payload = base64_decode($b64payload, true);
    $sig     = base64_decode($b64sig, true);
    if ($payload === false || $sig === false) return false;

    // Signatur prüfen (Timing-sicher)
    $calc = hash_hmac('sha256', $payload, self::appSecret() . $rawSalt, true);
    if (!hash_equals($calc, $sig)) return false;

    // Payload prüfen
    $data = json_decode($payload, true);
    if (!is_array($data)) return false;

    // userId & Ablauf prüfen
    if ((int)($data['uid'] ?? 0) !== (int)$userId) return false;
    $exp = (int)($data['exp'] ?? 0);
    if ($exp <= 0 || time() >= $exp) return false;

    return true;
}

// Auto-Cleanup von abgelaufenen Remember-Einträgen
public static function pruneRememberDeviceIfExpired(?string $cookieValue): void
{
    if (empty($cookieValue)) return;

    // Cookie zerlegen
    $parts = explode('.', $cookieValue, 2);
    if (count($parts) !== 2) return;

    $payload = base64_decode($parts[0], true);
    if ($payload === false) return;

    $data = json_decode($payload, true);
    if (!is_array($data)) return;

    $exp = (int)($data['exp'] ?? 0);
    if ($exp > 0 && time() >= $exp) {
        // Abgelaufen → sofort clientseitig entfernen
        $secure = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
            || (($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '') === 'on')
        );

        setcookie(self::RDV_COOKIE, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => self::RDV_SAMESITE,
        ]);
    }
}

public static function rotateRememberSalt(\mysqli $db, int $userId): void {
    // neuen RAW-Salt erzeugen und als HEX speichern
    $hexSalt = bin2hex(random_bytes(32));
    $stmt = $db->prepare("UPDATE users SET remember_device_salt=? WHERE userID=?");
    $stmt->bind_param("si", $hexSalt, $userId);
    $stmt->execute();
    $stmt->close();
}

// ==== END: 2FA + Remember + Mailer ====

}
