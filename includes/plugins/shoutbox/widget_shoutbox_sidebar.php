<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;

global $_database, $languageService;
$tpl = new Template();

$recaptcha = nx_get_recaptcha_config();
$webkey = $recaptcha['webkey'];
$recaptchaEnabled = $recaptcha['enabled'];

$lang = $languageService->detectLanguage();
$languageService->readPluginModule('shoutbox');

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

$data_array = [
    'class'    => $class,
    'title'    => $languageService->get('title'),
    'subtitle' => 'Shoutbox'
];

echo $tpl->loadTemplate("shoutbox", "head", $data_array, 'plugin');

$username = trim((string)($_SESSION['username'] ?? ''));
$loggedin = isset($_SESSION['userID']) && (int)$_SESSION['userID'] > 0;

if (!$loggedin && $recaptchaEnabled) {
    nx_mark_recaptcha_required();
}

$run = 0;
$fehler = [];

if ($loggedin || !$recaptchaEnabled || !empty($_SESSION['shoutbox_guest_verified'])) {
    $run = 1;
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $recaptchaResponse = (string)($_POST['g-recaptcha-response'] ?? '');
    if ($recaptchaResponse !== '' && nx_verify_recaptcha($recaptchaResponse, (string)($_SERVER['REMOTE_ADDR'] ?? ''))) {
        $_SESSION['shoutbox_guest_verified'] = 1;
        $run = 1;
    } else {
        $fehler[] = "reCAPTCHA Error";
    }
}

$result = $_database->query("SELECT id, created_at, username, message FROM plugins_shoutbox_messages ORDER BY id DESC LIMIT 100");
if (!$result) {
    die('DB-Abfrage fehlgeschlagen: ' . $_database->error);
}
?>
<style>
    #messages ul {
        list-style: none;
        padding: 0;
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ccc;
    }
    #messages li {
        padding: 5px;
        border-bottom: 1px solid #ddd;
    }
    #messages li strong {
        color: #007BFF;
    }
</style>
<div class="card mb-3">
  <div class="card-body">
    <div id="messages" class="mb-4">
      <h5 class="mb-3">Shoutbox Nachrichten:</h5>
      <ul class="list-group">
        <?php while ($row = $result->fetch_assoc()): ?>
          <li class="list-group-item">
            <strong><?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></strong>
            <small class="text-muted">
              [<?php echo date('H:i:s', strtotime($row['created_at'])); ?>]
            </small>:
            <?php echo htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8'); ?>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>

    <?php if ($run === 1): ?>
      <form id="shoutbox-form">
        <input type="text" name="company" value="" tabindex="-1" autocomplete="off" style="position:absolute; left:-9999px; height:0; width:0;">
        <input
          class="form-control"
          type="text"
          id="shoutbox-username"
          name="username"
          placeholder="Name"
          required
          style="flex: 0 0 150px;"
          <?php if ($username !== ''): ?>
            value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"
            readonly
          <?php endif; ?>
        />
        <input
          class="form-control"
          type="text"
          id="shoutbox-message"
          name="message"
          placeholder="Nachricht (max. 500 Zeichen)"
          maxlength="500"
          required
        />
        <button type="submit" class="btn btn-success">
          Senden
        </button>
      </form>
    <?php else: ?>
      <div class="alert alert-info mt-3">
        Bitte registriere dich oder löse das reCAPTCHA, um Nachrichten zu senden.
      </div>
      <?php foreach ($fehler as $err): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endforeach; ?>
      <form method="post" class="mt-3">
        <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($webkey, ENT_QUOTES, 'UTF-8'); ?>" style="margin-left: -15px;"></div>
        <button type="submit" class="btn btn-primary mt-2">Bestätigen</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<script>
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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

        const container = document.getElementById('messages').querySelector('ul');
        container.innerHTML = '';

        data.messages.forEach(msg => {
            const li = document.createElement('li');
            const time = new Date(msg.timestamp).toLocaleTimeString();
            li.innerHTML = `<strong>${escapeHtml(msg.username)}</strong> <small>[${time}]</small>: ${escapeHtml(msg.message)}`;
            container.appendChild(li);
        });

        const messagesDiv = document.getElementById('messages');
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
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
