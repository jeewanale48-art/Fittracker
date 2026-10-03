<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$q = trim((string)($_GET['q'] ?? ''));
$sql = 'SELECT id, name, category, muscle_group, instructions FROM exercises';
$params = [];
if ($q !== '') {
    $sql .= ' WHERE name LIKE ? OR category LIKE ? OR muscle_group LIKE ?';
    $like = '%'.$q.'%';
    $params = [$like, $like, $like];
}
$sql .= ' ORDER BY category, name LIMIT 200';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$exercises = $stmt->fetchAll();
$pageTitle = 'Exercises';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">EXERCISE LIBRARY</p><h1>Explore exercises</h1><p>Find movements to include in your workout routine.</p></div></section>
<section class="panel">
    <form class="search-form" method="GET"><input name="q" value="<?= escapeHtml($q) ?>" placeholder="Search name, category, muscle group"><button class="btn" type="submit">Search</button></form>
    <?php if (!$exercises): ?><p>No exercises found. An admin can add exercises from the admin panel.</p><?php else: ?>
    <div class="card-grid">
    <?php foreach ($exercises as $e): ?><article class="exercise-card"><span class="pill"><?= escapeHtml($e['category']) ?></span><h3><?= escapeHtml($e['name']) ?></h3><p class="muted"><?= escapeHtml($e['muscle_group'] ?: 'General') ?></p><?php if ($e['instructions']): ?><p><?= nl2br(escapeHtml($e['instructions'])) ?></p><?php endif; ?></article><?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
