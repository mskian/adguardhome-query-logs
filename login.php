<?php

session_start();

require_once 'functions.php';
include './store.php';
(new DevCoder\DotEnv('./.env'))->load();

header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=63072000');
header('X-Robots-Tag: noindex, nofollow', true);
header('Content-Type: application/json');

$user = getenv('USER');
$pass = getenv('PASS');
$user_password = password_hash($pass, PASSWORD_DEFAULT);

$input = json_decode(file_get_contents('php://input'), true);

$csrf_token = $input['csrf_token'] ?? '';
$username = sanitize_input($input['username'] ?? '');
$password = $input['password'] ?? '';

if (!verify_csrf_token($csrf_token)) {
    send_json_response('error', 'Invalid CSRF token.');
}

if (empty($username) || empty($password)) {
    send_json_response('error', 'Username and Password are required.');
}

if ($username === $user && password_verify($password, $user_password)) {
    $_SESSION['username'] = $username;
    send_json_response('success', 'Login successful.');
} else {
    send_json_response('error', 'Invalid username or password.');
}

?>