<?php
require_once __DIR__ . '/includes/bootstrap.php';

require_admin($pdo);

$users = $pdo->query('SELECT id, username, password_hash, role, created_at FROM users ORDER BY id ASC')->fetchAll();
$comments = $pdo->query(
    'SELECT comments.id, comments.body, comments.created_at, users.username
     FROM comments
     JOIN users ON users.id = comments.user_id
     ORDER BY comments.id DESC'
)->fetchAll();

require __DIR__ . '/templates/header.php';
?>
<section class="hero">
    <h1>Admin monitoring</h1>
    <p>Panel ini hanya bisa diakses role admin dan memakai query database yang aman. Password plaintext tidak disimpan; kolom password menampilkan hash bcrypt yang sudah memuat salt.</p>
</section>

<div class="grid">
    <section class="table-wrap">
        <h2>Users</h2>
        <table>
            <thead>
                <tr><th>ID</th><th>Username</th><th>Password hash</th><th>Role</th><th>Dibuat</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $row): ?>
                    <tr>
                        <td><?= e((string) $row['id']) ?></td>
                        <td><?= e($row['username']) ?></td>
                        <td><code><?= e($row['password_hash']) ?></code></td>
                        <td><?= e($row['role']) ?></td>
                        <td><?= e($row['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="table-wrap">
        <h2>Comments</h2>
        <table>
            <thead>
                <tr><th>ID</th><th>User</th><th>Komentar</th><th>Dibuat</th></tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $row): ?>
                    <tr>
                        <td><?= e((string) $row['id']) ?></td>
                        <td><?= e($row['username']) ?></td>
                        <td><?= e($row['body']) ?></td>
                        <td><?= e($row['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
<?php require __DIR__ . '/templates/footer.php'; ?>
