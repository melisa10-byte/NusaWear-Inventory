<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
function require_login(): void {
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}
function require_admin(): void {
    require_login();
    if (($_SESSION['user']['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('403 Forbidden — halaman ini hanya untuk admin.');
    }
}
function current_user(): array {
    return $_SESSION['user'] ?? [];
}
