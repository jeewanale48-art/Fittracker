<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';

if (!empty($_SESSION['user_id'])) {
    redirect(($_SESSION['role'] ?? '') === 'admin' ? 'admin/dashboard.php' : 'dashboard.php');
}
$role = ($_GET['role'] ?? $_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Email or password is incorrect.';
        } elseif ($user['role'] !== $role) {
            $error = $role === 'admin'
                ? 'This account is not an administrator. Choose User sign in instead.'
                : 'This account is an administrator. Choose Admin sign in instead.';
        } else {
            signInUser($user);
            redirect($role === 'admin' ? 'admin/dashboard.php' : 'dashboard.php');
        }
    }
}
$notice = takeFlash();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $role === 'admin' ? 'Admin' : 'User' ?> Sign In | FitTrack</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body><header class="site-header"><div class="wrap header-inner"><a class="brand" href="index.php"><span class="brand-mark">✣</span>FitTrack</a><a class="small-nav" href="index.php">← Change portal</a></div></header>
<main class="auth-layout"><section class="auth-card"><p class="eyebrow"><?= $role === 'admin' ? 'Administrator portal' : 'Member portal' ?></p><h1>Welcome back</h1><p class="intro">Sign in to your <?= $role === 'admin' ? 'admin workspace' : 'personal fitness dashboard' ?>.</p>
<?php if ($notice): ?><div class="alert <?= e($notice['type']) ?>"><?= e($notice['message']) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post" action="login.php?role=<?= e($role) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="role" value="<?= e($role) ?>">
<div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="email" value="<?= e($email) ?>" required></div>
<div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
<button class="button full" type="submit">Sign in as <?= $role === 'admin' ? 'Admin' : 'User' ?> →</button></form>
<p class="form-foot">Don't have an account? <a href="register.php?role=<?= e($role) ?>">Register here</a></p><p class="form-foot"><a href="index.php">← Back to portal selection</a></p>
</section></main></body></html>
