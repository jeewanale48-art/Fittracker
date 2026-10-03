<?php
require_once __DIR__ . '/includes/auth.php';
requireRole('user');

$userId = (int) $_SESSION['user_id'];
$error = '';
$success = '';
$plans = [
    'monthly' => ['label' => 'Monthly', 'months' => 1, 'description' => 'Access for 1 month'],
    'quarterly' => ['label' => '3 Months', 'months' => 3, 'description' => 'Access for 3 months'],
    'yearly' => ['label' => 'Yearly', 'months' => 12, 'description' => 'Access for 12 months'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $plan = (string) ($_POST['plan'] ?? '');
    if (!isset($plans[$plan])) {
        $error = 'Please select a valid subscription plan.';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET subscription_plan = ?, subscription_status = 'pending' WHERE id = ? AND role = 'user'");
        $stmt->execute([$plan, $userId]);
        flash('Subscription request submitted. An administrator must activate it; no payment has been taken.', 'success');
        redirect('subscription.php');
    }
}

$stmt = $pdo->prepare('SELECT subscription_plan, subscription_status, subscription_expires_at FROM users WHERE id = ?');
$stmt->execute([$userId]);
$subscription = $stmt->fetch() ?: [];
$status = $subscription['subscription_status'] ?? 'normal';
$expiry = $subscription['subscription_expires_at'] ?? null;
$isActive = $status === 'active' && $expiry && strtotime($expiry) >= strtotime('today');
if ($status === 'active' && !$isActive) {
    $pdo->prepare("UPDATE users SET subscription_status = 'expired' WHERE id = ?")->execute([$userId]);
    $status = 'expired';
}
$notice = takeFlash();
$pageTitle = 'Subscription';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">MEMBERSHIP</p><h1>Subscription options</h1><p>Choose a membership duration for your FitTrack account.</p></div></section>
<?php if ($notice): ?><div class="alert <?= e($notice['type']) ?>"><?= e($notice['message']) ?></div><?php endif; ?>
<section class="panel subscription-current">
  <div><p class="eyebrow">CURRENT STATUS</p><h2><?= $isActive ? 'Premium subscription active' : ($status === 'pending' ? 'Request pending approval' : ($status === 'expired' ? 'Subscription expired' : 'Normal account')) ?></h2>
  <p class="muted"><?= $isActive ? 'Your subscription is active until ' . date('F j, Y', strtotime($expiry)) . '.' : ($status === 'pending' ? 'Your selected plan is waiting for administrator approval.' : 'You can continue with a normal account or request a subscription below.') ?></p></div>
  <span class="badge <?= $isActive ? 'badge-premium' : ($status === 'pending' ? 'badge-pending' : '') ?>"><?= e(ucfirst($status)) ?></span>
</section>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<section class="card-grid subscription-grid">
<?php foreach ($plans as $key => $plan): ?>
<article class="exercise-card plan-card">
  <p class="eyebrow">FITTRACK PREMIUM</p><h2><?= e($plan['label']) ?></h2><p><?= e($plan['description']) ?></p>
  <ul><li>Premium member status</li><li>Expiry date tracked in your account</li><li>Admin-confirmed activation</li></ul>
  <form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="plan" value="<?= e($key) ?>"><button class="button full" type="submit" <?= $status === 'pending' ? 'disabled' : '' ?>><?= $status === 'pending' ? 'Request pending' : 'Request plan' ?></button></form>
</article>
<?php endforeach; ?>
</section>
<p class="field-help">Note: This version records subscription requests but does not process payments. An administrator activates the plan from the admin panel.</p>
<?php require __DIR__ . '/includes/footer.php'; ?>
