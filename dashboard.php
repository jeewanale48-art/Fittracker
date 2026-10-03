<?php
require_once __DIR__ . '/includes/auth.php';
requireRole('user');

$userId = (int) $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Member';
$totalWorkouts = $totalMinutes = $weeklyWorkouts = 0;
$latestWeight = null;
$recentWorkouts = [];
$latestGoal = null;
$loadError = false;

try {
    $stmt = $pdo->prepare('SELECT COUNT(*) AS total_workouts, COALESCE(SUM(duration_minutes),0) AS total_minutes FROM workout_sessions WHERE user_id = ?');
    $stmt->execute([$userId]);
    $stats = $stmt->fetch();
    $totalWorkouts = (int) $stats['total_workouts'];
    $totalMinutes = (int) $stats['total_minutes'];

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM workout_sessions WHERE user_id = ? AND workout_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE()');
    $stmt->execute([$userId]);
    $weeklyWorkouts = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT weight_kg, measured_on FROM body_measurements WHERE user_id = ? AND weight_kg IS NOT NULL ORDER BY measured_on DESC, id DESC LIMIT 1');
    $stmt->execute([$userId]);
    $latestWeight = $stmt->fetch() ?: null;

    $stmt = $pdo->prepare('SELECT id, title, workout_date, duration_minutes, notes FROM workout_sessions WHERE user_id = ? ORDER BY workout_date DESC, id DESC LIMIT 5');
    $stmt->execute([$userId]);
    $recentWorkouts = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT title, target_value, current_value, unit, target_date, status FROM fitness_goals WHERE user_id = ? AND status = 'active' ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$userId]);
    $latestGoal = $stmt->fetch() ?: null;
} catch (PDOException $exception) {
    error_log('FitTrack user dashboard: ' . $exception->getMessage());
    $loadError = true;
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>User Dashboard | FitTrack</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<div class="app-shell"><aside class="sidebar"><a class="brand" href="dashboard.php"><span class="brand-mark">✣</span>FitTrack</a><nav class="nav-list"><a class="active" href="dashboard.php"><span class="ico">⌂</span>Dashboard</a><a href="workouts.php"><span class="ico">⚒</span>Workouts</a><a href="workout_plans.php"><span class="ico">▤</span>Workout Plans</a><a href="body_measurements.php"><span class="ico">♙</span>Body Measurements</a><a href="goals.php"><span class="ico">◎</span>Fitness Goals</a><a href="nutrition.php"><span class="ico">♜</span>Nutrition</a><a href="water.php"><span class="ico">♢</span>Water Log</a></nav><div class="side-bottom nav-list"><a href="logout.php"><span class="ico">↪</span>Sign out</a></div></aside>
<main class="main-area"><header class="topbar"><h2>Your Dashboard</h2><div class="user-chip"><span class="avatar"><?= e(strtoupper(substr($userName,0,1))) ?></span><?= e($userName) ?><a class="button light" href="logout.php">Sign out</a></div></header><div class="content">
<section class="welcome"><p class="eyebrow">Your fitness journey</p><h1>Welcome back, <?= e($userName) ?>!</h1><p>Stay consistent. Small steps lead to big results.</p></section>
<?php if ($loadError): ?><div class="alert error">Some dashboard data could not be loaded. Check your database schema and PHP error log.</div><?php endif; ?>
<section class="stats"><article class="stat"><div class="stat-label"><span class="stat-icon">⚒</span>Total workouts</div><div class="stat-value"><?= $totalWorkouts ?></div><span class="muted">All recorded workouts</span></article><article class="stat"><div class="stat-label"><span class="stat-icon">◷</span>Workout minutes</div><div class="stat-value"><?= $totalMinutes ?></div><span class="muted">Total recorded duration</span></article><article class="stat"><div class="stat-label"><span class="stat-icon">▦</span>Last 7 days</div><div class="stat-value"><?= $weeklyWorkouts ?></div><span class="muted">Workouts this week</span></article><article class="stat"><div class="stat-label"><span class="stat-icon">⚖</span>Latest weight</div><div class="stat-value"><?= $latestWeight ? e($latestWeight['weight_kg']) . ' <small style="font-size:14px">kg</small>' : '—' ?></div><span class="muted"><?= $latestWeight ? 'Measured ' . e($latestWeight['measured_on']) : 'No measurement yet' ?></span></article></section>
<div class="cols"><section class="panel"><div class="panel-head"><h3>Recent workouts</h3><a class="button light" href="workouts.php">View all</a></div><p class="panel-desc">Your latest training sessions</p><?php if ($recentWorkouts): ?><div class="table-wrap"><table><thead><tr><th>Date</th><th>Workout</th><th>Duration</th></tr></thead><tbody><?php foreach ($recentWorkouts as $workout): ?><tr><td><?= e($workout['workout_date']) ?></td><td><strong><?= e($workout['title']) ?></strong><?php if (!empty($workout['notes'])): ?><br><span class="muted"><?= e($workout['notes']) ?></span><?php endif; ?></td><td><?= $workout['duration_minutes'] !== null ? (int)$workout['duration_minutes'] . ' min' : '—' ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p class="empty">No workouts recorded yet. Start with your first workout.</p><?php endif; ?></section>
<div><section class="panel"><h3>Quick actions</h3><p class="panel-desc">What would you like to do today?</p><div class="quick-grid"><a class="quick" href="workouts.php"><span>⚒ Log workout</span><span>→</span></a><a class="quick" href="body_measurements.php"><span>♙ Add measurements</span><span>→</span></a><a class="quick" href="goals.php"><span>◎ Set a goal</span><span>→</span></a><a class="quick" href="nutrition.php"><span>♜ Log food</span><span>→</span></a></div></section>
<section class="panel"><h3>Weekly workout target</h3><p class="panel-desc">Suggested target: 5 sessions per week</p><div class="progress"><span style="width:<?= min(100,($weeklyWorkouts/5)*100) ?>%"></span></div><div class="progress-meta"><span><?= $weeklyWorkouts ?> completed</span><strong><?= max(0,5-$weeklyWorkouts) ?> to go</strong></div></section>
<section class="panel"><h3>Active goal</h3><?php if ($latestGoal): ?><p><strong><?= e($latestGoal['title']) ?></strong></p><p class="panel-desc"><?= e($latestGoal['current_value']) ?> / <?= e($latestGoal['target_value'] ?? '—') ?> <?= e($latestGoal['unit'] ?? '') ?></p><?php else: ?><p class="empty">You have no active goal yet.</p><?php endif; ?><a class="button light" href="goals.php">Manage goals</a></section></div></div></div></main></div></body></html>
