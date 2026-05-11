<?php
require_once __DIR__ . '/includes/bootstrap.php';

$mode = getenv('LOGIN_MODE') ?: 'secure';
$usernamePattern = $mode === 'secure' ? ' pattern="[A-Za-z0-9_]{3,32}"' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $usernameInvalid = $mode === 'secure'
        ? !valid_username($username)
        : ($username === '' || strlen($username) > 128);

    if ($usernameInvalid || strlen($password) > 128) {
        sleep(2);
        flash('error', 'Username atau password tidak valid.');
        redirect('/login.php');
    }

    try {
        $user = login_user($pdo, $username, $password);
    } catch (Throwable $e) {
        sleep(2);
        $user = null;
    }

    if ($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        flash('success', 'Login berhasil.');
        redirect('/');
    }

    flash('error', 'Login gagal. Periksa username dan password.');
    redirect('/login.php');
}

require __DIR__ . '/templates/header.php';
?>
<section class="panel">
    <h1>Login</h1>
    <p class="meta">Akun demo admin: <code>admin</code> / <code>Admin@240!</code>.</p>
    <form class="stack" method="post" action="/login.php">
        <?= csrf_field() ?>
        <div class="field">
            <label for="username">Username</label>
            <input id="username" name="username" maxlength="128"<?= $usernamePattern ?> required autocomplete="username">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" maxlength="128" required autocomplete="current-password">
        </div>
        <div class="button-row">
            <button type="submit">Login</button>
            <button class="secondary" type="button" id="toggle-password">Tampilkan password</button>
        </div>
    </form>
</section>
<script src="/assets/app.js"></script>
<script>wirePasswordToggle('toggle-password', 'password');</script>
<?php require __DIR__ . '/templates/footer.php'; ?>
