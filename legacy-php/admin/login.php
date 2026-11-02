<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';
if (!empty($_SESSION['admin_id'])) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? ''); $pass = $_POST['password'] ?? '';
    $st = $conn->prepare('SELECT id,name,email,password FROM admins WHERE email=? OR name=? LIMIT 1');
    $st->bind_param('ss', $login, $login); $st->execute();
    $u = $st->get_result()->fetch_assoc();
    $ok = false;
    if ($u) {
        if (password_verify($pass, $u['password'])) $ok = true;
        elseif (hash_equals($u['password'], md5($pass))) {      // old md5 passwords: accept once, then upgrade
            $ok = true;
            $h = password_hash($pass, PASSWORD_DEFAULT);
            $up = $conn->prepare('UPDATE admins SET password=? WHERE id=?'); $up->bind_param('si', $h, $u['id']); $up->execute();
        }
    }
    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $u['id']; $_SESSION['admin_name'] = $u['name']; $_SESSION['admin_email'] = $u['email'];
        flash('success', 'Welcome back, ' . $u['name'] . '.');
        redirect('index.php');
    }
    flash('error', 'Email or password is incorrect.');
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log in · e-Linx Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="icon" type="image/svg+xml" href="../../favicon.svg">
<link rel="stylesheet" href="assets/css/admin.css">
</head><body class="login-body">
<form class="login-card" method="post">
  <div class="login-brand"><img src="../assets/img/elinxlogo.png" alt="e-Linx Technology"></div>
  <h1>Log in to admin</h1>
  <p>Manage leads, services and your portfolio.</p>
  <div class="field"><label for="login">Email</label>
    <input class="input" id="login" name="login" required autofocus value="<?= e($_POST['login'] ?? '') ?>"></div>
  <div class="field"><label for="password">Password</label>
    <input class="input" id="password" name="password" type="password" required></div>
  <button class="btn btn-primary" style="width:100%" type="submit">Log in</button>
</form>
<div class="toasts" id="toasts"></div>
<script>window.__flashes = <?= json_encode(get_flashes(), JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>
<script src="assets/js/admin.js"></script>
</body></html>
