<?php

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

if (!isset($_SESSION['username'])) {
    header('Location: user.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABqklEQVQ4jZ2Tv0scURDHP7P7SGWh14mkuXJZEH8cgqUWcklAsLBbCEEJSprkD7hD/4BUISHEkMBBiivs5LhCwRQBuWgQji2vT7NeYeF7GxwLd7nl4knMwMDMfL8z876P94TMLt+8D0U0EggQSsAjwMvga8ChJAqxqjTG3m53AQTg4tXHDRH9ABj+zf6oytbEu5d78nvzcyiivx7QXBwy46XOi5z1jbM+Be+nqVfP8yzuD3FM6rzIs9YE1hqGvDf15cVunmdx7w5eYJw1pcGptC9CD4gBUuef5Ujq/BhAlTLIeFYuyfmTZgeYv+2nPt1a371P+Hm1WUPYydKf0lnePwVmh3hnlcO1uc7yvgJUDtdG8oy98kduK2KjeHI0fzCQINSXOk/vlXBUOaihAwnGWd8V5r1uhe1VIK52V6JW2D4FqHZX5lphuwEE7ooyaN7gjLMmKSwYL+pMnV+MA/6+g8RYa2Lg2RBQbj4+rll7uymLy3coiuXb5PdQVf7rKYvojAB8Lf3YUJUHfSYR3XqeLO5JXvk0dhKqSqQQoCO+s5AIxCLa2Lxc6ALcAPwS26XFskWbAAAAAElFTkSuQmCC" />

    <title>AdGuardHome Query Logs</title>
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
           background-color: #312b2b;
           min-height: 100vh;
           padding: 20px;
           color: #4b0082;
           font-weight: 700;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .title {
            font-family: "Roboto Mono", monospace;
            color: #d78ee1;
            text-align: center;
            margin-bottom: 20px;
        }
        .log-entry {
            background-color: #e6e6fa;
            border-radius: 12px;
            font-family: "Roboto Mono", monospace;
            color: #4b0082;
            word-wrap: break-word;
            letter-spacing: .03em;

            margin-bottom: 15px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .blocked {
            font-family: "Roboto Mono", monospace;
            color: #e94e77;
        }
        .pagination-previous:hover:not(:disabled), .pagination-next:hover:not(:disabled) {
            background-color: #6a0dad;
            color: #ffffff;
        }
        .pagination-previous, .pagination-next {
            font-family: "Roboto Mono", monospace;
            background-color: #6a0dad;
            border: none;
            color: #fff;
            cursor: pointer;
            border-radius: 10px;
            padding: 10px 20px;
            margin: 5px;
        }
        .pagination-previous.disabled, .pagination-next.disabled {
            background-color: #d8bfd8;
            cursor: not-allowed;
             opacity: 0.5;
        }
        .notification {
            font-family: "Roboto Mono", monospace;
            position: fixed;
            top: 10px;
            right: 10px;
            max-width: 300px;
            z-index: 1000;
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .notification.is-success {
            font-family: "Roboto Mono", monospace;
            background-color: #5c2a9b;
            color: #ffffff;
        }
        .notification.is-danger {
            font-family: "Roboto Mono", monospace;
            background-color: #e94e77;
            color: #ffffff;
        }
        .notification .delete {
            color: #ffffff;
        }
        .refresh-icon {
            cursor: pointer;
            font-size: 24px;
            color: #f6ebb5;
            margin-left: 10px;
            vertical-align: middle;
        }
        .status {
            font-family: "Roboto Mono", monospace;
            font-weight: bold;
        }
        .has-text-centered {
            text-align: center;
        }
        .adblock-status {
            font-family: "Roboto Mono", monospace;
            word-wrap: break-word;
            padding: 10px;
            background-color: #d4acfa;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
        }
    </style>

</head>
<body>

    <div class="container">
        <h1 class="title is-size-5">AdGuardHome Query Logs</h1>
        <hr>
        <div id="adblock-status" class="adblock-status">
        <p class="has-text-centered">DNS Status...</p>
        </div>
        <br>
        <div class="has-text-centered">
            <span class="icon is-medium refresh-icon" onclick="fetchLogs()">
                <i class="fas fa-sync-alt"></i>
            </span>
        </div>
        <br>
        <div id="logs"></div>
        <br>
        <nav class="pagination is-centered" role="navigation" aria-label="pagination">
            <a class="pagination-previous" id="prev-page" aria-label="Previous page" aria-disabled="true">Previous</a>
            <a class="pagination-next" id="next-page" aria-label="Next page" aria-disabled="true">Next</a>
        </nav>
        <br>
        <hr>
        <div class="has-text-centered">
            <a href="logout.php" class="button is-danger is-rounded">Log out</a>
        </div>
        <br>
    </div>

    <div id="status-notification" class="notification">
        <button class="delete" onclick="closeNotification()"></button>
        <p id="notification-message">Logs Updated successfully</p>
    </div>

    <script>
       async function fetchAdBlockStatus() {
            document.getElementById('adblock-status').innerHTML = `<P class="has-text-centered">DNS Status...</p>`;
            try {
                const response = await fetch('status.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const data = await response.json();
                const isProtectionEnabled = data.adblockStatus && data.adblockStatus.protection_enabled;
                const protectionEnabled = isProtectionEnabled === "1" ? true : (isProtectionEnabled === "" ? false : null);
                if (protectionEnabled === true) {
                    document.getElementById('adblock-status').innerHTML = '<p class="has-text-centered">✅ AdBlock DNS is enabled</p>';
                } else if (protectionEnabled === false) {
                    document.getElementById('adblock-status').innerHTML = '<p class="has-text-centered">❌ AdBlock DNS is disabled</p>';
                } else {
                    document.getElementById('adblock-status').innerHTML = '<p class="has-text-centered">❓ AdBlock status is disabled</p>';
                }
            } catch (error) {
                document.getElementById('adblock-status').textContent = `Error: ${error.message}`;
            }
        }

        fetchAdBlockStatus();

        const PAGE_SIZE = 10;
        let currentPage = 1;
        let allLogs = [];

        function formatTimeToIndianLocalTime(utcTime) {
            const date = new Date(utcTime);
            const options = {
                timeZone: 'Asia/Kolkata',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            return new Intl.DateTimeFormat('en-IN', options).format(date);
        }

        function formatResponseTime(ms) {
            return `${Math.round(ms)} ms`;
        }

        function displayLogs(logs) {
            const logsContainer = document.getElementById('logs');
            logsContainer.innerHTML = '';

            if (logs.length === 0) {
                logsContainer.innerHTML = '<p class="notification is-info">No logs available.</p>';
                return;
            }

            logs.forEach(log => {
                const logEntry = document.createElement('div');
                logEntry.className = 'log-entry';

                const localTime = formatTimeToIndianLocalTime(log.time);
                const responseTime = formatResponseTime(log.elapsedMs);
                const isBlocked = !log.upstream || log.upstream.trim() === '';
                const statusClass = isBlocked ? 'blocked' : '';

                logEntry.innerHTML = `
                    <p><strong>Time:</strong> ${localTime}</p>
                    <p><strong>URL:</strong> ${log.question.name}</p>
                    <p><strong>Client:</strong> ${log.client_info.name || log.client}</p>
                    <p><strong>Status:</strong> <span class="${statusClass}">${isBlocked ? 'Blocked by AdGuard' : log.status}</span></p>
                    <p><strong>Upstream:</strong> ${log.upstream || '<span class="blocked">Blocked</span>'}</p>
                    <p><strong>Response Time:</strong> ${responseTime}</p>
                `;
                logsContainer.appendChild(logEntry);
            });
        }

        function paginateLogs(logs, page, pageSize) {
            const offset = (page - 1) * pageSize;
            return logs.slice(offset, offset + pageSize);
        }

        function updatePagination() {
            const prevPageButton = document.getElementById('prev-page');
            const nextPageButton = document.getElementById('next-page');

            prevPageButton.disabled = currentPage === 1;
            nextPageButton.disabled = currentPage * PAGE_SIZE >= allLogs.length;
        }

        async function fetchLogs() {
            try {
                const response = await fetch('adguard_proxy.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                const data = result.data;

                if (Array.isArray(data)) {
                    if (data.length === 0) {
                        allLogs = [];
                        currentPage = 1;
                        displayLogs([]);
                    } else {
                        allLogs = data;
                        currentPage = 1;
                        const paginatedLogs = paginateLogs(allLogs, currentPage, PAGE_SIZE);
                        displayLogs(paginatedLogs);
                        updatePagination();
                    }
                } else {
                    throw new Error('Error: Expected an array but received: ' + JSON.stringify(data));
                }
                showNotification('Logs Updated successfully.', 'is-success');
            } catch (error) {
                console.error('Error fetching logs:', error);
                const logsContainer = document.getElementById('logs');
                logsContainer.innerHTML = '<p class="notification is-danger">Error fetching logs. Please try again later.</p>';
                showNotification('Error fetching logs. Please try again later.', 'is-danger');
            }
        }

        function handlePageChange(newPage) {
            if (newPage < 1 || newPage * PAGE_SIZE > allLogs.length) {
                return;
            }
            currentPage = newPage;
            const paginatedLogs = paginateLogs(allLogs, currentPage, PAGE_SIZE);
            displayLogs(paginatedLogs);
            updatePagination();
        }

        function showNotification(message, type) {
            const notification = document.getElementById('status-notification');
            const notificationMessage = document.getElementById('notification-message');
            
            notificationMessage.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            notification.style.opacity = 1;

            setTimeout(() => {
                notification.style.opacity = 0;
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 500);
            }, 3000);
        }

        function closeNotification() {
            const notification = document.getElementById('status-notification');
            notification.style.opacity = 0;
            setTimeout(() => {
                notification.style.display = 'none';
            }, 500);
        }

        document.getElementById('prev-page').addEventListener('click', () => {
            handlePageChange(currentPage - 1);
        });

        document.getElementById('next-page').addEventListener('click', () => {
            handlePageChange(currentPage + 1);
        });
        fetchLogs();
    </script>

</body>
</html>