<?php
require_once 'includes/functions.php';
require_admin();
require_once 'includes/db.php';

// Column names are detected, so this works whether your table uses is_active or active, link or url, etc.
$cT = col($conn, 'portfolio', ['title', 'name']);
$cC = col($conn, 'portfolio', ['category', 'type']);
$cD = col($conn, 'portfolio', ['description', 'details']);
$cL = col($conn, 'portfolio', ['link', 'url', 'project_url']);
$cI = col($conn, 'portfolio', ['icon', 'icon_class']);
$cA = col($conn, 'portfolio', ['is_active', 'active', 'status']);
$v  = fn($row, $c) => $c ? ($row[$c] ?? '') : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);

    if (($_POST['action'] ?? '') === 'delete') {
        $st = $conn->prepare('DELETE FROM portfolio WHERE id = ?'); $st->bind_param('i', $id); $st->execute();
        flash('success', 'Project deleted.'); redirect('portfolio.php');
    }

    $title = trim($_POST['title'] ?? ''); $cat = trim($_POST['category'] ?? '');
    $desc  = trim($_POST['description'] ?? ''); $link = trim($_POST['link'] ?? '');
    $icon  = trim($_POST['icon'] ?? '') ?: 'bi bi-window'; $act = isset($_POST['is_active']) ? 1 : 0;
    $back  = 'portfolio.php' . ($id ? "?edit=$id" : '');

    if ($title === '' || $cat === '') { flash('error', 'Project title and category are required.'); redirect($back); }
    if ($link !== '' && !filter_var($link, FILTER_VALIDATE_URL)) { flash('error', 'Enter a valid link, starting with https://'); redirect($back); }

    $d = [];
    if ($cT) $d[$cT] = [$title, 's'];
    if ($cC) $d[$cC] = [$cat, 's'];
    if ($cD) $d[$cD] = [$desc, 's'];
    if ($cL) $d[$cL] = [$link, 's'];
    if ($cI) $d[$cI] = [$icon, 's'];
    if ($cA) $d[$cA] = [$act, 'i'];
    save_row($conn, 'portfolio', $id, $d);
    flash('success', $id ? 'Project updated.' : 'Project added.');
    redirect('portfolio.php');
}

$edit = null;
if (!empty($_GET['edit'])) {
    $st = $conn->prepare('SELECT * FROM portfolio WHERE id = ?'); $st->bind_param('i', $_GET['edit']); $st->execute();
    $edit = $st->get_result()->fetch_assoc();
}
$rows = $conn->query('SELECT * FROM portfolio ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Portfolio'; $activePage = 'portfolio';
require_once 'includes/layout_top.php';
?>

<div class="page-head"><div><h1>Portfolio</h1><p>Projects shown on your website.</p></div></div>

<div class="grid-2">
  <section class="card">
    <h2><?= $edit ? 'Edit project' : 'Add project' ?></h2>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
      <div class="field"><label for="title">Project title</label>
        <input class="input" id="title" name="title" required value="<?= e($v($edit ?? [], $cT)) ?>"></div>
      <div class="field"><label for="category">Category</label>
        <input class="input" id="category" name="category" required placeholder="Web, Mobile or SaaS" value="<?= e($v($edit ?? [], $cC)) ?>"></div>
      <div class="field"><label for="description">Description</label>
        <textarea class="textarea" id="description" name="description"><?= e($v($edit ?? [], $cD)) ?></textarea></div>
      <div class="field"><label for="link">Project link</label>
        <input class="input" id="link" name="link" placeholder="https://..." value="<?= e($v($edit ?? [], $cL)) ?>"></div>
      <div class="field"><label for="icon">Icon class</label>
        <input class="input" id="icon" name="icon" placeholder="bi bi-window" value="<?= e($v($edit ?? [], $cI) ?: 'bi bi-window') ?>">
        <div class="hint">Any Bootstrap Icons class.</div></div>
      <label class="check"><input type="checkbox" name="is_active" <?= !$edit || !$cA || !empty($edit[$cA]) ? 'checked' : '' ?>>Show on website</label>
      <div class="btn-row">
        <button class="btn btn-primary" type="submit"><i class="bi bi-check2"></i><?= $edit ? 'Save changes' : 'Add project' ?></button>
        <?php if ($edit): ?><a class="btn btn-ghost" href="portfolio.php">Cancel</a><?php endif; ?>
      </div>
    </form>
  </section>

  <section class="card">
    <div class="card-head"><h2>All projects (<?= count($rows) ?>)</h2></div>
    <div class="table-wrap"><table>
      <thead><tr><th>Title</th><th>Category</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php if (!$rows): ?><tr><td colspan="4"><div class="empty"><i class="bi bi-briefcase"></i>No projects yet. Add your first one on the left.</div></td></tr><?php endif; ?>
      <?php foreach ($rows as $r): $on = $cA ? !empty($r[$cA]) : true; ?>
        <tr>
          <td><strong><?= e($v($r, $cT)) ?></strong></td>
          <td><?= e($v($r, $cC)) ?></td>
          <td><span class="badge <?= $on ? 'badge-on' : 'badge-off' ?>"><?= $on ? 'Live' : 'Hidden' ?></span></td>
          <td class="actions">
            <a class="btn btn-ghost btn-sm" href="portfolio.php?edit=<?= (int)$r['id'] ?>"><i class="bi bi-pencil"></i>Edit</a>
            <form method="post" data-confirm="Delete “<?= e($v($r, $cT)) ?>”? This can't be undone.">
              <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i>Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </section>
</div>

<?php require_once 'includes/layout_bottom.php'; ?>
