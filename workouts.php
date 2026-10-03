<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$userId = (int)$_SESSION['user_id'];
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrf();
    $date = trim((string)($_POST['workout_date'] ?? ''));
    $durationRaw = trim((string)($_POST['duration_minutes'] ?? ''));
    $notes = trim((string)($_POST['notes'] ?? ''));
    $planRaw = trim((string)($_POST['plan_id'] ?? ''));
    $validDate = DateTime::createFromFormat('Y-m-d', $date);
    $duration = $durationRaw === '' ? null : filter_var($durationRaw, FILTER_VALIDATE_INT);
    $planId = $planRaw === '' ? null : filter_var($planRaw, FILTER_VALIDATE_INT);
    if (!$validDate || $validDate->format('Y-m-d') !== $date) {
        $error = 'Enter a valid workout date.';
    } elseif ($durationRaw !== '' && ($duration === false || $duration < 1 || $duration > 1440)) {
        $error = 'Duration must be between 1 and 1440 minutes.';
    } elseif ($notes !== '' && mb_strlen($notes) > 5000) {
        $error = 'Notes are too long.';
    } else {
        if ($planId !== null) {
            $check = $pdo->prepare('SELECT id FROM workout_plans WHERE id = ? AND user_id = ?');
            $check->execute([$planId, $userId]);
            if (!$check->fetch()) $error = 'Choose one of your own workout plans.';
        }
        if ($error === '') {
            $stmt = $pdo->prepare('INSERT INTO workout_sessions (user_id, plan_id, title, workout_date, duration_minutes, notes) VALUES (?, ?, ?, ?, ?, ?)');
            $title = trim((string)($_POST['title'] ?? 'Workout session'));
            if ($title === '') $title = 'Workout session';
            $stmt->execute([$userId, $planId, $title, $validDate->format('Y-m-d'), $duration ?: null, $notes !== '' ? $notes : null]);
            header('Location: workouts.php');
            exit;
        }
    }
}
$stmt = $pdo->prepare('SELECT id, name FROM workout_plans WHERE user_id = ? ORDER BY name');
$stmt->execute([$userId]);
$plans = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT s.id, s.title, s.workout_date, s.duration_minutes, s.notes, p.name plan_name FROM workout_sessions s LEFT JOIN workout_plans p ON p.id = s.plan_id WHERE s.user_id = ? ORDER BY s.workout_date DESC, s.id DESC LIMIT 100');
$stmt->execute([$userId]);
$sessions = $stmt->fetchAll();
$pageTitle = 'Workouts';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">ACTIVITY LOG</p><h1>Workout sessions</h1><p>Record each session and review your history.</p></div></section>
<section class="panel"><h2>Log a workout</h2><?php if ($error): ?><div class="alert"><?= escapeHtml($error) ?></div><?php endif; ?>
<form method="POST" class="form-grid"><input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
<div><label for="workout_date">Date and time</label><input id="workout_date" name="workout_date" type="date" value="<?= date('Y-m-d') ?>" required></div>
<div><label for="title">Workout title</label><input id="title" name="title" maxlength="150" value="Workout session" required></div><div><label for="duration_minutes">Duration (minutes)</label><input id="duration_minutes" name="duration_minutes" type="number" min="1" max="1440"></div>
<div><label for="plan_id">Workout plan (optional)</label><select id="plan_id" name="plan_id"><option value="">No plan</option><?php foreach ($plans as $p): ?><option value="<?= (int)$p['id'] ?>"><?= escapeHtml($p['name']) ?></option><?php endforeach; ?></select></div>
<div class="full"><label for="notes">Notes</label><textarea id="notes" name="notes" rows="3" maxlength="5000" placeholder="How did the workout go?"></textarea></div>
<div><button class="btn" type="submit">Save workout</button></div></form></section>
<section class="panel"><h2>Workout history</h2><?php if (!$sessions): ?><p>Your saved workouts will appear here.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Date</th><th>Plan</th><th>Duration</th><th>Notes</th></tr></thead><tbody><?php foreach ($sessions as $s): ?><tr><td><?= escapeHtml($s['workout_date']) ?></td><td><?= escapeHtml($s['title'] ?: ($s['plan_name'] ?: '—')) ?></td><td><?= $s['duration_minutes'] === null ? '—' : (int)$s['duration_minutes'].' min' ?></td><td><?= escapeHtml($s['notes'] ?: '—') ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
