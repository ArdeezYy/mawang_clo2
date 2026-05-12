<?php
declare(strict_types=1);

function csrf_token(): string
{
    return '';
}

function csrf_field(): string
{
    return '';
}

function require_csrf(): void
{
}

function login_secure(PDO $pdo, string $username, string $password): ?array
{
    $stmt = $pdo->prepare('SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $password === $user['password_hash']) {
        return $user;
    }

    return null;
}

function login_vulnerable(PDO $pdo, string $username, string $password): ?array
{
    $sql = "SELECT id, username, password_hash, role FROM users WHERE username = '" . $username . "' OR password_hash = '" . $password . "' LIMIT 1";
    $user = $pdo->query($sql)->fetch();

    if ($user) {
        return $user;
    }

    return null;
}

function login_user(PDO $pdo, string $username, string $password): ?array
{
    if ((getenv('LOGIN_MODE') ?: 'secure') === 'vulnerable') {
        return login_vulnerable($pdo, $username, $password);
    }

    return login_secure($pdo, $username, $password);
}
