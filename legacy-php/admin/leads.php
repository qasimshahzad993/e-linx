<?php
require_once 'includes/functions.php';
require_admin();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'status' && in_array($_POST['status'] ?? '', ['new','contacted','closed'], true)) {
        $st = $conn->prepare('UPDATE leads SET status=? WHERE id=?'); $st->bind_param('si', $_POST['status'], $id); $st->execute();
        flash('success', 'Lead marked as ' . $_POST['status'] . '.');
    }
    if (($_POST['action'] ?? '') === 'delete') {
        $st = $conn->prepare('DELETE FROM leads WHERE id=?'); $st->bind_param('i', $id); $st->execute();
        flash('success', 'Lead deleted.');
    }
    redirect('leads.php' . (!empty($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
}

$filter = in_array($_GET['status'] ?? '', ['new','contacted','closed'], true) ? $_GET['status'] : '';
if ($filter) { $st = $conn->prepare('SELECT * FROM leads WHERE status=? ORDER BY id DESC'); $st->bind_param('s', $filter); $st->execute(); $rows = $st->get_result()->fetch_all(MYSQLI_ASSOC); }
else $rows = $conn->query('SELECT * FROM leads ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Leads'; $activePage = 'leads';
require_once 'includes/layout_top.php';
?>
<div class="page-head"><div><h1>Leads</h1><p>Quote requests from your website.</p></div>
  <div class="tabs">
    <?php foreach (['' => 'All', 'new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $k => $v): ?>
      <a class="tab <?= $filter === $k ? 'on' : '' ?>" href="leads.php<?= $k ? "?status=$k" : '' ?>"><?= $v ?></a>
    <?php endforeach; ?>
  </div></div>

<section class="card"><div class="table-wrap"><table>
  <thead><tr><th>Contact</th><th>Service</th><th>Message</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php if (!$rows): ?><tr><td colspan="5"><div class="empty"><i class="bi bi-inbox"></i>No leads here.</div></td></tr><?php endif; ?>
  <?php foreach ($rows as $l): ?>
    <tr>
      <td><strong><?= e($l['name']) ?></strong><br><small class="muted"><?= e($l['email']) ?><?= $l['phone'] ? ' · ' . e($l['phone']) : '' ?></small><br><small class="muted"><?= e(date('d M Y, g:i a', strtotime($l['created_at']))) ?></small></td>
      <td><?= isset($l['service']) && $l['service'] !== '' ? e($l['service']) : '-' ?></td>
      <td style="max-width:280px"><details><summary><?= e(mb_strimwidth((string)$l['message'], 0, 40, '…')) ?></summary><p><?= nl2br(e($l['message'])) ?></p></details></td>
      <td>
        <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
          <select class="select select-sm" name="status" onchange="this.form.submit()">
            <?php foreach (['new','contacted','closed'] as $s): ?><option value="<?= $s ?>" <?= $l['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
          </select></form>
      </td>
      <td class="actions">
        <?php if ($l['email']): ?><a class="btn btn-ghost btn-sm" href="mailto:<?= e($l['email']) ?>"><i class="bi bi-envelope"></i>Reply</a><?php endif; ?>
        <form method="post" data-confirm="Delete the lead from <?= e($l['name']) ?>? This can't be undone.">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
          <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i>Delete</button></form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody></table></div></section>
<?php require_once 'includes/layout_bottom.php'; ?>