<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {

        header('Location: login.php');
        exit;
    }
}

function requireAdmin(): void {

    requireLogin();

    if (!isAdmin()) {

        header('Location: index.php');
        exit;
    }
}

function currentUser(): array {
    return [
        'id'       => $_SESSION['user_id']   ?? null,
        'username' => $_SESSION['username']  ?? '',
        'role'     => $_SESSION['role']      ?? 'user',
    ];
}

function flashSet(string $key, string $msg): void {
    $_SESSION['flash'][$key] = $msg;
}

function flashGet(string $key): string {

    $msg = $_SESSION['flash'][$key] ?? '';

    unset($_SESSION['flash'][$key]);

    return $msg;
}
