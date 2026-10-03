
<?php
require_once __DIR__ . '/auth.php';

$pageTitle = $pageTitle ?? 'FitTrack';
$baseUrl = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') ? '../' : '';
$assetBase = $baseUrl . 'assets/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escapeHtml($pageTitle) ?> | FitTrack</title>
<link rel="stylesheet" href="<?= $assetBase ?>css/style.css">
</head>
<body>

<header class="site-header"><div class="wrap header-inner">
    <a class="brand" href="<?= $baseUrl ?>index.php"><span class="brand-mark">✣</span>FitTrack</a>

    <nav class="small-nav">
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="<?= $baseUrl ?>dashboard.php">Dashboard</a>
            <a href="<?= $baseUrl ?>exercises.php">Exercises</a>
            <a href="<?= $baseUrl ?>workout-plans.php">Plans</a>
            <a href="<?= $baseUrl ?>workouts.php">Workouts</a>
            <a href="<?= $baseUrl ?>goals.php">Goals</a>
            <a href="<?= $baseUrl ?>body_measurements.php">Measurements</a>
            <a href="<?= $baseUrl ?>nutrition.php">Nutrition</a>
            <a href="<?= $baseUrl ?>water.php">Water</a>
            <a href="<?= $baseUrl ?>progress.php">Progress</a>
            <a href="<?= $baseUrl ?>profile.php">Profile</a>
            <?php if (($_SESSION['role'] ?? '') === 'user'): ?><a href="<?= $baseUrl ?>subscription.php">Subscription</a><?php endif; ?>

            <?php if (($_SESSION['role'] ?? $_SESSION['user_role'] ?? '') === 'admin'): ?>
                <a href="<?= $baseUrl ?>admin/dashboard.php">Admin</a>
            <?php endif; ?>

            <span class="nav-user">
                <?= escapeHtml($_SESSION['user_name'] ?? 'User') ?>
            </span>

            <form class="logout-form" method="POST" action="<?= $baseUrl ?>logout.php">
                <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
                <button class="btn btn-small" type="submit">Logout</button>
            </form>
        <?php else: ?>
            <a href="<?= $baseUrl ?>login.php">Login</a>
            <a class="btn btn-small" href="<?= $baseUrl ?>register.php">Register</a>
        <?php endif; ?>
    </nav>
</div></header>

<main class="wrap section legacy-content">
