<?php
/*
 * Usage on any admin page:
 *   require_once 'includes/layout_top.php';   // set $pageTitle and $activePage BEFORE this
 *   ... page content ...
 *   require_once 'includes/layout_bottom.php';
 */
require_once __DIR__ . '/functions.php';
require_admin();

$pageTitle  = $pageTitle  ?? 'Dashboard';
$activePage = $activePage ?? 'dashboard';
$adminName  = $_SESSION['admin_name']  ?? 'Admin';
$adminEmail = $_SESSION['admin_email'] ?? '';

// Add a new page = add one line here
$nav = [
    'dashboard' => ['Dashboard', 'bi-grid',        'index.php'],
    'leads'     => ['Leads',     'bi-inbox',       'leads.php'],
    'services'  => ['Services',  'bi-layers',      'services.php'],
    'products'  => ['Products',  'bi-box-seam',    'products.php'],
    'portfolio' => ['Portfolio', 'bi-briefcase',   'portfolio.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> · e-Linx Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="shell">

  <aside class="sidebar" id="sidebar">
    <a class="brand" href="index.php">
      <img src="../assets/img/elinxlogo.png" alt="e-Linx Technology">
    </a>
    <div class="nav-group">Management</div>
    <nav>
      <?php foreach ($nav as $key => [$label, $icon, $href]): ?>
        <a href="<?= $href ?>" class="nav-link <?= $key === $activePage ? 'active' : '' ?>">
          <i class="bi <?= $icon ?>"></i><?= e($label) ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">
      <a href="../index.php" target="_blank" class="nav-link"><i class="bi bi-globe2"></i>View website</a>
      <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-left"></i>Log out</a>
    </div>
  </aside>
  <div class="scrim" id="scrim"></div>

  <div class="main">
    <header class="topbar">
      <button class="icon-btn menu-btn" id="menuBtn" aria-label="Open menu"><i class="bi bi-list"></i></button>
      <div class="crumbs">
        <span>Admin</span><i class="bi bi-chevron-right"></i><strong><?= e($pageTitle) ?></strong>
      </div>

      <div class="profile" id="profile">
        <button class="profile-btn" id="profileBtn" aria-haspopup="true" aria-expanded="false">
          <span class="avatar"><?= e(initials($adminName)) ?></span>
          <span class="who"><strong><?= e($adminName) ?></strong><small>Administrator</small></span>
          <i class="bi bi-chevron-down"></i>
        </button>
        <div class="menu" id="profileMenu" role="menu">
          <div class="menu-head">
            <strong><?= e($adminName) ?></strong>
            <small><?= e($adminEmail) ?></small>
          </div>
          <a href="profile.php" role="menuitem"><i class="bi bi-person"></i>My profile</a>
          <a href="profile.php#password" role="menuitem"><i class="bi bi-shield-lock"></i>Change password</a>
          <a href="logout.php" role="menuitem" class="danger"><i class="bi bi-box-arrow-left"></i>Log out</a>
        </div>
      </div>
    </header>

    <main class="content">
