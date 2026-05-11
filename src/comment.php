<?php
require_once __DIR__ . '/includes/bootstrap.php';

$user = require_login($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $body = trim((string) ($_POST['body'] ?? ''));
    $length = strlen($body);

    if ($length < 1 || $length > 500) {
        flash('error', 'Komentar wajib diisi dan maksimal 500 karakter.');
        redirect('/comment.php');
    }

    $stmt = $pdo->prepare('INSERT INTO comments (user_id, body) VALUES (?, ?)');
    $stmt->execute([(int) $user['id'], $body]);

    flash('success', 'Komentar berhasil disimpan.');
    redirect('/');
}

require __DIR__ . '/templates/header.php';
?>
<section class="panel">
    <h1>Tulis komentar</h1>
    <p class="meta">Komentar dibatasi 500 karakter dan akan ditampilkan dengan escaping HTML untuk mencegah XSS.</p>
    <form class="stack" method="post" action="/comment.php">
        <?= csrf_field() ?>
        <div class="field">
            <label for="body">Komentar</label>
            <textarea id="body" name="body" maxlength="500" required></textarea>
            <span class="help">Maksimal 500 karakter.</span>
        </div>
        <button type="submit">Kirim komentar</button>
    </form>
</section>
<?php require __DIR__ . '/templates/footer.php'; ?>
