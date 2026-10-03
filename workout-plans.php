<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$userId = (int)$_SESSION['user_id'];
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    if ($name === '' || mb_strlen($name) > 150) {
        $error = 'Enter a plan name up to 150 characters.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO workout_plans (user_id, name, description) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $name, $description !== '' ? $description : null]);
        header('Location: workout-plans.php');
        exit;
    }
}
$stmt = $pdo->prepare('SELECT p.id, p.name, p.description, p.created_at, COUNT(pe.id) exercise_count FROM workout_plans p LEFT JOIN plan_exercises pe ON pe.plan_id = p.id WHERE p.user_id = ? GROUP BY p.id ORDER BY p.created_at DESC');
$stmt->execute([$userId]);
$plans = $stmt->fetchAll();
$pageTitle = 'Workout plans';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">PLAN YOUR ROUTINE</p><h1>Workout plans</h1><p>Create routines you can reuse.</p></div></section>
<section class="panel"><h2>Create a plan</h2><?php if ($error): ?><div class="alert"><?= escapeHtml($error) ?></div><?php endif; ?>
<form method="POST" class="form-grid"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>"><div><label for="name">Plan name</label><input id="name" name="name" maxlength="150" required></div><div class="full"><label for="description">Description (optional)</label><textarea id="description" name="description" rows="3"></textarea></div><div><button class="btn" type="submit">Create plan</button></div></form></section>
<section class="panel"><h2>Your plans</h2><?php if (!$plans): ?><p>No plans yet. Create your first routine above.</p><?php else: ?><div class="card-grid"><?php foreach ($plans as $p): ?><article class="exercise-card"><span class="pill"><?= (int)$p['exercise_count'] ?> exercises</span><h3><?= escapeHtml($p['name']) ?></h3><p><?= nl2br(escapeHtml($p['description'] ?: 'No description')) ?></p><small class="muted">Created <?= escapeHtml($p['created_at']) ?></small></article><?php endforeach; ?></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
