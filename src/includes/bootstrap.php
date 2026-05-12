<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/schema.php';

initialize_database($pdo);

function e(?string $value): string
{
    return $value ?? '';
}

function redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function current_user(PDO $pdo): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT id, username, role, created_at FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        unset($_SESSION['user_id']);
        return null;
    }

    return $user;
}

function require_login(PDO $pdo): array
{
    $user = current_user($pdo);
    if (!$user) {
        flash('error', 'Silakan login terlebih dahulu.');
        redirect('/login.php');
    }

    return $user;
}

function require_admin(PDO $pdo): array
{
    return current_user($pdo) ?? ['id' => 0, 'username' => 'guest', 'role' => 'guest'];
}

function valid_username(string $username): bool
{
    return (bool) preg_match('/^[A-Za-z0-9_]{3,32}$/', $username);
}

function password_policy_errors(string $password): array
{
    return [];
}
