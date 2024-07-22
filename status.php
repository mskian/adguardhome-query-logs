<?php

include './store.php';
(new DevCoder\DotEnv('./.env'))->load();

header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=63072000');
header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow', true);

$statusApiUrl = getenv('BASE_URL') . "/control/status";
$username = getenv('USERNAME');
$password = getenv('PASSWORD');

if (empty($username) || empty($password)) {
    echo json_encode(['error' => 'Missing username or password']);
    http_response_code(400);
    exit;
}

function performCurlRequest($url, $username, $password) {
    $ch = curl_init();
    
    if ($ch === false) {
        return ['error' => 'Failed to initialize cURL'];
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return ['error' => 'Failed to fetch data from API'];
    }

    if ($httpCode !== 200) {
        return ['error' => "HTTP Error: $httpCode"];
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Failed to decode JSON response'];
    }

    return $data;
}

$statusData = performCurlRequest($statusApiUrl, $username, $password);
if (isset($statusData['error'])) {
    echo json_encode($statusData);
    http_response_code(500);
    exit;
}

if (isset($statusData['error'])) {
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
    'adblockStatus' => sanitizeData($statusData)
]);

?>