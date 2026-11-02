<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin sign in · e-Linx</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v=5">
</head>
<body class="login-body">
<form class="login-card" method="post" action="{{ route('admin.authenticate') }}">
    @csrf
    <div class="login-brand">
        <img src="{{ asset('assets/img/elinxlogo.png') }}" alt="e-Linx Technology">
        <span><strong>e-Linx Technology</strong><small>ADMIN CONSOLE</small></span>
    </div>
    <div class="login-eyebrow"><span></span> SECURE WORKSPACE</div>
    <h1>Welcome back</h1>
    <p>Sign in to manage your website, content, and enquiries.</p>
    @if(session('status'))<div class="login-status" role="status"><i class="bi bi-check-circle-fill" aria-hidden="true"></i>{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert-error" role="alert">{{ $errors->first() }}</div>@endif
    <div class="field">
        <label for="login">Email address or admin name</label>
        <span class="login-field-control"><i class="bi bi-envelope" aria-hidden="true"></i><input class="input" id="login" name="login" type="text" required autofocus value="{{ old('login') }}" autocomplete="username" placeholder="you@example.com"></span>
    </div>
    <div class="field">
        <label for="password">Password</label>
        <span class="login-field-control"><i class="bi bi-lock" aria-hidden="true"></i><input class="input" id="password" name="password" type="password" required autocomplete="current-password" placeholder="Enter your password"></span>
    </div>
    <div class="login-actions">
        <button class="btn btn-primary" type="submit"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Sign in to dashboard</button>
        <a class="btn btn-secondary" href="{{ route('home') }}"><i class="bi bi-globe2" aria-hidden="true"></i> Visit website</a>
    </div>
    <div class="login-footnote"><i class="bi bi-shield-check" aria-hidden="true"></i> Sign in again if your session expires or is cleared.</div>
</form>
</body>
</html>
