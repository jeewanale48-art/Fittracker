<?php
require_once __DIR__ . '/includes/init.php';
if (!empty($_SESSION['user_id'])) {
    redirect(($_SESSION['role'] ?? '') === 'admin' ? 'admin/dashboard.php' : 'dashboard.php');
}
$notice = takeFlash();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>FitTrack | Choose your portal</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<header class="site-header"><div class="wrap header-inner"><a class="brand" href="index.php"><span class="brand-mark">✣</span>FitTrack</a><nav class="small-nav"><a href="#portals">Choose portal</a><a href="login.php">Sign in</a></nav></div></header>
<section class="hero"><div class="wrap hero-grid"><div><p class="eyebrow">Fitness, made personal</p><h1>Build healthy habits.<br>Track every win.</h1><p>One simple place to manage workouts, body measurements, goals, nutrition, and your fitness journey.</p><a class="button" href="#portals">Choose your portal ↓</a></div><div class="hero-art" aria-hidden="true"><div class="art-symbol">✣</div></div></div></section>
<main id="portals" class="wrap section">
<?php if ($notice): ?><div class="alert <?= e($notice['type']) ?>"><?= e($notice['message']) ?></div><?php endif; ?>
<p class="eyebrow">Get started</p><h2 class="section-title">Who is using FitTrack?</h2><p class="section-sub">Choose the portal that matches your role. Admin accounts and user accounts have separate access.</p>
<div class="role-grid">
<article class="role-card"><div class="role-icon">♙</div><h3>I'm a User</h3><p>Log workouts, track your weight and measurements, set goals, and keep an eye on your progress.</p><div class="role-actions"><a class="button" href="login.php?role=user">User sign in</a><a class="button light" href="register.php?role=user">Create user account</a></div></article>
<article class="role-card"><div class="role-icon admin">⚙</div><h3>I'm an Admin</h3><p>Manage registered accounts, review application statistics, and maintain the exercise library.</p><div class="role-actions"><a class="button" href="login.php?role=admin">Admin sign in</a><a class="button light" href="register.php?role=admin">Register admin</a></div></article>
</div>
</main><footer class="footer">FitTrack · Your progress, your pace.</footer>
</body></html>
