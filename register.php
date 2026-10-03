<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';

if (!empty($_SESSION['user_id'])) {
    redirect(($_SESSION['role'] ?? '') === 'admin' ? 'admin/dashboard.php' : 'dashboard.php');
}
$role = ($_GET['role'] ?? $_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
$name = '';
$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');
    $adminCode = (string) ($_POST['admin_code'] ?? '');

    if ($name === '' || mb_strlen($name) > 100) {
        $error = 'Enter your name (up to 100 characters).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Your password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'The passwords do not match.';
    } elseif ($role === 'admin' && (ADMIN_REGISTRATION_CODE === 'CHANGE-THIS-ADMIN-CODE' || !hash_equals(ADMIN_REGISTRATION_CODE, $adminCode))) {
        $error = 'Admin registration code is not configured or is incorrect. Ask the system owner to configure it in config/app.php.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'An account with this email already exists. Please sign in.';
        } else {
            $age = isset($_POST['age']) && $_POST['age'] !== '' ? filter_var($_POST['age'], FILTER_VALIDATE_INT) : null;
            $genderOptions = ['male', 'female', 'other', 'prefer_not_to_say'];
            $gender = in_array($_POST['gender'] ?? '', $genderOptions, true) ? $_POST['gender'] : null;
            $height = isset($_POST['height_cm']) && $_POST['height_cm'] !== '' ? (float) $_POST['height_cm'] : null;
            $weight = isset($_POST['weight_kg']) && $_POST['weight_kg'] !== '' ? (float) $_POST['weight_kg'] : null;
            
$fitnessOptions = ['beginner', 'intermediate', 'advanced'];

$submittedFitnessLevel = $_POST['fitness_level'] ?? 'beginner';

$fitnessLevel = in_array($submittedFitnessLevel, $fitnessOptions, true)
    ? $submittedFitnessLevel
    : 'beginner';

            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, age, gender, height_cm, weight_kg, fitness_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role, $age, $gender, $height, $weight, $fitnessLevel]);
            flash('Your account has been created. You can sign in now.');
            redirect('login.php?role=' . $role);
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $role === 'admin' ? 'Admin' : 'User' ?> Registration | FitTrack</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body><header class="site-header"><div class="wrap header-inner"><a class="brand" href="index.php"><span class="brand-mark">✣</span>FitTrack</a><a class="small-nav" href="index.php">← Change portal</a></div></header>
<main class="auth-layout"><section class="auth-card"><p class="eyebrow"><?= $role === 'admin' ? 'Administrator registration' : 'Join FitTrack' ?></p><h1>Create your account</h1><p class="intro"><?= $role === 'admin' ? 'Admin registration requires a private setup code.' : 'Set up your account to start tracking your fitness.' ?></p>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post" action="register.php?role=<?= e($role) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="role" value="<?= e($role) ?>">
<div class="field"><label for="name">Full name</label><input id="name" name="name" maxlength="100" autocomplete="name" value="<?= e($name) ?>" required></div>
<div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" maxlength="190" autocomplete="email" value="<?= e($email) ?>" required></div>
<div class="field"><label for="password">Password</label><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required><span class="field-help">Use at least 8 characters.</span></div>
<div class="field"><label for="confirm_password">Confirm password</label><input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required></div>
<?php if ($role === 'admin'): ?><div class="field"><label for="admin_code">Admin registration code</label><input id="admin_code" name="admin_code" type="password" required><span class="field-help">Set ADMIN_REGISTRATION_CODE in config/app.php first.</span></div><?php else: ?>
<div class="field"><label for="age">Age (optional)</label><input id="age" name="age" type="number" min="1" max="120"></div>
<div class="field"><label for="gender">Gender (optional)</label><select id="gender" name="gender"><option value="">Prefer not to specify</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option><option value="prefer_not_to_say">Prefer not to say</option></select></div>
<div class="field"><label for="height_cm">Height in cm (optional)</label><input id="height_cm" name="height_cm" type="number" min="1" max="300" step="0.1"></div>
<div class="field"><label for="weight_kg">Weight in kg (optional)</label><input id="weight_kg" name="weight_kg" type="number" min="1" max="500" step="0.1"></div>
<div class="field"><label for="fitness_level">Fitness level</label><select id="fitness_level" name="fitness_level"><option value="beginner">Beginner</option><option value="intermediate">Intermediate</option><option value="advanced">Advanced</option></select></div>
<?php endif; ?>
<button class="button full" type="submit">Create <?= $role === 'admin' ? 'admin' : 'user' ?> account →</button></form>
<p class="form-foot">Already registered? <a href="login.php?role=<?= e($role) ?>">Sign in</a></p></section></main></body></html>
