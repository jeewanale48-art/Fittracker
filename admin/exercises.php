<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim((string)($_POST['name'] ?? ''));
        $category = trim((string)($_POST['category'] ?? 'General'));
        $muscle = trim((string)($_POST['muscle_group'] ?? ''));
        $difficulty = $_POST['difficulty'] ?? 'beginner';
        $equipment = trim((string)($_POST['equipment'] ?? ''));
        $instructions = trim((string)($_POST['instructions'] ?? ''));
        if ($name === '' || mb_strlen($name) > 150) {
            $error = 'Enter an exercise name up to 150 characters.';
        } elseif (!in_array($difficulty, ['beginner','intermediate','advanced'], true)) {
            $error = 'Choose a valid difficulty.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO exercises (name, category, muscle_group, difficulty, equipment, instructions) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $category ?: 'General', $muscle ?: null, $difficulty, $equipment ?: null, $instructions ?: null]);
            flash('Exercise added to the library.');
            redirect('exercises.php');
        }
    } elseif ($action === 'delete') {
        $id = filter_var($_POST['exercise_id'] ?? null, FILTER_VALIDATE_INT);
        if ($id) {
            try {
                $stmt = $pdo->prepare('DELETE FROM exercises WHERE id = ?');
                $stmt->execute([$id]);
                flash('Exercise deleted.');
            } catch (PDOException $exception) {
                flash('This exercise is used by a workout plan or workout set and cannot be deleted yet.', 'error');
            }
            redirect('exercises.php');
        }
    }
}
$exercises = $pdo->query('SELECT id, name, category, muscle_group, difficulty, equipment FROM exercises ORDER BY name')->fetchAll();
$notice = takeFlash();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Exercises | FitTrack Admin</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="app-shell"><aside class="sidebar"><a class="brand" href="dashboard.php"><span class="brand-mark">✣</span>FitTrack</a><nav class="nav-list"><a href="dashboard.php">⌂ Overview</a><a href="users.php">♙ Manage users</a><a class="active" href="exercises.php">⚒ Exercises</a></nav><div class="side-bottom nav-list"><a href="../logout.php">↪ Sign out</a></div></aside><main class="main-area"><header class="topbar"><h2>Exercise library</h2><a class="button light" href="dashboard.php">← Overview</a></header><div class="content"><section class="welcome admin-banner"><p class="eyebrow">Content management</p><h1>Manage exercises</h1><p>Add exercises to the shared library for workout plans.</p></section><?php if ($notice): ?><div class="alert <?= e($notice['type']) ?>"><?= e($notice['message']) ?></div><?php endif; ?><?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><div class="cols"><section class="panel"><h3>Add an exercise</h3><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="add"><div class="field"><label>Name</label><input name="name" maxlength="150" required></div><div class="field"><label>Category</label><input name="category" maxlength="80" value="General"></div><div class="field"><label>Muscle group</label><input name="muscle_group" maxlength="100"></div><div class="field"><label>Difficulty</label><select name="difficulty"><option value="beginner">Beginner</option><option value="intermediate">Intermediate</option><option value="advanced">Advanced</option></select></div><div class="field"><label>Equipment</label><input name="equipment" maxlength="120"></div><div class="field"><label>Instructions</label><textarea name="instructions" rows="4"></textarea></div><button class="button" type="submit">Add exercise</button></form></section><section class="panel"><h3>Current exercises (<?= count($exercises) ?>)</h3><p class="panel-desc">Exercises in your database</p><div class="list"><?php foreach ($exercises as $exercise): ?><div class="list-row"><div><strong><?= e($exercise['name']) ?></strong><div class="muted"><?= e($exercise['category']) ?> · <?= e($exercise['difficulty']) ?></div></div><form method="post" onsubmit="return confirm('Delete this exercise?');"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="exercise_id" value="<?= (int)$exercise['id'] ?>"><button class="button danger" type="submit">Delete</button></form></div><?php endforeach; ?></div></section></div></div></main></div></body></html>
