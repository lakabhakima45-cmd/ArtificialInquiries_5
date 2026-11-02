<?php
// proxy.php — relaie les requêtes de response.html vers l'API Omeka S commune du cours.
// Le serveur humanum-p8.fr n'envoie pas d'en-tête CORS : le navigateur bloque les appels directs.
// Ici, c'est le serveur PHP (même origine que la page) qui appelle l'API, donc pas de blocage.
//
// Écriture pour les comptes CAS / GitHub / Google : ces connexions ne donnent pas de clé Omeka.
// Quand une personne est connectée par l'un d'eux (session PHP), le proxy ajoute la clé API
// du serveur (rangée HORS du dossier www, dans ~/omeka-config.json) pour qu'elle puisse écrire.
//   proxy.php?path=…           → relais vers l'API
//   proxy.php?action=status    → { write: true/false } : écriture possible pour la session ?
//   proxy.php?action=setup     → formulaire (une seule fois) pour enregistrer la clé API du serveur
// Sécurité : seul l'Omeka commun est joignable (pas de proxy ouvert), et rien n'est enregistré.

const OMEKA_API = 'https://humanum-p8.fr/omk_artificialinquiries/api/';

session_start();
$CONFIG_FILE = dirname(__DIR__) . '/omeka-config.json';
$user = $_SESSION['cas_user'] ?? ($_SESSION['github_user']['login'] ?? ($_SESSION['google_user']['email'] ?? null));
session_write_close();
$action = $_GET['action'] ?? '';

function page($html) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>Omeka – configuration</title>'
       . '<body style="font-family:sans-serif;max-width:560px;margin:3rem auto;line-height:1.5">' . $html;
    exit;
}

function fail($code, $message) {
    http_response_code($code);
    echo json_encode(['errors' => ['proxy' => $message]]);
    exit;
}

if ($action === 'setup') {
    if (file_exists($CONFIG_FILE)) page('<p>✅ La clé API du serveur est déjà configurée. Pour la changer, supprime le fichier <code>omeka-config.json</code> (dossier au-dessus de www), puis reviens ici.</p>');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = trim($_POST['key_identity'] ?? '');
        $cred = trim($_POST['key_credential'] ?? '');
        if ($id === '' || $cred === '') page('<p>Les deux champs sont obligatoires. <a href="?action=setup">Recommencer</a></p>');
        file_put_contents($CONFIG_FILE, json_encode(['key_identity' => $id, 'key_credential' => $cred]));
        chmod($CONFIG_FILE, 0600);
        page('<p>✅ Clé API enregistrée. Les personnes connectées par CAS, GitHub ou Google peuvent maintenant créer, modifier et supprimer.</p>');
    }
    page('<h2>Clé API Omeka S du serveur</h2>'
       . '<p>Utilisée pour les personnes connectées par CAS, GitHub ou Google.</p>'
       . '<form method="post" autocomplete="off"><p><label>key_identity<br><input name="key_identity" autocomplete="off" style="width:100%"></label></p>'
       . '<p><label>key_credential<br><input name="key_credential" type="password" autocomplete="new-password" style="width:100%"></label></p>'
       . '<p><button>Enregistrer</button></p></form>');
}

header('Content-Type: application/json; charset=utf-8');

if ($action === 'status') {
    header('Cache-Control: no-store');
    echo json_encode(['write' => $user !== null && file_exists($CONFIG_FILE)]);
    exit;
}

$path = $_GET['path'] ?? '';
// ressource Omeka (items, properties, resource_templates…), éventuellement un id, puis la requête
if (!preg_match('#^[a-z_]+(/[0-9]+)?(\?[^\s]*)?$#', $path)) {
    fail(400, 'Chemin d\'API invalide.');
}

$method = $_SERVER['REQUEST_METHOD'];
if (!in_array($method, ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'], true)) {
    fail(405, 'Méthode non autorisée.');
}

// personne connectée par CAS / GitHub / Google, sans clé à elle : on ajoute la clé du serveur
if ($user !== null && strpos($path, 'key_identity=') === false && file_exists($CONFIG_FILE)) {
    $keys = json_decode(file_get_contents($CONFIG_FILE), true);
    $path .= (strpos($path, '?') === false ? '?' : '&')
           . 'key_identity=' . urlencode($keys['key_identity'])
           . '&key_credential=' . urlencode($keys['key_credential']);
}

$body = file_get_contents('php://input');

$ch = curl_init(OMEKA_API . $path);
curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST  => $method,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTPHEADER     => ['Accept: application/json', 'Content-Type: application/json'],
]);
if ($body !== '' && $method !== 'GET') {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
}

$response = curl_exec($ch);
if ($response === false) {
    fail(502, 'Omeka S injoignable : ' . curl_error($ch));
}
http_response_code(curl_getinfo($ch, CURLINFO_RESPONSE_CODE));
curl_close($ch);
echo $response;
