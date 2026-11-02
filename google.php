<?php
// google.php — vérifie côté serveur la connexion Google de response.html.
//   POST google.php (credential=…) → vérifie le jeton auprès de Google, ouvre la session
//   google.php?action=logout       → oublie la connexion Google
// Le jeton est vérifié par Google (tokeninfo) et doit être destiné à NOTRE ID client.

const GOOGLE_CLIENT_ID = '931364181243-ehvffvbn1vkmg83dek8070i3is8gmd91.apps.googleusercontent.com';

session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (($_GET['action'] ?? '') === 'logout') {
    unset($_SESSION['google_user']);
    http_response_code(204);
    exit;
}

$credential = $_POST['credential'] ?? '';
if ($credential === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Jeton Google manquant.']);
    exit;
}

$ch = curl_init('https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential));
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15]);
$info = json_decode(curl_exec($ch) ?: 'null', true);
curl_close($ch);

$ok = $info
    && ($info['aud'] ?? '') === GOOGLE_CLIENT_ID
    && in_array($info['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)
    && ($info['email_verified'] ?? '') === 'true';

if (!$ok) {
    http_response_code(401);
    echo json_encode(['error' => 'Jeton Google refusé.']);
    exit;
}

session_regenerate_id(true);
$_SESSION['google_user'] = [
    'email' => $info['email'],
    'name' => $info['name'] ?? $info['email'],
    'avatar' => $info['picture'] ?? '',
];
echo json_encode(['user' => $_SESSION['google_user']]);
