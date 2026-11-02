<?php
// proxy.php — relaie les requêtes de response.html vers l'API Omeka S commune du cours.
// Le serveur humanum-p8.fr n'envoie pas d'en-tête CORS : le navigateur bloque les appels directs.
// Ici, c'est le serveur PHP (même origine que la page) qui appelle l'API, donc pas de blocage.
// Sécurité : seul l'Omeka commun est joignable (pas de proxy ouvert), et rien n'est enregistré.

const OMEKA_API = 'https://humanum-p8.fr/omk_artificialinquiries/api/';

header('Content-Type: application/json; charset=utf-8');

function fail($code, $message) {
    http_response_code($code);
    echo json_encode(['errors' => ['proxy' => $message]]);
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
