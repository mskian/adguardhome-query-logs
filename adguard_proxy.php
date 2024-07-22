<?php

include './store.php';
(new DevCoder\DotEnv('./.env'))->load();

header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=63072000');
header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow', true);

$apiUrl = getenv('BASE_URL') . "/control/querylog";
$username = getenv('USERNAME');
$password = getenv('PASSWORD');

if (empty($username) || empty($password)) {
    echo json_encode(['error' => 'Missing username or password']);
    http_response_code(400);
    exit;
}

$ch = curl_init();

if ($ch === false) {
    echo json_encode(['error' => 'Failed to initialize cURL']);
    http_response_code(500);
    exit;
}

curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

$response = curl_exec($ch);

if ($response === false) {
    error_log('cURL Error: ' . curl_error($ch));
    echo json_encode(['error' => 'Failed to fetch data from API']);
    curl_close($ch);
    http_response_code(500);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode !== 200) {
    error_log('HTTP Error: ' . $httpCode);
    echo json_encode(['error' => "HTTP Error: $httpCode"]);
    curl_close($ch);
    http_response_code($httpCode);
    exit;
}

curl_close($ch);

$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log('JSON Decode Error: ' . json_last_error_msg());
    echo json_encode(['error' => 'Failed to decode JSON response']);
    http_response_code(500);
    exit;
}

if (!isset($data['data']) || !is_array($data['data']) || !isset($data['oldest'])) {
    error_log('Unexpected API response structure');
    echo json_encode(['error' => 'Unexpected API response structure']);
    http_response_code(500);
    exit;
}

function sanitizeData($data) {
    if (is_array($data)) {
        return array_map('sanitizeData', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

echo json_encode([
    'data' => sanitizeData($data['data']),
    'oldest' => sanitizeData($data['oldest'])
]);

?>