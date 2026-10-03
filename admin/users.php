<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$plans = ['monthly' => 1, 'quarterly' => 3, 'yearly' => 12];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = (string) ($_POST['action'] ?? '');
    $targetId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);

    if ($targetId && $targetId !== (int) $_SESSION['user_id'] && $action === 'activate') {
        $plan = (string) ($_POST['plan'] ?? '');
        if (!isset($plans[$plan])) {
            flash('Choose a valid subscription duration.', 'error');
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'user'");
            $stmt->execute([$targetId]);
            if ($stmt->fetch()) {
                $expires = new DateTimeImmutable('today');
                $expires = $expires->modify('+' . $plans[$plan] . ' months')->format('Y-m-d');
                $stmt = $pdo->prepare("UPDATE users SET subscription_plan = ?, subscription_status = 'active', subscription_expires_at = ? WHERE id = ? AND role = 'user'");
                $stmt->execute([$plan, $expires, $targetId]);
                flash('Subscription activated until ' . $expires . '.');
            } else {
                flash('Only regular user accounts can have subscriptions.', 'error');
            }
        }
    } elseif ($targetId && $action === 'normal') {
        $stmt = $pdo->prepare("UPDATE users SET subscription_plan = NULL, subscription_status = 'normal', subscription_expires_at = NULL WHERE id = ? AND role = 'user'");
        $stmt->execute([$targetId]);
        flash('User set to a normal account.');
    } elseif ($targetId && $action === 'delete' && $targetId !== (int) $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
        $stmt->execute([$targetId]);
        flash($stmt->rowCount() ? 'User account deleted.' : 'Only regular user accounts can be deleted here.', $stmt->rowCount() ? 'success' : 'error');
    }
    redirect('users.php');
}

$users = $pdo->query('SELECT id, name, email, role, created_at, subscription_plan, subscription_status, subscription_expires_at FROM users ORDER BY created_at DESC')->fetchAll();
$notice = takeFlash();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Manage Users | FitTrack Admin</title><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="app-shell"><aside class="sidebar"><a class="brand" href="dashboard.php"><span class="brand-mark">✣</span>FitTrack</a><nav class="nav-list"><a href="dashboard.php">⌂ Overview</a><a class="active" href="users.php">♙ Manage users</a><a href="exercises.php">⚒ Exercises</a><a href="../subscription.php">↗ User subscription page</a></nav><div class="side-bottom nav-list"><a href="../logout.php">↪ Sign out</a></div></aside>
<main class="main-area"><header class="topbar"><h2>Manage accounts & subscriptions</h2><a class="button light" href="dashboard.php">← Overview</a></header><div class="content">
<section class="welcome admin-banner"><p class="eyebrow">User administration</p><h1>Accounts and subscriptions</h1><p>Review normal and premium accounts, approve requests, and track expiry dates.</p></section>
<?php if ($notice): ?><div class="alert <?= e($notice['type']) ?>"><?= e($notice['message']) ?></div><?php endif; ?>
<section class="panel"><div class="table-wrap"><table class="subscription-table"><thead><tr><th>Name</th><th>Email</th><th>Account</th><th>Subscription</th><th>Expiry date</th><th>Manage</th></tr></thead><tbody>
<?php foreach ($users as $person):
  $status = $person['subscription_status'] ?? 'normal';
  $expires = $person['subscription_expires_at'] ?? null;
  if ($person['role'] === 'user' && $status === 'active' && (!$expires || strtotime($expires) < strtotime('today'))) {
      $status = 'expired';
      $pdo->prepare("UPDATE users SET subscription_status = 'expired' WHERE id = ?")->execute([(int)$person['id']]);
  }
?>
<tr><td><strong><?= e($person['name']) ?></strong><div class="muted">Joined <?= e(substr($person['created_at'],0,10)) ?></div></td><td><?= e($person['email']) ?></td><td><span class="badge"><?= e(ucfirst($person['role'])) ?></span></td>
<td><span class="badge <?= $status === 'active' ? 'badge-premium' : ($status === 'pending' ? 'badge-pending' : ($status === 'expired' ? 'badge-expired' : '')) ?>"><?= e($person['role'] === 'admin' ? 'Admin' : ($status === 'normal' ? 'Normal' : ucfirst($status))) ?></span>
<?php if (!empty($person['subscription_plan'])): ?><div class="muted"><?= e(['monthly'=>'Monthly','quarterly'=>'3 Months','yearly'=>'Yearly'][$person['subscription_plan']] ?? $person['subscription_plan']) ?></div><?php endif; ?></td>
<td><?= $expires ? e(date('M j, Y', strtotime($expires))) : '—' ?></td><td class="manage-cell">
<?php if ($person['role'] === 'user'): ?>
<form method="post" class="admin-sub-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="user_id" value="<?= (int)$person['id'] ?>"><input type="hidden" name="action" value="activate"><select name="plan" aria-label="Subscription duration"><option value="monthly">1 month</option><option value="quarterly">3 months</option><option value="yearly">12 months</option></select><button class="button btn-small" type="submit"><?= $status === 'pending' ? 'Approve' : 'Activate / renew' ?></button></form>
<form method="post" class="inline-form"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="user_id" value="<?= (int)$person['id'] ?>"><input type="hidden" name="action" value="normal"><button class="button outline btn-small" type="submit">Set normal</button></form>
<form method="post" class="inline-form" onsubmit="return confirm('Delete this user and all related data?');"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="user_id" value="<?= (int)$person['id'] ?>"><button class="button danger btn-small" type="submit">Delete</button></form>
<?php else: ?>Protected<?php endif; ?></td></tr>
<?php endforeach; ?>
</tbody></table></div></section></div></main></div></body></html>
