<?php
require_once 'includes/functions.php';
require_admin();
require_once 'includes/db.php';

$stats = [
  ['New leads',   count_of($conn, 'leads', "status='new'"), 'bi-inbox',     'leads.php'],
  ['All leads',   count_of($conn, 'leads'),                 'bi-people',    'leads.php'],
  ['Services',    count_of($conn, 'services'),              'bi-layers',    'services.php'],
  ['Products',    count_of($conn, 'products'),              'bi-box-seam',  'products.php'],
  ['Projects',    count_of($conn, 'portfolio'),             'bi-briefcase', 'portfolio.php'],
];
try { $recent = $conn->query('SELECT * FROM leads ORDER BY id DESC LIMIT 6')->fetch_all(MYSQLI_ASSOC); }
catch (Throwable $e) { $recent = []; }

$pageTitle = 'Dashboard'; $activePage = 'dashboard';
require_once 'includes/layout_top.php';
?>
<div class="page-head"><div>
  <h1>Welcome back, <?= e(explode(' ', $adminName)[0]) ?></h1>
  <p>Here's what's happening on your site.</p></div></div>

<div class="stats">
<?php foreach ($stats as [$label, $n, $icon, $href]): ?>
  <a class="stat card" href="<?= $href ?>">
    <span class="stat-ico"><i class="bi <?= $icon ?>"></i></span>
    <span><strong><?= $n ?></strong><small><?= e($label) ?></small></span>
  </a>
<?php endforeach; ?>
</div>

<section class="card" style="margin-top:22px">
  <div class="card-head"><h2>Latest leads</h2><a class="btn btn-ghost btn-sm" href="leads.php">View all</a></div>
  <div class="table-wrap"><table>
    <thead><tr><th>Name</th><th>Service</th><th>Status</th><th>Received</th></tr></thead>
    <tbody>
    <?php if (!$recent): ?><tr><td colspan="4"><div class="empty"><i class="bi bi-inbox"></i>No leads yet. Quote requests will appear here.</div></td></tr><?php endif; ?>
    <?php foreach ($recent as $l): ?>
      <tr><td><strong><?= e($l['name']) ?></strong><br><small class="muted"><?= e($l['email']) ?></small></td>
          <td><?= isset($l['service']) && $l['service'] !== '' ? e($l['service']) : '-' ?></td>
          <td><span class="badge badge-<?= e($l['status']) ?>"><?= e(ucfirst($l['status'])) ?></span></td>
          <td><?= e(date('d M Y, g:i a', strtotime($l['created_at']))) ?></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
</section>
<?php require_once 'includes/layout_bottom.php'; ?>