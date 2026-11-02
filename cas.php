<?php
// cas.php — connexion avec le CAS de l'université Paris 8 (protocole CAS 2.0).
//   cas.php                → redirige vers la page de connexion du CAS
//   cas.php?ticket=ST-...  → retour du CAS : le ticket est validé ici, côté serveur
//   cas.php?action=me      → renvoie l'identifiant connecté (JSON) à response.html
//   cas.php?action=logout  → oublie la connexion CAS

session_start();

const CAS_SERVER = 'https://cas.univ-paris8.fr/cas';

$service = 'https://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');
$page = dirname($service) . '/response.html';
$action = $_GET['action'] ?? '';

if ($action === 'me') {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(['user' => $_SESSION['cas_user'] ?? null]);
    exit;
}

if ($action === 'logout') {
    unset($_SESSION['cas_user']);
    session_destroy();
    http_response_code(204);
    exit;
}

if (isset($_GET['ticket'])) {
    $url = CAS_SERVER . '/serviceValidate?service=' . urlencode($service)
         . '&ticket=' . urlencode($_GET['ticket']);
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15]);
    $xml = curl_exec($ch);
    curl_close($ch);

    if ($xml && preg_match('#<cas:user>\s*([^<]+?)\s*</cas:user>#', $xml, $m)) {
        session_regenerate_id(true);
        $_SESSION['cas_user'] = $m[1];
        header('Location: ' . $page . '?cas=ok');
    } else {
        header('Location: ' . $page . '?cas=error');
    }
    exit;
}

header('Location: ' . CAS_SERVER . '/login?service=' . urlencode($service));
