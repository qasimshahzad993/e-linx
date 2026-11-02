<?php
require_once 'includes/functions.php';
require_admin();
require_once 'includes/db.php';
$cA = col($conn,'products',['is_active','active','status']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete') {
        $st = $conn->prepare('DELETE FROM products WHERE id=?'); $st->bind_param('i', $id); $st->execute();
        flash('success', 'Product deleted.'); redirect('products.php');
    }
    $name = trim($_POST['name'] ?? ''); $desc = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0); $act = isset($_POST['is_active']) ? 1 : 0;
    if ($name === '') { flash('error', 'Product name is required.'); redirect('products.php' . ($id ? "?edit=$id" : '')); }
    if ($price < 0)   { flash('error', 'Price can\'t be negative.'); redirect('products.php' . ($id ? "?edit=$id" : '')); }
    $d = ['name'=>[$name,'s'],'price'=>[$price,'d'],'description'=>[$desc,'s']];
    if ($cA) $d[$cA]=[$act,'i'];
    save_row($conn,'products',$id,$d); flash('success', $id ? 'Product updated.' : 'Product added.'); redirect('products.php');
}
$edit = null;
if (!empty($_GET['edit'])) { $st = $conn->prepare('SELECT * FROM products WHERE id=?'); $st->bind_param('i', $_GET['edit']); $st->execute(); $edit = $st->get_result()->fetch_assoc(); }
$rows = $conn->query('SELECT * FROM products ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Products'; $activePage = 'products';
require_once 'includes/layout_top.php';
?>
<div class="page-head"><div><h1>Products</h1><p>Products listed on your website.</p></div></div>
<div class="grid-2">
  <section class="card"><h2><?= $edit ? 'Edit product' : 'Add product' ?></h2>
    <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
      <div class="field"><label for="name">Product name</label><input class="input" id="name" name="name" required value="<?= e($edit['name'] ?? '') ?>"></div>
      <div class="field"><label for="price">Price</label><input class="input" id="price" name="price" type="number" step="0.01" min="0" value="<?= e($edit['price'] ?? '0') ?>"></div>
      <div class="field"><label for="description">Description</label><textarea class="textarea" id="description" name="description"><?= e($edit['description'] ?? '') ?></textarea></div>
      <label class="check"><input type="checkbox" name="is_active" <?= !$edit || !$cA || !empty($edit[$cA]) ? 'checked' : '' ?>>Show on website</label>
      <div class="btn-row"><button class="btn btn-primary" type="submit"><i class="bi bi-check2"></i><?= $edit ? 'Save changes' : 'Add product' ?></button>
        <?php if ($edit): ?><a class="btn btn-ghost" href="products.php">Cancel</a><?php endif; ?></div>
    </form></section>
  <section class="card"><div class="card-head"><h2>All products (<?= count($rows) ?>)</h2></div>
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Price</th><th>Status</th><th></th></tr></thead><tbody>
    <?php if (!$rows): ?><tr><td colspan="4"><div class="empty"><i class="bi bi-box-seam"></i>No products yet. Add your first one on the left.</div></td></tr><?php endif; ?>
    <?php foreach ($rows as $r): ?>
      <tr><td><strong><?= e($r['name']) ?></strong></td><td><?= e(number_format((float)$r['price'], 2)) ?></td>
        <td><span class="badge <?= ($cA ? !empty($r[$cA]) : true) ? 'badge-on' : 'badge-off' ?>"><?= ($cA ? !empty($r[$cA]) : true) ? 'Live' : 'Hidden' ?></span></td>
        <td class="actions"><a class="btn btn-ghost btn-sm" href="products.php?edit=<?= (int)$r['id'] ?>"><i class="bi bi-pencil"></i>Edit</a>
          <form method="post" data-confirm="Delete “<?= e($r['name']) ?>”? This can't be undone."><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i>Delete</button></form></td></tr>
    <?php endforeach; ?></tbody></table></div></section>
</div>
<?php require_once 'includes/layout_bottom.php'; ?>
