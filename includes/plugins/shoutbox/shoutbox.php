<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
global $_database, $languageService;

$recaptcha = nx_get_recaptcha_config();
$webkey = $recaptcha['webkey'];
$recaptchaEnabled = $recaptcha['enabled'];

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

$data_array = [
    'class'    => $class,
    'title'    => $languageService->get('title'),
    'subtitle' => 'Shoutbox'
];

echo $tpl->loadTemplate("shoutbox", "head", $data_array, 'plugin');
echo '<link rel="stylesheet" href="/includes/plugins/shoutbox/css/shoutbox.css">';

$username = trim((string)($_SESSION['username'] ?? ''));
$loggedin = isset($_SESSION['userID']) && (int)$_SESSION['userID'] > 0;

if (!$loggedin && $recaptchaEnabled) {
    nx_mark_recaptcha_required();
}

$run = 0;
$fehler = [];

if ($loggedin || !$recaptchaEnabled || !empty($_SESSION['shoutbox_guest_verified'])) {
    $run = 1;
} else {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $recaptcha_response = (string)($_POST['g-recaptcha-response'] ?? '');
        if (!empty($recaptcha_response) && nx_verify_recaptcha($recaptcha_response, (string)($_SERVER['REMOTE_ADDR'] ?? ''))) {
            $_SESSION['shoutbox_guest_verified'] = 1;
            $run = 1;
        } else {
            $fehler[] = "reCAPTCHA Error";
        }
    }
}

$result = $_database->query("SELECT id, created_at, username, message FROM plugins_shoutbox_messages ORDER BY created_at ASC LIMIT 100");
if (!$result) {
    $result = false;
    $fehler[] = 'Shoutbox konnte aktuell nicht geladen werden.';
}

?>


<div class="shoutbox-shell">
    <div class="card shoutbox-card">
        <div class="card-body">
            <div class="shoutbox-headline">
                <div class="shoutbox-title-wrap">
                    <h5>Shoutbox</h5>
                    <p>Kurze Nachrichten aus der Community, direkt und ohne Umwege.</p>
                </div>
                <div class="shoutbox-status">
                    <span class="shoutbox-status-dot"></span>
                    Live Feed
                </div>
            </div>

            <div id="messages" class="shoutbox-feed" aria-live="polite">
                <?php if ($result instanceof mysqli_result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <article class="shoutbox-message">
                            <img class="shoutbox-avatar" src="/images/avatars/svg-avatar.php?name=<?= urlencode((string)$row['username']) ?>" alt="<?= htmlspecialchars((string)$row['username'], ENT_QUOTES, 'UTF-8') ?>">
                            <div>
                                <div class="shoutbox-meta">
                                    <span class="shoutbox-user"><?= htmlspecialchars((string)$row['username']) ?></span>
                                    <span class="shoutbox-time">
                                        <?= !empty($row['created_at']) ? htmlspecialchars(date('d.m.Y H:i', strtotime($row['created_at']))) : '--.--.---- --:--' ?>
                                    </span>
                                </div>
                                <p class="shoutbox-text"><?= nl2br(htmlspecialchars((string)$row['message'])) ?></p>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="shoutbox-empty" id="shoutbox-empty-state">
                        <strong>Noch keine Nachrichten</strong>
                        <span>Starte die Unterhaltung und setze den ersten Eintrag.</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($run === 1): ?>
                <form id="shoutbox-form" class="shoutbox-composer">
                    <input type="text" name="company" value="" tabindex="-1" autocomplete="off" style="position:absolute; left:-9999px; height:0; width:0;">
                    <div class="shoutbox-composer-head">
                        <div class="shoutbox-user-chip">
                            <img class="shoutbox-avatar" src="/images/avatars/svg-avatar.php?name=<?= urlencode($username !== '' ? $username : 'Gast') ?>" alt="<?= htmlspecialchars($username !== '' ? $username : 'Gast', ENT_QUOTES, 'UTF-8') ?>">
                            <span><?= htmlspecialchars($username !== '' ? $username : 'Gast') ?></span>
                        </div>
                        <span class="shoutbox-limit">Maximal 500 Zeichen</span>
                    </div>

                    <div class="shoutbox-form-row">
                        <input
                            class="form-control"
                            type="text"
                            id="shoutbox-username"
                            name="username"
                            placeholder="Name"
                            required
                            <?php if ($username !== ''): ?>value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>" readonly<?php endif; ?>
                        />
                        <input
                            class="form-control"
                            type="text"
                            id="shoutbox-message"
                            name="message"
                            placeholder="Schreibe eine kurze Nachricht ..."
                            maxlength="500"
                            required
                        />
                        <button type="submit" class="btn btn-primary">Senden</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-info shoutbox-alert">
                    Bitte registriere dich oder bestätige das reCAPTCHA, um Nachrichten zu senden.
                </div>
                <?php if (count($fehler) > 0): ?>
                    <?php foreach ($fehler as $err): ?>
                        <div class="alert alert-danger shoutbox-alert"><?= htmlspecialchars($err) ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <form method="post" class="shoutbox-captcha">
                    <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($webkey, ENT_QUOTES, 'UTF-8') ?>"></div>
                    <button type="submit" class="btn btn-primary mt-3">Bestätigen</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


function renderMessage(msg) {
    const time = msg.timestamp ? new Date(msg.timestamp.replace(' ', 'T')) : null;
    const dateLabel = time && !Number.isNaN(time.getTime())
        ? time.toLocaleString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
        : '--.--.---- --:--';

    return `
        <article class="shoutbox-message">
            <img class="shoutbox-avatar" src="/images/avatars/svg-avatar.php?name=${encodeURIComponent(msg.username || 'Gast')}" alt="${escapeHtml(msg.username || 'Gast')}">
            <div>
                <div class="shoutbox-meta">
                    <span class="shoutbox-user">${escapeHtml(msg.username)}</span>
                    <span class="shoutbox-time">${escapeHtml(dateLabel)}</span>
                </div>
                <p class="shoutbox-text">${escapeHtml(msg.message).replace(/\n/g, '<br>')}</p>
            </div>
        </article>
    `;
}

function renderEmptyState() {
    return `
        <div class="shoutbox-empty" id="shoutbox-empty-state">
            <strong>Noch keine Nachrichten</strong>
            <span>Starte die Unterhaltung und setze den ersten Eintrag.</span>
        </div>
    `;
}

async function fetchMessages() {
    try {
        const res = await fetch('/includes/plugins/shoutbox/shoutbox_ajax.php', {
            headers: { 'Accept': 'application/json' }
        });
        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Fehler beim JSON-Parsing:', e.message);
            console.error('Server-Antwort:', text);
            return;
        }

        if (data.status !== 'success') {
            console.error('Fehler beim Laden:', data.message);
            return;
        }

        const container = document.getElementById('messages');
        if (!container) {
            return;
        }

        if (!Array.isArray(data.messages) || data.messages.length === 0) {
            container.innerHTML = renderEmptyState();
            return;
        }

        container.innerHTML = data.messages.slice().reverse().map(renderMessage).join('');
        container.scrollTop = container.scrollHeight;
    } catch (err) {
        console.error('Fehler beim Laden der Nachrichten:', err);
    }
}

document.getElementById('shoutbox-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const usernameInput = document.getElementById('shoutbox-username');
    const messageInput = document.getElementById('shoutbox-message');
    const companyInput = document.querySelector('#shoutbox-form input[name="company"]');

    const username = usernameInput.value.trim();
    const message = messageInput.value.trim();
    const company = companyInput ? companyInput.value : '';

    if (!username) {
        alert('Bitte gib einen Namen ein.');
        return;
    }
    if (!message) {
        alert('Bitte gib eine Nachricht ein.');
        return;
    }
    if (message.length > 500) {
        alert('Die Nachricht darf maximal 500 Zeichen lang sein.');
        return;
    }

    try {
        const res = await fetch('/includes/plugins/shoutbox/shoutbox_ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
            },
            body: new URLSearchParams({ username, message, company }).toString()
        });

        const text = await res.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            alert('Fehler beim JSON-Parsing: ' + e.message + '\nServer-Antwort:\n' + text);
            return;
        }

        if (result.status === 'success') {
            messageInput.value = '';
            fetchMessages();
        } else {
            alert('Fehler: ' + (result.message || 'Unbekannter Fehler'));
        }
    } catch (err) {
        alert('Netzwerkfehler: ' + err.message);
    }
});

fetchMessages();
setInterval(fetchMessages, 10000);
</script>


