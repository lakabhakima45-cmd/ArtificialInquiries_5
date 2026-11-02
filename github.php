<?php
// github.php — connexion avec GitHub (OAuth) pour response.html.
//   github.php                 → redirige vers GitHub pour autoriser l'application
//   github.php?code=…&state=…  → retour de GitHub : le code est échangé côté serveur
//   github.php?action=me       → renvoie le compte GitHub connecté (JSON) à response.html
//   github.php?action=logout   → oublie la connexion GitHub
//   github.php?action=setup    → formulaire (une seule fois) pour enregistrer l'ID client et le secret
// Le secret est rangé HORS du dossier www (jamais publié sur le web ni sur GitHub).

session_start();

$CONFIG_FILE = dirname(__DIR__) . '/github-config.json';
$self = 'https://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');
$page = dirname($self) . '/response.html';
$action = $_GET['action'] ?? '';

function page($html) {
    echo '<!doctype html><meta charset="utf-8"><title>GitHub – configuration</title>'
       . '<body style="font-family:sans-serif;max-width:560px;margin:3rem auto;line-height:1.5">' . $html;
    exit;
}

function http($url, $post = null, $token = null) {
    $ch = curl_init($url);
    $headers = ['Accept: application/json', 'User-Agent: ArtificialInquiries-Ex5'];
    if ($token) $headers[] = 'Authorization: Bearer ' . $token;
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_HTTPHEADER => $headers]);
    if ($post !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $out = curl_exec($ch);
    curl_close($ch);
    return $out ? json_decode($out, true) : null;
}

if ($action === 'me') {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(['user' => $_SESSION['github_user'] ?? null]);
    exit;
}

if ($action === 'logout') {
    unset($_SESSION['github_user']);
    http_response_code(204);
    exit;
}

if ($action === 'setup') {
    if (file_exists($CONFIG_FILE)) page('<p>✅ GitHub est déjà configuré. Pour changer le secret, supprime le fichier <code>~/github-config.json</code> sur alwaysdata, puis reviens ici.</p>');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = trim($_POST['client_id'] ?? '');
        $secret = trim($_POST['client_secret'] ?? '');
        if ($id === '' || $secret === '') page('<p>Les deux champs sont obligatoires. <a href="?action=setup">Recommencer</a></p>');
        file_put_contents($CONFIG_FILE, json_encode(['client_id' => $id, 'client_secret' => $secret]));
        chmod($CONFIG_FILE, 0600);
        page('<p>✅ Configuration enregistrée. <a href="' . htmlspecialchars($page) . '">Retour à la page</a></p>');
    }
    page('<h2>Configurer la connexion GitHub</h2>'
       . '<form method="post"><p><label>Client ID<br><input name="client_id" style="width:100%"></label></p>'
       . '<p><label>Client secret<br><input name="client_secret" type="password" style="width:100%"></label></p>'
       . '<p><button>Enregistrer</button></p></form>');
}

if (!file_exists($CONFIG_FILE)) {
    page('<p>La connexion GitHub n\'est pas encore configurée.</p>');
}
$config = json_decode(file_get_contents($CONFIG_FILE), true);

// Retour de GitHub
if (isset($_GET['code'])) {
    if (!isset($_SESSION['github_state']) || !hash_equals($_SESSION['github_state'], $_GET['state'] ?? '')) {
        header('Location: ' . $page . '?github=error');
        exit;
    }
    unset($_SESSION['github_state']);
    $tok = http('https://github.com/login/oauth/access_token', [
        'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret'],
        'code' => $_GET['code'],
        'redirect_uri' => $self,
    ]);
    $user = !empty($tok['access_token']) ? http('https://api.github.com/user', null, $tok['access_token']) : null;
    if (!empty($user['login'])) {
        session_regenerate_id(true);
        $_SESSION['github_user'] = [
            'login' => $user['login'],
            'name' => $user['name'] ?: $user['login'],
            'avatar' => $user['avatar_url'] ?? '',
        ];
        header('Location: ' . $page . '?github=ok');
    } else {
        header('Location: ' . $page . '?github=error');
    }
    exit;
}

// Départ vers GitHub
$_SESSION['github_state'] = bin2hex(random_bytes(16));
header('Location: https://github.com/login/oauth/authorize?' . http_build_query([
    'client_id' => $config['client_id'],
    'redirect_uri' => $self,
    'state' => $_SESSION['github_state'],
    'allow_signup' => 'false',
]));
