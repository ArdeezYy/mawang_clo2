<?php
require_once __DIR__ . '/includes/bootstrap.php';

$user = require_login($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $body = trim((string) ($_POST['body'] ?? ''));

    if ($body === '') {
        flash('error', 'Komentar wajib diisi.');
        redirect('/comment.php');
    }

    $sql = "INSERT INTO comments (user_id, body) VALUES (" . (int) $user['id'] . ", '" . $body . "')";
    $pdo->exec($sql);

    flash('success', 'Komentar berhasil disimpan.');
    redirect('/');
}

require __DIR__ . '/templates/header.php';
?>
<section class="panel">
    <h1>Tulis komentar</h1>
    <p class="meta">Tulis komentar ringkas agar mudah dibaca oleh semua orang.</p>
    <form class="stack" method="post" action="/comment.php">
        <?= csrf_field() ?>
        <div class="field">
            <label for="body">Komentar</label>
            <textarea id="body" name="body" required></textarea>
        </div>
        <button type="submit">Kirim komentar</button>
    </form>
</section>
<?php require __DIR__ . '/templates/footer.php'; ?>
