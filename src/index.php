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
    <div>
        <span class="eyebrow">Ruang diskusi publik</span>
        <h1>Bagikan pendapat dengan singkat dan jelas.</h1>
        <p>Baca komentar terbaru dari komunitas, masuk ke akunmu, lalu ikut menulis.</p>
    </div>
    <div class="hero-actions">
        <a class="button" href="/comment.php">Tulis komentar</a>
        <?php if (!$user): ?>
            <a class="button secondary" href="/signup.php">Buat akun</a>
        <?php endif; ?>
    </div>
</section>

<section class="summary-bar">
    <div>
        <strong><?= e((string) count($comments)) ?></strong>
        <span>Komentar terbaru</span>
    </div>
    <div>
        <strong>500</strong>
        <span>Karakter maksimal</span>
    </div>
</section>

<section class="stack comments-section">
    <h2>Komentar terbaru</h2>
    <?php if (!$comments): ?>
        <div class="empty-state">
            <h3>Belum ada komentar</h3>
            <p>Jadilah orang pertama yang membuka percakapan.</p>
            <a class="button" href="/comment.php">Tulis komentar</a>
        </div>
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
