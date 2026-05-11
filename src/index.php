<?php
require_once __DIR__ . '/includes/bootstrap.php';

$stmt = $pdo->query(
    'SELECT comments.body, comments.created_at, users.username
     FROM comments
     JOIN users ON users.id = comments.user_id
     ORDER BY comments.created_at DESC, comments.id DESC
     LIMIT 50'
);
$comments = $stmt->fetchAll();

require __DIR__ . '/templates/header.php';
?>
<section class="hero">
    <h1>Papan komentar publik yang diamankan</h1>
    <p>Demo CLO 2 target 80 poin: HTTPS/TLS, hash password dengan salt, dan pembatasan input untuk mitigasi buffer overflow/input berlebih.</p>
</section>

<section class="grid">
    <div class="panel">
        <h2>Kontrol aktif</h2>
        <p class="meta">Transport web memakai HTTPS, password disimpan sebagai hash bcrypt bersalt, dan komentar dibatasi maksimal 500 karakter di server.</p>
        <a class="button" href="/comment.php">Tulis komentar</a>
    </div>
    <div class="panel">
        <h2>Uji buffer overflow</h2>
        <p class="meta">Kirim komentar lebih dari 500 karakter untuk melihat server menolak input yang terlalu panjang.</p>
    </div>
</section>

<section class="stack" style="margin-top: 22px;">
    <h2>Komentar terbaru</h2>
    <?php if (!$comments): ?>
        <div class="comment"><p>Belum ada komentar. Login lalu tulis komentar pertama.</p></div>
    <?php endif; ?>
    <?php foreach ($comments as $comment): ?>
        <article class="comment">
            <header>
                <strong><?= e($comment['username']) ?></strong>
                <span><?= e($comment['created_at']) ?></span>
            </header>
            <p><?= e($comment['body']) ?></p>
        </article>
    <?php endforeach; ?>
</section>
<?php require __DIR__ . '/templates/footer.php'; ?>
