<?php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_secure', '1');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/schema.php';

initialize_database($pdo);

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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
    $user = require_login($pdo);
    if (($user['role'] ?? '') !== 'admin') {
        http_response_code(403);
        require __DIR__ . '/../templates/header.php';
        echo '<section class="panel"><h1>Akses ditolak</h1><p>Halaman admin hanya untuk akun dengan role admin.</p></section>';
        require __DIR__ . '/../templates/footer.php';
        exit;
    }

    return $user;
}

function valid_username(string $username): bool
{
    return (bool) preg_match('/^[A-Za-z0-9_]{3,32}$/', $username);
}

function password_policy_errors(string $password): array
{
    $errors = [];
    if (strlen($password) < 8) {
        $errors[] = 'minimal 8 karakter';
    }
    if (strlen($password) > 128) {
        $errors[] = 'maksimal 128 karakter';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'memiliki huruf besar';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'memiliki huruf kecil';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'memiliki angka';
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'memiliki simbol';
    }

    return $errors;
}
