<?php

require_once 'functions.php';

header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=63072000');
header('X-Robots-Tag: noindex, nofollow', true);

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
$csrf_token = generate_csrf_token();
store_csrf_token($csrf_token);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABqklEQVQ4jZ2Tv0scURDHP7P7SGWh14mkuXJZEH8cgqUWcklAsLBbCEEJSprkD7hD/4BUISHEkMBBiivs5LhCwRQBuWgQji2vT7NeYeF7GxwLd7nl4knMwMDMfL8z876P94TMLt+8D0U0EggQSsAjwMvga8ChJAqxqjTG3m53AQTg4tXHDRH9ABj+zf6oytbEu5d78nvzcyiivx7QXBwy46XOi5z1jbM+Be+nqVfP8yzuD3FM6rzIs9YE1hqGvDf15cVunmdx7w5eYJw1pcGptC9CD4gBUuef5Ujq/BhAlTLIeFYuyfmTZgeYv+2nPt1a371P+Hm1WUPYydKf0lnePwVmh3hnlcO1uc7yvgJUDtdG8oy98kduK2KjeHI0fzCQINSXOk/vlXBUOaihAwnGWd8V5r1uhe1VIK52V6JW2D4FqHZX5lphuwEE7ooyaN7gjLMmKSwYL+pMnV+MA/6+g8RYa2Lg2RBQbj4+rll7uymLy3coiuXb5PdQVf7rKYvojAB8Lf3YUJUHfSYR3XqeLO5JXvk0dhKqSqQQoCO+s5AIxCLa2Lxc6ALcAPwS26XFskWbAAAAAElFTkSuQmCC" />

    <title>Login - AdGuardHome Query Logs</title>
    <meta name="description" content="AdGuardHome Query Logs - Track the Query Logs from Adguard DNS Server."/>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.4/css/bulma.min.css" integrity="sha512-HqxHUkJM0SYcbvxUw5P60SzdOTy/QVwA1JJrvaXJv4q7lmbDZCmZaqz01UPOaQveoxfYRv1tHozWGPMcuTBuvQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        html, body {
            font-family: "Roboto Mono", monospace;
            font-weight: 600;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            padding-bottom: 20px;
            background-color: #282a36;
        }
        .container {
            font-family: "Roboto Mono", monospace;
            align-items: center;
            justify-content: center;
        }
        .notification {
            font-family: "Roboto Mono", monospace;
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 10;
            background-color: #ff5555;
            color: #f8f8f2;
        }
        .login-card {
            font-family: "Roboto Mono", monospace;
            background-color: #44475a;
            max-width: 400px;
            margin: 0 auto;
            border-radius: 10px;
        }
        .login-form {
            padding: 2rem;
        }
        input {
            font-family: "Roboto Mono", monospace;
            font-weight: 600;
        }
        button {
            font-family: "Roboto Mono", monospace;
            font-weight: 700;
        }
    </style>
</head>
<body>

<section class="section">
        <div class="container">
            <div class="card login-card">
                <div class="card-content">
                    <form id="loginForm" class="login-form">
                        <input type="hidden" name="csrf_token" value="<?= sanitize_input($csrf_token); ?>">
                        <div class="field">
                            <label class="label has-text-warning">Username</label>
                            <div class="control">
                                <input class="input is-rounded" type="text" id="username" name="username">
                            </div>
                        </div>
                        <div class="field">
                            <label class="label has-text-warning">Password</label>
                            <div class="control">
                                <input class="input is-rounded" type="password" id="password" name="password">
                            </div>
                        </div>
                        <div class="field">
                            <div class="control has-text-centered">
                                <button type="submit" class="button is-primary is-rounded">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</section>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(event) {
    event.preventDefault();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!validateForm(username, password)) {
        return;
    }

    const csrf_token = document.querySelector('input[name="csrf_token"]').value;

    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                password: password,
                csrf_token: csrf_token
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            window.location.href = 'index.php';
        } else {
            displayNotification(data.message, 'is-danger');
        }
    } catch (error) {
        displayNotification('An error occurred. Please try again.', 'is-danger');
    }
});

function validateForm(username, password) {
    if (!username) {
        displayNotification('Username is required.', 'is-danger');
        return false;
    }
    if (!password) {
        displayNotification('Password is required.', 'is-danger');
        return false;
    }
    if (password.length < 6) {
        displayNotification('Password must be at least 6 characters long.', 'is-danger');
        return false;
    }
    return true;
}

function displayNotification(message, type) {
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    const container = document.createElement('div');
    container.className = `notification ${type}`;
    container.innerText = message;
    document.body.appendChild(container);

    setTimeout(() => {
        container.remove();
    }, 3000);
}
</script>

</body>
</html>