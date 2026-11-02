<?php require_once __DIR__ . '/../config/config.php'; $pageTitle = $pageTitle ?? APP_NAME; ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle) ?> | <?= APP_NAME ?></title>
<meta name="description" content="e-Linx builds modern websites, software, mobile apps, YouTube automation, AI workflows and digital growth systems.">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css">
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<link href="<?= url('assets/css/app.css') ?>" rel="stylesheet">
</head><body>
<div class="topbar"><div class="container d-flex justify-content-between align-items-center"><span><i class="bi bi-stars"></i> Engineering ideas into scalable digital products</span><span class="d-none d-md-inline">Mon–Fri · 9:00 AM–7:00 PM PKT &nbsp; | &nbsp; <a href="mailto:hello@elinx.pk">hello@elinx.pk</a></span></div></div>
<nav class="navbar navbar-expand-lg main-nav sticky-top"><div class="container">
<a class="navbar-brand d-flex align-items-center" href="<?= url() ?>" aria-label="e-Linx home"><img class="elinx-logo" src="<?= url('assets/img/elinxlogo.png') ?>" alt="e-Linx logo"></a>
<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><i class="bi bi-list"></i></button>
<div class="collapse navbar-collapse" id="nav"><ul class="navbar-nav mx-auto gap-lg-2">
<li><a class="nav-link" href="<?= url() ?>">Home</a></li><li><a class="nav-link" href="<?= url('about.php') ?>">About</a></li>
<li><a class="nav-link" href="<?= url('services.php') ?>">Services</a></li><li><a class="nav-link" href="<?= url('products.php') ?>">Products</a></li>
<li><a class="nav-link" href="<?= url('portfolio.php') ?>">Portfolio</a></li><li><a class="nav-link" href="<?= url('contact.php') ?>">Contact</a></li></ul>
<a class="btn btn-primary rounded-pill px-4" href="<?= url('quote.php') ?>">Start a Project <i class="bi bi-arrow-up-right"></i></a></div></div></nav>
<?php if ($m = flash('success')): ?><div class="container mt-3"><div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?= e($m) ?><button class="btn-close" data-bs-dismiss="alert"></button></div></div><?php endif; ?>
<?php if ($m = flash('error')): ?><div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i><?= e($m) ?><button class="btn-close" data-bs-dismiss="alert"></button></div></div><?php endif; ?>
