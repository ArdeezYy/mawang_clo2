<?php
declare(strict_types=1);

function initialize_database(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(32) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS comments (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            body TEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec('ALTER TABLE comments MODIFY body TEXT NOT NULL');

    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $stmt->execute(['admin']);

    if (!$stmt->fetch()) {
        $insert = $pdo->prepare('INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)');
        $insert->execute(['admin', 'Admin@240!', 'admin']);
    } else {
        $update = $pdo->prepare('UPDATE users SET password_hash = ? WHERE username = ?');
        $update->execute(['Admin@240!', 'admin']);
    }

    $legacy = $pdo->query(
        "SELECT id, username FROM users
         WHERE username <> 'admin'
         AND (password_hash LIKE '\$2y\$%' OR password_hash LIKE '\$argon2%')"
    )->fetchAll();
    $updateLegacy = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    foreach ($legacy as $row) {
        $updateLegacy->execute(['password123', (int) $row['id']]);
    }

    $updateAdmin = $pdo->prepare('UPDATE users SET password_hash = ? WHERE username = ?');
    $updateAdmin->execute(['Admin@240!', 'admin']);
}
