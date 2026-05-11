<?php
$user = current_user($pdo);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Secure Comments</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="site-header">
    <nav class="nav">
        <a class="brand" href="/">Secure Comments</a>
        <div class="nav-links">
            <a href="/">Komentar</a>
            <?php if ($user): ?>
                <a href="/comment.php">Tulis</a>
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                    <a href="/admin.php">Admin</a>
                <?php endif; ?>
                <a href="/logout.php">Logout (<?= e($user['username']) ?>)</a>
            <?php else: ?>
                <a href="/login.php">Login</a>
                <a href="/signup.php">Sign up</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
<main class="container">
<?php foreach (flashes() as $item): ?>
    <div class="alert <?= e($item['type']) ?>"><?= e($item['message']) ?></div>
<?php endforeach; ?>
