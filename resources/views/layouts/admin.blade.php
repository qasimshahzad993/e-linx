<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') · e-Linx Admin</title>
    <meta name="theme-color" content="#0b1320">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v=5">
</head>
<body>
@php($adminName = session('admin_name', 'Admin'))
<div class="shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="{{ route('admin.dashboard') }}" aria-label="e-Linx admin dashboard"><img src="{{ asset('assets/img/elinxlogo.png') }}" alt="e-Linx Technology"></a>
        <div class="nav-group">Management</div>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i>Dashboard</a>
            <a href="{{ route('admin.leads.index') }}" class="nav-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}"><i class="bi bi-inbox"></i>Leads <span class="live-count" id="notification-count" aria-label="New leads">0</span></a>
            <a href="{{ route('admin.content.index', 'services') }}" class="nav-link {{ request()->is('admin/content/services*') ? 'active' : '' }}"><i class="bi bi-layers"></i>Services</a>
            <a href="{{ route('admin.courses.index') }}" class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}"><i class="bi bi-mortarboard"></i>Courses</a>
            <a href="{{ route('admin.content.index', 'products') }}" class="nav-link {{ request()->is('admin/content/products*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i>Products</a>
            <a href="{{ route('admin.content.index', 'portfolio') }}" class="nav-link {{ request()->is('admin/content/portfolio*') ? 'active' : '' }}"><i class="bi bi-briefcase"></i>Portfolio</a>
            <a href="{{ route('admin.trust.index', 'faqs') }}" class="nav-link {{ request()->is('admin/trust/faqs*') ? 'active' : '' }}"><i class="bi bi-question-circle"></i>FAQs</a>
            <a href="{{ route('admin.trust.index', 'testimonials') }}" class="nav-link {{ request()->is('admin/trust/testimonials*') ? 'active' : '' }}"><i class="bi bi-chat-quote"></i>Testimonials</a>
            <a href="{{ route('admin.blog.index') }}" class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}"><i class="bi bi-journal-richtext"></i>Blog</a>
            <a href="{{ route('admin.newsletter.index') }}" class="nav-link {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}"><i class="bi bi-envelope-paper"></i>Newsletter</a>
        </nav>
        <div class="sidebar-foot">
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="nav-link"><i class="bi bi-globe2"></i>View website</a>
            <form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit" class="nav-link"><i class="bi bi-box-arrow-left"></i>Log out</button></form>
        </div>
    </aside>
    <div class="scrim" id="scrim"></div>
    <div class="main">
        <header class="topbar">
            <button class="icon-btn menu-btn" id="menuBtn" aria-label="Open menu"><i class="bi bi-list"></i></button>
            <div class="crumbs"><span>Admin</span><i class="bi bi-chevron-right"></i><strong>@yield('title', 'Dashboard')</strong></div>
            <div class="profile" id="profile">
                <button class="profile-btn" id="profileBtn" aria-haspopup="true" aria-expanded="false"><span class="avatar">{{ collect(explode(' ', trim($adminName)))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('') }}</span><span class="who"><strong>{{ $adminName }}</strong><small>Administrator</small></span><i class="bi bi-chevron-down"></i></button>
                <div class="menu" id="profileMenu" role="menu"><div class="menu-head"><strong>{{ $adminName }}</strong><small>{{ session('admin_email') }}</small></div><a href="{{ route('admin.profile') }}" role="menuitem"><i class="bi bi-person"></i>My profile</a><a href="{{ route('admin.profile') }}#password" role="menuitem"><i class="bi bi-shield-lock"></i>Change password</a><form method="post" action="{{ route('admin.logout') }}">@csrf<button class="danger" type="submit" role="menuitem"><i class="bi bi-box-arrow-left"></i>Log out</button></form></div>
            </div>
        </header>
        <main class="content">
            @yield('content')
        </main>
        <footer class="foot">© {{ date('Y') }} e-Linx. All rights reserved.</footer>
    </div>
</div>
<div id="toast-fallback" class="toast-fallback" role="status" aria-live="polite" hidden></div>
<div class="modal" id="confirmModal" hidden><div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="confirmTitle"><div class="modal-icon"><i class="bi bi-exclamation-triangle"></i></div><h3 id="confirmTitle">Are you sure?</h3><p id="confirmText"></p><div class="modal-actions"><button type="button" class="btn btn-ghost" id="confirmNo">Cancel</button><button type="button" class="btn btn-danger" id="confirmYes">Delete</button></div></div></div>
@php($flashes = [])
@if(session('success')) @php($flashes[] = ['type' => 'success', 'msg' => session('success')]) @endif
@if($errors->any()) @php($flashes[] = ['type' => 'error', 'msg' => $errors->first()]) @endif
<script>window.__flashes = @json($flashes);window.__realtimeConfig = {endpoint: @json(route('admin.notifications')), latestId: @json((int) \App\Models\Lead::max('id')), newCount: @json((int) \App\Models\Lead::where('status', 'new')->count())};</script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0"></script>
<script src="{{ asset('assets/js/open-pages-in-new-tab.js') }}"></script>
<script src="{{ asset('assets/js/admin.js') }}?v=2"></script>
<script src="{{ asset('assets/js/admin-realtime.js') }}?v=2"></script>
</body>
</html>
