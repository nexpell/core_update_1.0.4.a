<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;

global $_database, $languageService, $tpl;

if (isset($languageService) && method_exists($languageService, 'readModule')) {
    $languageService->readModule('twitch');
}

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

$data_array = [
    'class'    => $class,
    'title'    => $languageService->get('title'),
    'subtitle' => 'Twitch'
];

echo $tpl->loadTemplate("twitch", "head", $data_array, 'plugin');

$result = $_database->query("SELECT * FROM plugins_twitch_settings WHERE id = 1");
$row = $result ? $result->fetch_assoc() : null;

$mainChannel = trim((string)($row['main_channel'] ?? ''));
$extraChannels = array_values(array_filter(array_map('trim', explode(',', (string)($row['extra_channels'] ?? '')))));

$channels = [];
if ($mainChannel !== '') {
    $channels[] = $mainChannel;
}

foreach ($extraChannels as $channel) {
    if ($channel !== '' && !in_array($channel, $channels, true)) {
        $channels[] = $channel;
    }
}

if (!function_exists('twitch_ensure_settings_schema')) {
    function twitch_ensure_settings_schema(mysqli $database): void
    {
        static $ensured = false;

        if ($ensured) {
            return;
        }

        $database->query("
            CREATE TABLE IF NOT EXISTS plugins_twitch_settings (
                id int(11) NOT NULL AUTO_INCREMENT,
                main_channel varchar(100) NOT NULL,
                extra_channels text NOT NULL,
                client_id varchar(255) NOT NULL DEFAULT '',
                client_secret varchar(255) NOT NULL DEFAULT '',
                updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=2
        ");

        $requiredColumns = [
            'client_id' => "ALTER TABLE plugins_twitch_settings ADD client_id varchar(255) NOT NULL DEFAULT '' AFTER extra_channels",
            'client_secret' => "ALTER TABLE plugins_twitch_settings ADD client_secret varchar(255) NOT NULL DEFAULT '' AFTER client_id",
        ];

        foreach ($requiredColumns as $column => $sql) {
            $exists = $database->query("SHOW COLUMNS FROM plugins_twitch_settings LIKE '" . $database->real_escape_string($column) . "'");
            if ($exists instanceof mysqli_result && $exists->num_rows === 0) {
                $database->query($sql);
            }
        }

        $database->query("
            INSERT IGNORE INTO plugins_twitch_settings (id, main_channel, extra_channels, client_id, client_secret)
            VALUES (1, 'fl0m', 'zonixxcs,trilluxe', '', '')
        ");

        $ensured = true;
    }
}

if (!function_exists('twitch_ensure_banner_cache_schema')) {
    function twitch_ensure_banner_cache_schema(mysqli $database): void
    {
        static $ensured = false;

        if ($ensured) {
            return;
        }

        $database->query("
            CREATE TABLE IF NOT EXISTS plugins_twitch_banner_cache (
                channel varchar(100) NOT NULL,
                banner_url text NOT NULL,
                updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (channel)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $ensured = true;
    }
}

if (!function_exists('twitch_channel_initials')) {
    function twitch_channel_initials(string $channel): string
    {
        $clean = preg_replace('/[^a-z0-9]+/i', ' ', $channel);
        $parts = array_values(array_filter(explode(' ', (string)$clean)));
        if (empty($parts)) {
            return strtoupper(substr($channel, 0, 2));
        }

        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        return $initials !== '' ? $initials : strtoupper(substr($channel, 0, 2));
    }
}

if (!function_exists('twitch_card_variant')) {
    function twitch_card_variant(int $index): string
    {
        $variants = ['violet', 'blue', 'red', 'orange', 'dark', 'indigo'];
        return $variants[$index % count($variants)];
    }
}

if (!function_exists('twitch_preview_image')) {
    function twitch_preview_image(string $channel): string
    {
        $slug = strtolower(trim($channel));
        return 'https://static-cdn.jtvnw.net/previews-ttv/live_user_' . rawurlencode($slug) . '-640x360.jpg';
    }
}

if (!function_exists('twitch_http_json')) {
    function twitch_http_json(string $url, array $headers = [], string $method = 'GET'): ?array
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            if ($method === 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
            }

            $response = curl_exec($ch);
            $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            if ($response === false || $status >= 400) {
                return null;
            }

            $decoded = json_decode($response, true);
            return is_array($decoded) ? $decoded : null;
        }

        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'timeout' => 10,
                'header' => implode("\r\n", $headers),
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }
}

if (!function_exists('twitch_http_text')) {
    function twitch_http_text(string $url, array $headers = []): ?string
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            if ($response === false || $status >= 400) {
                return null;
            }

            return is_string($response) ? $response : null;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 10,
                'header' => implode("\r\n", $headers),
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        return $response !== false ? (string)$response : null;
    }
}

if (!function_exists('twitch_cached_profile_banner_image')) {
    function twitch_cached_profile_banner_image(mysqli $database, string $channel): string
    {
        $channel = strtolower(trim($channel));
        if ($channel === '') {
            return '';
        }

        $channelEsc = $database->real_escape_string($channel);
        $result = $database->query("
            SELECT banner_url
            FROM plugins_twitch_banner_cache
            WHERE channel = '{$channelEsc}'
            LIMIT 1
        ");

        if (!$result instanceof mysqli_result) {
            return '';
        }

        $row = $result->fetch_assoc();
        return trim((string)($row['banner_url'] ?? ''));
    }
}

if (!function_exists('twitch_store_profile_banner_image')) {
    function twitch_store_profile_banner_image(mysqli $database, string $channel, string $bannerUrl): void
    {
        $channel = strtolower(trim($channel));
        $bannerUrl = trim($bannerUrl);
        if ($channel === '' || $bannerUrl === '') {
            return;
        }

        $channelEsc = $database->real_escape_string($channel);
        $bannerEsc = $database->real_escape_string($bannerUrl);

        $database->query("
            INSERT INTO plugins_twitch_banner_cache (channel, banner_url)
            VALUES ('{$channelEsc}', '{$bannerEsc}')
            ON DUPLICATE KEY UPDATE banner_url = VALUES(banner_url), updated_at = CURRENT_TIMESTAMP
        ");
    }
}

if (!function_exists('twitch_profile_banner_image')) {
    function twitch_profile_banner_image(mysqli $database, string $channel): string
    {
        static $bannerCache = [];

        $channel = strtolower(trim($channel));
        if ($channel === '') {
            return '';
        }

        if (array_key_exists($channel, $bannerCache)) {
            return $bannerCache[$channel];
        }

        $cachedBanner = twitch_cached_profile_banner_image($database, $channel);
        if ($cachedBanner !== '') {
            $bannerCache[$channel] = $cachedBanner;
            return $cachedBanner;
        }

        $headers = [
            'User-Agent: Mozilla/5.0 (compatible; nexpell-twitch/1.0)',
        ];

        $html = twitch_http_text('https://www.twitch.tv/' . rawurlencode($channel), $headers);
        if (!is_string($html) || $html === '') {
            $bannerCache[$channel] = '';
            return '';
        }

        $normalizedHtml = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        $normalizedHtml = str_replace(['\\/', '\u002F'], '/', $normalizedHtml);

        $patterns = [
            '/https:\/\/static-cdn\.jtvnw\.net\/jtv_user_pictures\/[^"\']*profile_banner[^"\']*\.(?:png|jpe?g|webp)/i',
            '/"profileBannerImageURL"\s*:\s*"([^"]+)"/i',
            '/"bannerImageURL"\s*:\s*"([^"]+)"/i',
            '/<meta[^>]+property="og:image"[^>]+content="([^"]+)"/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $normalizedHtml, $matches) !== 1) {
                continue;
            }

            $candidate = isset($matches[1]) ? (string)$matches[1] : (string)$matches[0];
            $candidate = html_entity_decode($candidate, ENT_QUOTES, 'UTF-8');
            $candidate = str_replace(['\\/', '\u002F'], '/', $candidate);

            if (stripos($candidate, 'profile_banner') === false) {
                continue;
            }

            twitch_store_profile_banner_image($database, $channel, $candidate);
            $bannerCache[$channel] = $candidate;
            return $candidate;
        }

        $bannerCache[$channel] = '';
        return '';
    }
}

if (!function_exists('twitch_api_access_token')) {
    function twitch_api_access_token(string $clientId, string $clientSecret): ?string
    {
        if ($clientId === '' || $clientSecret === '') {
            return null;
        }

        $cachedToken = (string)($_SESSION['twitch_api_access_token'] ?? '');
        $cachedExpiry = (int)($_SESSION['twitch_api_access_token_expires'] ?? 0);

        if ($cachedToken !== '' && $cachedExpiry > (time() + 60)) {
            return $cachedToken;
        }

        $tokenUrl = 'https://id.twitch.tv/oauth2/token?client_id=' . rawurlencode($clientId)
            . '&client_secret=' . rawurlencode($clientSecret)
            . '&grant_type=client_credentials';

        $data = twitch_http_json($tokenUrl, ['Content-Type: application/x-www-form-urlencoded'], 'POST');
        if (!is_array($data) || empty($data['access_token'])) {
            return null;
        }

        $_SESSION['twitch_api_access_token'] = (string)$data['access_token'];
        $_SESSION['twitch_api_access_token_expires'] = time() + max(60, ((int)($data['expires_in'] ?? 0)) - 60);

        return (string)$data['access_token'];
    }
}

if (!function_exists('twitch_fetch_channel_state')) {
    function twitch_fetch_channel_state(array $channels, string $clientId, string $clientSecret): array
    {
        $token = twitch_api_access_token($clientId, $clientSecret);
        if ($token === null || empty($channels)) {
            return [];
        }

        $queryUsers = [];
        $queryStreams = [];
        foreach ($channels as $channel) {
            $queryUsers[] = 'login=' . rawurlencode($channel);
            $queryStreams[] = 'user_login=' . rawurlencode($channel);
        }

        $headers = [
            'Authorization: Bearer ' . $token,
            'Client-Id: ' . $clientId,
        ];

        $usersResponse = twitch_http_json('https://api.twitch.tv/helix/users?' . implode('&', $queryUsers), $headers);
        $streamsResponse = twitch_http_json('https://api.twitch.tv/helix/streams?' . implode('&', $queryStreams), $headers);

        $state = [];
        foreach ($channels as $channel) {
            $state[strtolower($channel)] = [
                'display_name' => $channel,
                'is_live' => false,
                'viewer_count' => 0,
                'cover_image' => '',
                'profile_image' => '',
            ];
        }

        foreach ((array)($usersResponse['data'] ?? []) as $user) {
            $key = strtolower((string)($user['login'] ?? ''));
            if ($key === '' || !isset($state[$key])) {
                continue;
            }

            $state[$key]['display_name'] = (string)($user['display_name'] ?? $state[$key]['display_name']);
            $state[$key]['profile_image'] = (string)($user['profile_image_url'] ?? '');
            $state[$key]['cover_image'] = (string)($user['offline_image_url'] ?? '');
        }

        foreach ((array)($streamsResponse['data'] ?? []) as $stream) {
            $key = strtolower((string)($stream['user_login'] ?? ''));
            if ($key === '' || !isset($state[$key])) {
                continue;
            }

            $thumbnail = str_replace(
                ['{width}', '{height}'],
                ['640', '360'],
                (string)($stream['thumbnail_url'] ?? '')
            );

            $state[$key]['is_live'] = true;
            $state[$key]['viewer_count'] = (int)($stream['viewer_count'] ?? 0);
            if ($thumbnail !== '') {
                $state[$key]['cover_image'] = $thumbnail;
            }
        }

        return $state;
    }
}

if (!function_exists('twitch_has_status_data')) {
    function twitch_has_status_data(array $channels, array $channelState): bool
    {
        if (empty($channels) || empty($channelState)) {
            return false;
        }

        foreach ($channels as $channel) {
            $key = strtolower($channel);
            if (!array_key_exists($key, $channelState)) {
                return false;
            }
        }

        return true;
    }
}

twitch_ensure_settings_schema($_database);
twitch_ensure_banner_cache_schema($_database);

$clientId = trim((string)($row['client_id'] ?? ''));
$clientSecret = trim((string)($row['client_secret'] ?? ''));
$channelState = twitch_fetch_channel_state($channels, $clientId, $clientSecret);
$hasStatusData = twitch_has_status_data($channels, $channelState);

$streamerCount = count($channels);
$liveCount = 0;
$viewerCount = 0;

if ($hasStatusData) {
    foreach ($channelState as $state) {
        if (!empty($state['is_live'])) {
            $liveCount++;
            $viewerCount += (int)($state['viewer_count'] ?? 0);
        }
    }
}

$offlineCount = max(0, $streamerCount - $liveCount);
?>

<div class="twitch-page">
    <section class="twitch-streamers-shell card">
        <section class="twitch-streamers-hero">
            <div class="twitch-streamers-title">
                <i class="bi bi-twitch"></i>
                <h2><?= htmlspecialchars($languageService->get('hero_title'), ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
            <div class="twitch-streamers-stats">
                <span><strong><?= $streamerCount ?></strong> <?= htmlspecialchars($languageService->get('stat_streamers'), ENT_QUOTES, 'UTF-8') ?></span>
                <span><strong class="is-live"><?= $hasStatusData ? $liveCount : '-' ?></strong> <?= htmlspecialchars($languageService->get('stat_live_now'), ENT_QUOTES, 'UTF-8') ?></span>
                <span><strong><?= $hasStatusData ? $viewerCount : '-' ?></strong> <?= htmlspecialchars($languageService->get('stat_viewers'), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </section>

        <?php if (!empty($channels)): ?>
            <section class="twitch-status-section">
                <header class="twitch-status-head">
                    <h3><i class="bi bi-people-fill"></i> <?= htmlspecialchars($languageService->get('channel_section'), ENT_QUOTES, 'UTF-8') ?> <span>(<?= $streamerCount ?>)</span></h3>
                </header>

                <div class="twitch-streamer-grid">
                    <?php foreach ($channels as $channel): ?>
                        <?php
                        $index = array_search($channel, $channels, true);
                        $variant = twitch_card_variant((int)$index);
                        $initials = twitch_channel_initials($channel);
                        $channelUrl = 'https://www.twitch.tv/' . rawurlencode($channel);
                        $channelKey = strtolower($channel);
                        $state = $channelState[$channelKey] ?? [];
                        $displayName = (string)($state['display_name'] ?? $channel);
                        $coverImage = (string)($state['cover_image'] ?? '');
                        $profileImage = (string)($state['profile_image'] ?? '');
                        $isLive = $hasStatusData && !empty($state['is_live']);
                        $viewerLabel = (int)($state['viewer_count'] ?? 0);
                        $coverSource = 'default';

                        if (!$isLive) {
                            $bannerImage = twitch_profile_banner_image($_database, $channel);
                            if ($bannerImage !== '') {
                                $coverImage = $bannerImage;
                                $coverSource = 'banner';
                            } elseif ($profileImage !== '') {
                                $coverImage = $profileImage;
                                $coverSource = 'profile';
                            }
                        }

                        if ($isLive && $coverImage === '') {
                            $coverImage = twitch_preview_image($channel);
                            $coverSource = 'live-preview';
                        }

                        $cardStyle = $coverImage !== '' ? "background-image:url('" . htmlspecialchars($coverImage, ENT_QUOTES, 'UTF-8') . "');" : '';
                        ?>
                        <button
                            type="button"
                            class="twitch-streamer-card variant-<?= $variant ?>"
                            data-channel="<?= htmlspecialchars($channel, ENT_QUOTES, 'UTF-8') ?>"
                            data-url="<?= htmlspecialchars($channelUrl, ENT_QUOTES, 'UTF-8') ?>"
                        >
                            <div class="twitch-streamer-cover<?= $isLive ? ' is-live' : ' is-offline' ?><?= $coverSource === 'profile' ? ' uses-profile-cover' : '' ?><?= $coverImage !== '' ? ' has-cover-image' : '' ?>"<?= $cardStyle !== '' ? ' style="' . $cardStyle . '"' : '' ?>>
                                <div class="twitch-streamer-cover-overlay"></div>
                                <?php if ($coverImage === ''): ?>
                                    <div class="twitch-streamer-cover-art"><?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="twitch-streamer-body">
                                <div class="twitch-streamer-avatar">
                                    <?php if ($profileImage !== ''): ?>
                                        <img src="<?= htmlspecialchars($profileImage, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>">
                                    <?php else: ?>
                                        <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
                                    <?php endif; ?>
                                </div>
                                <strong><?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></strong>
                                <?php if ($hasStatusData): ?>
                                    <div class="twitch-streamer-status<?= $isLive ? ' is-live' : ' is-offline' ?>">
                                        <i class="bi <?= $isLive ? 'bi-broadcast' : 'bi-moon-fill' ?>"></i>
                                        <?php if ($isLive): ?>
                                            <?= htmlspecialchars($languageService->get('status_live'), ENT_QUOTES, 'UTF-8') ?>
                                            <span><?= $viewerLabel ?> <?= htmlspecialchars($languageService->get('status_viewers'), ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php else: ?>
                                            <?= htmlspecialchars($languageService->get('status_offline'), ENT_QUOTES, 'UTF-8') ?>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="twitch-streamer-status is-neutral">
                                        <i class="bi bi-twitch"></i>
                                        <?= htmlspecialchars($languageService->get('channel_label'), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </button>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php else: ?>
            <section class="twitch-status-section">
                <div class="alert alert-info mt-3" role="alert">
                    <?= htmlspecialchars($languageService->get('no_channels_available'), ENT_QUOTES, 'UTF-8') ?>
                </div>
            </section>
        <?php endif; ?>
    </section>
</div>

<div id="twitch-stream-modal" class="twitch-stream-modal" hidden>
    <div class="twitch-stream-modal-backdrop" data-twitch-close></div>
    <div class="twitch-stream-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="twitch-stream-modal-title">
        <button type="button" class="twitch-stream-modal-close" aria-label="<?= htmlspecialchars($languageService->get('close_modal'), ENT_QUOTES, 'UTF-8') ?>" data-twitch-close>
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="twitch-stream-modal-player-wrap">
            <div id="twitch-stream-modal-player" class="twitch-stream-modal-player"></div>
        </div>
        <div class="twitch-stream-modal-footer">
            <div class="twitch-stream-modal-copy">
                <strong id="twitch-stream-modal-title"></strong>
                <span><?= htmlspecialchars($languageService->get('modal_hint'), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <a id="twitch-stream-modal-link" class="btn btn-outline-light" href="#" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-box-arrow-up-right"></i>
                <?= htmlspecialchars($languageService->get('open_channel'), ENT_QUOTES, 'UTF-8') ?>
            </a>
        </div>
    </div>
</div>

<script src="https://player.twitch.tv/js/embed/v1.js"></script>
<script>
  const TWITCH_MODAL_CONFIG = {
    parent: <?= json_encode($_SERVER['HTTP_HOST'] ?? 'localhost') ?>
  };

  (function () {
    let modalPlayer = null;

    function ensurePlayer(channel) {
      const target = document.getElementById('twitch-stream-modal-player');
      if (!target || !channel || typeof Twitch === 'undefined' || !Twitch.Player) {
        return false;
      }

      if (modalPlayer) {
        modalPlayer.setChannel(channel);
        return true;
      }

      target.innerHTML = '';
      modalPlayer = new Twitch.Player('twitch-stream-modal-player', {
        channel: channel,
        parent: [TWITCH_MODAL_CONFIG.parent],
        width: '100%',
        height: 720
      });

      return true;
    }

    function openModal(channel, url) {
      const modal = document.getElementById('twitch-stream-modal');
      const title = document.getElementById('twitch-stream-modal-title');
      const link = document.getElementById('twitch-stream-modal-link');

      if (!modal || !ensurePlayer(channel)) {
        window.open(url, '_blank', 'noopener');
        return;
      }

      if (title) {
        title.textContent = channel;
      }

      if (link) {
        link.href = url;
      }

      modal.hidden = false;
      document.body.classList.add('twitch-modal-open');
    }

    function closeModal() {
      const modal = document.getElementById('twitch-stream-modal');
      if (!modal) {
        return;
      }

      modal.hidden = true;
      document.body.classList.remove('twitch-modal-open');

      if (modalPlayer && typeof modalPlayer.pause === 'function') {
        modalPlayer.pause();
      }
    }

    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.twitch-streamer-card[data-channel]').forEach(function (card) {
        card.addEventListener('click', function () {
          openModal(card.getAttribute('data-channel'), card.getAttribute('data-url'));
        });
      });

      document.querySelectorAll('[data-twitch-close]').forEach(function (element) {
        element.addEventListener('click', closeModal);
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          closeModal();
        }
      });
    });
  })();
</script>
