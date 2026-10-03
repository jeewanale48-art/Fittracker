<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$userId = (int)$_SESSION['user_id'];
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrf();
    $title = trim((string)($_POST['title'] ?? ''));
    $type = (string)($_POST['goal_type'] ?? 'other');
    $targetRaw = trim((string)($_POST['target_value'] ?? ''));
    $start = (string)($_POST['start_date'] ?? date('Y-m-d'));
    $deadline = trim((string)($_POST['deadline'] ?? ''));
    $unit = trim((string)($_POST['unit'] ?? ''));
    $allowed = ['weight', 'workouts', 'duration', 'steps', 'custom'];
    $target = filter_var($targetRaw, FILTER_VALIDATE_FLOAT);
    $startDate = DateTime::createFromFormat('!Y-m-d', $start);
    $deadlineDate = $deadline === '' ? null : DateTime::createFromFormat('!Y-m-d', $deadline);
    if ($title === '' || mb_strlen($title) > 150) $error = 'Enter a goal title up to 150 characters.';
    elseif (!in_array($type, $allowed, true)) $error = 'Choose a valid goal type.';
    elseif ($target === false || $target <= 0) $error = 'Target must be a positive number.';
    elseif (!$startDate || $startDate->format('Y-m-d') !== $start) $error = 'Enter a valid start date.';
    elseif ($deadline !== '' && (!$deadlineDate || $deadlineDate->format('Y-m-d') !== $deadline || $deadline < $start)) $error = 'Deadline must be a valid date on or after the start date.';
    else {
        $stmt = $pdo->prepare('INSERT INTO fitness_goals (user_id, title, goal_type, target_value, current_value, unit, start_date, target_date) VALUES (?, ?, ?, ?, 0, ?, ?, ?)');
        $stmt->execute([$userId, $title, $type, $target, $unit !== '' ? $unit : null, $start, $deadline !== '' ? $deadline : null]);
        header('Location: goals.php'); exit;
    }
}
$stmt = $pdo->prepare('SELECT id, title, goal_type, target_value, current_value, unit, start_date, target_date, status, created_at FROM fitness_goals WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$userId]);
$goals = $stmt->fetchAll();
$pageTitle = 'Goals';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">SMALL STEPS, BIG PROGRESS</p><h1>Fitness goals</h1><p>Choose measurable targets to work toward.</p></div></section>
<section class="panel"><h2>Create a goal</h2><?php if ($error): ?><div class="alert"><?= escapeHtml($error) ?></div><?php endif; ?>
<form method="POST" class="form-grid"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
<div><label for="title">Goal title</label><input id="title" name="title" maxlength="150" required></div>
<div><label for="goal_type">Goal type</label><select id="goal_type" name="goal_type"><option value="weight">Target weight</option><option value="workouts">Workout frequency</option><option value="duration">Workout minutes</option><option value="steps">Steps</option><option value="custom">Custom</option></select></div>
<div><label for="target_value">Target value</label><input id="target_value" name="target_value" type="number" step="0.01" min="0.01" required></div>
<div><label for="unit">Unit (optional)</label><input id="unit" name="unit" maxlength="30" placeholder="kg, sessions/week, minutes"></div>
<div><label for="start_date">Start date</label><input id="start_date" name="start_date" type="date" value="<?= date('Y-m-d') ?>" required></div>
<div><label for="deadline">Deadline (optional)</label><input id="deadline" name="deadline" type="date"></div>
<div><button class="btn" type="submit">Create goal</button></div></form></section>
<section class="panel"><h2>Your goals</h2><?php if (!$goals): ?><p>No goals yet.</p><?php else: ?><div class="card-grid"><?php foreach ($goals as $g): ?><article class="exercise-card"><span class="pill"><?= escapeHtml(str_replace('_', ' ', $g['status'])) ?></span><h3><?= escapeHtml($g['title']) ?></h3><p>Target: <strong><?= escapeHtml((string)$g['target_value']) ?> <?= escapeHtml($g['unit'] ?: '') ?></strong></p><p class="muted"><?= escapeHtml(str_replace('_', ' ', $g['goal_type'])) ?></p><small class="muted">Start: <?= escapeHtml($g['start_date']) ?> · Deadline: <?= escapeHtml($g['target_date'] ?: 'Not set') ?></small></article><?php endforeach; ?></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
