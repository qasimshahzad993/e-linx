<?php
require_once 'includes/functions.php';
require_admin();
require_once 'includes/db.php';

/* Table assumed: admins(id, name, email, password)  — password stored with password_hash() */
$id = (int)$_SESSION['admin_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    if (($_POST['action'] ?? '') === 'profile') {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Enter your name and a valid email address.');
        } else {
            $st = $conn->prepare('UPDATE admins SET name=?, email=? WHERE id=?');
            $st->bind_param('ssi', $name, $email, $id);
            $st->execute();
            $_SESSION['admin_name'] = $name;
            $_SESSION['admin_email'] = $email;
            flash('success', 'Profile updated.');
        }
        redirect('profile.php');
    }

    if (($_POST['action'] ?? '') === 'password') {
        $cur = $_POST['current'] ?? ''; $new = $_POST['new'] ?? ''; $con = $_POST['confirm'] ?? '';
        $st = $conn->prepare('SELECT password FROM admins WHERE id=?');
        $st->bind_param('i', $id); $st->execute();
        $hash = $st->get_result()->fetch_assoc()['password'] ?? '';

        if (!password_verify($cur, $hash))      flash('error', 'Current password is incorrect.');
        elseif (strlen($new) < 8)               flash('error', 'New password must be at least 8 characters.');
        elseif ($new !== $con)                  flash('error', 'New passwords do not match.');
        else {
            $h = password_hash($new, PASSWORD_DEFAULT);
            $st = $conn->prepare('UPDATE admins SET password=? WHERE id=?');
            $st->bind_param('si', $h, $id); $st->execute();
            flash('success', 'Password changed.');
        }
        redirect('profile.php#password');
    }
}

$st = $conn->prepare('SELECT name, email FROM admins WHERE id=?');
$st->bind_param('i', $id); $st->execute();
$me = $st->get_result()->fetch_assoc() ?: ['name' => '', 'email' => ''];

$pageTitle  = 'My profile';
$activePage = '';
require_once 'includes/layout_top.php';
?>

<div class="page-head">
  <div><h1>My profile</h1><p>Your account details and password.</p></div>
</div>

<div class="grid-2" style="grid-template-columns:repeat(auto-fit,minmax(300px,1fr))">
  <section class="card">
    <h2>Account details</h2>
    <form method="post">
      <?= csrf_field() ?><input type="hidden" name="action" value="profile">
      <div class="field"><label for="name">Full name</label>
        <input class="input" id="name" name="name" required value="<?= e($me['name']) ?>"></div>
      <div class="field"><label for="email">Email</label>
        <input class="input" id="email" name="email" type="email" required value="<?= e($me['email']) ?>"></div>
      <button class="btn btn-primary" type="submit">Save changes</button>
    </form>
  </section>

  <section class="card" id="password">
    <h2>Change password</h2>
    <form method="post" autocomplete="off">
      <?= csrf_field() ?><input type="hidden" name="action" value="password">
      <div class="field"><label for="current">Current password</label>
        <input class="input" id="current" name="current" type="password" required></div>
      <div class="field"><label for="new">New password</label>
        <input class="input" id="new" name="new" type="password" minlength="8" required>
        <div class="hint">At least 8 characters.</div></div>
      <div class="field"><label for="confirm">Confirm new password</label>
        <input class="input" id="confirm" name="confirm" type="password" required></div>
      <button class="btn btn-primary" type="submit">Update password</button>
    </form>
  </section>
</div>

<?php require_once 'includes/layout_bottom.php'; ?>
