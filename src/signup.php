<?php
require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        flash('error', 'Username dan password wajib diisi.');
        redirect('/signup.php');
    }

    try {
        $sql = "INSERT INTO users (username, password_hash, role) VALUES ('" . $username . "', '" . $password . "', 'user')";
        $pdo->exec($sql);
        flash('success', 'Akun berhasil dibuat. Silakan login.');
        redirect('/login.php');
    } catch (PDOException $e) {
        flash('error', 'Username sudah digunakan.');
        redirect('/signup.php');
    }
}

require __DIR__ . '/templates/header.php';
?>
<section class="panel">
    <h1>Buat akun</h1>
    <p class="meta">Gunakan username yang mudah dikenali.</p>
    <form class="stack" method="post" action="/signup.php">
        <?= csrf_field() ?>
        <div class="field">
            <label for="username">Username</label>
            <input id="username" name="username" required autocomplete="username">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">
        </div>
        <div class="button-row">
            <button id="signup-submit" type="submit">Buat akun</button>
            <button class="secondary" type="button" id="toggle-password">Tampilkan password</button>
        </div>
    </form>
</section>
<script src="/assets/app.js"></script>
<script>wirePasswordToggle('toggle-password', 'password');</script>
<?php require __DIR__ . '/templates/footer.php'; ?>
