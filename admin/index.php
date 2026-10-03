<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$users = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$exercises = (int)$pdo->query('SELECT COUNT(*) FROM exercises')->fetchColumn();
$pageTitle = 'Admin';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">SYSTEM MANAGEMENT</p><h1>Admin dashboard</h1><p>Manage shared exercise data and review system totals.</p></div></section>
<section class="stats-grid"><article class="stat-card"><p>Registered users</p><h2><?= $users ?></h2></article><article class="stat-card"><p>Exercises</p><h2><?= $exercises ?></h2></article></section>
<section class="panel"><h2>Administration</h2><p><a class="btn" href="exercises.php">Manage exercises</a> <a class="btn btn-secondary" href="users.php">View users</a></p></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
