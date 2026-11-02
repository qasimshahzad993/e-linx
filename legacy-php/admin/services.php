<?php
require_once 'includes/functions.php';
require_admin();
require_once 'includes/db.php';
$cA = col($conn,'services',['is_active','active','status']);
$cI = col($conn,'services',['icon','icon_class']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete') {
        $st = $conn->prepare('DELETE FROM services WHERE id=?'); $st->bind_param('i', $id); $st->execute();
        flash('success', 'Service deleted.'); redirect('services.php');
    }
    $title = trim($_POST['title'] ?? ''); $desc = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? '') ?: 'bi bi-gear'; $act = isset($_POST['is_active']) ? 1 : 0;
    if ($title === '') { flash('error', 'Service title is required.'); redirect('services.php' . ($id ? "?edit=$id" : '')); }
    $d = ['title'=>[$title,'s'],'description'=>[$desc,'s']]; if ($cI) $d[$cI]=[$icon,'s'];
    if ($cA) $d[$cA]=[$act,'i'];
    save_row($conn,'services',$id,$d); flash('success', $id ? 'Service updated.' : 'Service added.'); redirect('services.php');
}
$edit = null;
if (!empty($_GET['edit'])) { $st = $conn->prepare('SELECT * FROM services WHERE id=?'); $st->bind_param('i', $_GET['edit']); $st->execute(); $edit = $st->get_result()->fetch_assoc(); }
$rows = $conn->query('SELECT * FROM services ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Services'; $activePage = 'services';
require_once 'includes/layout_top.php';
?>
<div class="page-head"><div><h1>Services</h1><p>What you offer, shown on your website.</p></div></div>
<div class="grid-2">
  <section class="card"><h2><?= $edit ? 'Edit service' : 'Add service' ?></h2>
    <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
      <div class="field"><label for="title">Service title</label><input class="input" id="title" name="title" required value="<?= e($edit['title'] ?? '') ?>"></div>
      <div class="field"><label for="description">Description</label><textarea class="textarea" id="description" name="description"><?= e($edit['description'] ?? '') ?></textarea></div>
      <div class="field"><label for="icon">Icon class</label><input class="input" id="icon" name="icon" placeholder="bi bi-gear" value="<?= e($edit['icon'] ?? 'bi bi-gear') ?>"><div class="hint">Any Bootstrap Icons class.</div></div>
      <label class="check"><input type="checkbox" name="is_active" <?= !$edit || !$cA || !empty($edit[$cA]) ? 'checked' : '' ?>>Show on website</label>
      <div class="btn-row"><button class="btn btn-primary" type="submit"><i class="bi bi-check2"></i><?= $edit ? 'Save changes' : 'Add service' ?></button>
        <?php if ($edit): ?><a class="btn btn-ghost" href="services.php">Cancel</a><?php endif; ?></div>
    </form></section>
  <section class="card"><div class="card-head"><h2>All services (<?= count($rows) ?>)</h2></div>
    <div class="table-wrap"><table><thead><tr><th>Title</th><th>Status</th><th></th></tr></thead><tbody>
    <?php if (!$rows): ?><tr><td colspan="3"><div class="empty"><i class="bi bi-layers"></i>No services yet. Add your first one on the left.</div></td></tr><?php endif; ?>
    <?php foreach ($rows as $r): ?>
      <tr><td><i class="<?= e($cI ? ($r[$cI] ?? '') : 'bi bi-gear') ?>" style="margin-right:8px;color:var(--accent)"></i><strong><?= e($r['title']) ?></strong></td>
        <td><span class="badge <?= ($cA ? !empty($r[$cA]) : true) ? 'badge-on' : 'badge-off' ?>"><?= ($cA ? !empty($r[$cA]) : true) ? 'Live' : 'Hidden' ?></span></td>
        <td class="actions"><a class="btn btn-ghost btn-sm" href="services.php?edit=<?= (int)$r['id'] ?>"><i class="bi bi-pencil"></i>Edit</a>
          <form method="post" data-confirm="Delete “<?= e($r['title']) ?>”? This can't be undone."><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i>Delete</button></form></td></tr>
    <?php endforeach; ?></tbody></table></div></section>
</div>
<?php require_once 'includes/layout_bottom.php'; ?>
