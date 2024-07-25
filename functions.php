<?php

function generate_csrf_token() {
    return bin2hex(random_bytes(32));
}

function store_csrf_token($token) {
    $_SESSION['csrf_token'] = $token;
}

function verify_csrf_token($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function sanitize_input($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function send_json_response($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

?>