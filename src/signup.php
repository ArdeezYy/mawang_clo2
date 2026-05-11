<?php
require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $reserved = ['admin', 'root'];

    if (!valid_username($username)) {
        flash('error', 'Username harus 3-32 karakter dan hanya boleh huruf, angka, atau underscore.');
        redirect('/signup.php');
    }

    if (in_array(strtolower($username), $reserved, true)) {
        flash('error', 'Username tersebut tidak boleh didaftarkan dari sign up publik.');
        redirect('/signup.php');
    }

    $errors = password_policy_errors($password);
    if ($errors) {
        flash('error', 'Password harus ' . implode(', ', $errors) . '.');
        redirect('/signup.php');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)');
        $stmt->execute([$username, $hash, 'user']);
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
    <p class="meta">Gunakan username yang mudah dikenali dan password yang kuat.</p>
    <form class="stack" method="post" action="/signup.php">
        <?= csrf_field() ?>
        <div class="field">
            <label for="username">Username</label>
            <input id="username" name="username" maxlength="32" pattern="[A-Za-z0-9_]{3,32}" required autocomplete="username">
            <span class="help">3-32 karakter: huruf, angka, dan underscore.</span>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" maxlength="128" required autocomplete="new-password">
            <ul class="checklist">
                <li data-rule="length">Minimal 8 karakter</li>
                <li data-rule="upper">Ada huruf besar</li>
                <li data-rule="lower">Ada huruf kecil</li>
                <li data-rule="number">Ada angka</li>
                <li data-rule="symbol">Ada simbol</li>
            </ul>
        </div>
        <div class="button-row">
            <button id="signup-submit" type="submit">Buat akun</button>
            <button class="secondary" type="button" id="toggle-password">Tampilkan password</button>
        </div>
    </form>
</section>
<script src="/assets/app.js"></script>
<script>
wirePasswordToggle('toggle-password', 'password');
wireSignupChecklist();
</script>
<?php require __DIR__ . '/templates/footer.php'; ?>
