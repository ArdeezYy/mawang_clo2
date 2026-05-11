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
    <p>Demo CLO 2 untuk HTTPS, hash password dengan salt, proteksi SQL injection, mitigasi XSS, CSRF token, validasi input, dan session hardening.</p>
</section>

<section class="grid">
    <div class="panel">
        <h2>Kontrol aktif</h2>
        <p class="meta">Semua output komentar di-escape, semua form POST memakai token CSRF, dan login aman memakai prepared statement saat mode secure.</p>
        <a class="button" href="/comment.php">Tulis komentar</a>
    </div>
    <div class="panel">
        <h2>Uji serangan</h2>
        <p class="meta">Coba login dengan payload <code>' OR '1'='1</code> dan komentar <code>&lt;script&gt;alert(1)&lt;/script&gt;</code> untuk melihat mitigasi bekerja.</p>
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
            <p><?= comment_body($comment['body']) ?></p>
        </article>
    <?php endforeach; ?>
</section>
<?php require __DIR__ . '/templates/footer.php'; ?>
