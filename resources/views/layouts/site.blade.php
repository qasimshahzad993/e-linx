<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'e-Linx') | e-Linx</title>
    <meta name="description" content="@yield('description', 'Explore e-Linx software products, web platforms, mobile apps, YouTube automation and digital engineering services.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="e-Linx">
    <meta property="og:title" content="@yield('title', 'e-Linx') | e-Linx">
    <meta property="og:description" content="@yield('description', 'Explore e-Linx software products, web platforms, mobile apps, YouTube automation and digital engineering services.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('assets/img/stock/insight.jpg'))">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'e-Linx') | e-Linx">
    <meta name="twitter:description" content="@yield('description', 'Explore e-Linx software products, web platforms, mobile apps, YouTube automation and digital engineering services.')">
    @yield('article_meta')
    <meta name="theme-color" content="#0b1320">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/hero-slider.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/site-polish.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/blog-portfolio.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/button-polish.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/brand-theme.css') }}" rel="stylesheet">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>
<nav class="navbar navbar-expand-xl main-nav sticky-top"><div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}" aria-label="e-Linx home"><img class="elinx-logo" src="{{ asset('assets/img/elinxlogo.png') }}" alt="e-Linx Technology logo"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#site-nav" aria-label="Toggle navigation"><i class="bi bi-list"></i></button>
    <div class="collapse navbar-collapse" id="site-nav">
        <ul class="navbar-nav mx-auto gap-lg-1">
            @foreach(\App\Models\Service::CATEGORIES as $category => $categorySlug)
                @include('partials.service-nav-dropdown', ['category' => $category, 'slug' => $categorySlug])
            @endforeach
            <li class="nav-item dropdown">
                <button class="nav-link dropdown-toggle {{ request()->routeIs('courses.*') ? 'active' : '' }}" id="nav-courses" type="button" data-bs-toggle="dropdown" aria-expanded="false">Courses</button>
                <ul class="dropdown-menu" aria-labelledby="nav-courses">
                    @forelse($navCourses as $navCourse)
                        <li><a class="dropdown-item" href="{{ route('courses.show', $navCourse) }}">{{ $navCourse->title }}</a></li>
                    @empty
                        <li><span class="dropdown-item-text">New course dates will be announced here.</span></li>
                    @endforelse
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item nav-category-all" href="{{ route('courses.index') }}">Browse all courses <i class="bi bi-arrow-right" aria-hidden="true"></i></a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <button class="nav-link dropdown-toggle {{ request()->routeIs('products', 'portfolio', 'portfolio.show', 'blog.*', 'faqs', 'about', 'contact') ? 'active' : '' }}" id="nav-more" type="button" data-bs-toggle="dropdown" aria-expanded="false">More</button>
                <ul class="dropdown-menu" aria-labelledby="nav-more">
                    <li><a class="dropdown-item" href="{{ route('products') }}">Products</a></li>
                    <li><a class="dropdown-item" href="{{ route('portfolio') }}">Portfolio</a></li>
                    <li><a class="dropdown-item" href="{{ route('blog.index') }}">Blog &amp; insights</a></li>
                    <li><a class="dropdown-item" href="{{ route('faqs') }}">FAQs</a></li>
                    <li><a class="dropdown-item" href="{{ route('about') }}">About e-Linx</a></li>
                    <li><a class="dropdown-item" href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </li>
        </ul>
        <a class="btn btn-primary rounded-pill px-4 nav-proposal-cta" href="{{ route('quote') }}">Request a Proposal <i class="bi bi-arrow-up-right"></i></a>
    </div>
</div></nav>
@if (session('success'))
    <div class="container mt-3"><div class="alert alert-success alert-dismissible fade show" role="status">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>
@endif
@if ($errors->any())
    <div class="container mt-3"><div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
@endif
@if(session('warning'))
    <div class="container mt-3"><div class="alert alert-warning" role="alert">{{ session('warning') }}</div></div>
@endif
<main id="main-content">
    @yield('content')
</main>
<footer class="footer site-footer">
    <div class="container">
        <div class="site-footer-main">
            <div class="site-footer-brand-column">
                <a class="brand-footer" href="{{ route('home') }}" aria-label="e-Linx home">
                    <img class="elinx-logo footer-logo" src="{{ asset('assets/img/elinxlogo.png') }}" alt="e-Linx Technology logo">
                </a>
                <p>Digital products, software, marketing, and practical training for growing businesses.</p>
                <a class="site-footer-email" href="mailto:{{ config('mail.notifications.to') }}"><i class="bi bi-envelope" aria-hidden="true"></i> {{ config('mail.notifications.to') }}</a>
                @php($socialLinks = [
                    ['label' => 'Facebook', 'url' => config('services.social.facebook'), 'icon' => 'bi-facebook'],
                    ['label' => 'Instagram', 'url' => config('services.social.instagram'), 'icon' => 'bi-instagram'],
                    ['label' => 'LinkedIn', 'url' => config('services.social.linkedin'), 'icon' => 'bi-linkedin'],
                    ['label' => 'YouTube', 'url' => config('services.social.youtube'), 'icon' => 'bi-youtube'],
                ])
                <nav class="site-footer-socials" aria-label="Social media links">
                    @foreach($socialLinks as $socialLink)
                        @if($socialLink['url'])
                            <a href="{{ $socialLink['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialLink['label'] }} (demo link; update before launch)" title="{{ $socialLink['label'] }} demo link">
                                <i class="bi {{ $socialLink['icon'] }}" aria-hidden="true"></i>
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>
            <nav class="site-footer-nav" aria-label="Footer navigation">
                <div class="site-footer-links">
                    <h2>Explore</h2>
                    <a href="{{ route('about') }}">About e-Linx</a>
                    <a href="{{ route('faqs') }}">FAQs</a>
                    <a href="{{ route('courses.index') }}">Courses &amp; training</a>
                    <a href="{{ route('portfolio') }}">Our work</a>
                    <a href="{{ route('blog.index') }}">Blog &amp; insights</a>
                    <a href="{{ route('contact') }}">Contact</a>
                </div>
                <div class="site-footer-links">
                    <h2>What we do</h2>
                    <a href="{{ route('services') }}">Web &amp; SaaS</a>
                    <a href="{{ route('services') }}#social-media-marketing">Social media marketing</a>
                    <a href="{{ route('services') }}#seo-digital-growth">SEO &amp; digital growth</a>
                    <a href="{{ route('services') }}#paid-advertising-ppc">Paid ads &amp; PPC</a>
                    <a href="{{ route('services') }}#mobile-app-development">Mobile apps</a>
                    <a href="{{ route('services') }}#youtube-automation">YouTube Automation</a>
                    <a href="{{ route('services') }}#ui-ux-product-design">Product design</a>
                </div>
                <div class="site-footer-contact">
                    <h2>Project enquiries</h2>
                    <p>Share your objectives and requirements with our team to discuss an appropriate next step.</p>
                    <a class="site-footer-cta" href="{{ route('quote') }}">Request a proposal <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                    <span class="site-footer-region"><i class="bi bi-geo-alt" aria-hidden="true"></i> Based in Pakistan · Serving clients worldwide</span>
                </div>
            </nav>
        </div>
        <section class="site-footer-newsletter" aria-labelledby="newsletter-heading">
            <div>
                <h2 id="newsletter-heading">Occasional e-Linx updates</h2>
                <p>Subscribe for practical notes on digital products, technology, and marketing. We’ll only add you to the list after you opt in.</p>
            </div>
            <form method="post" action="{{ route('newsletter.store') }}" class="site-newsletter-form">
                @csrf
                <div class="hp-field" aria-hidden="true"><label for="newsletter-website">Leave this field empty</label><input id="newsletter-website" name="website" tabindex="-1" autocomplete="off"></div>
                <label class="visually-hidden" for="newsletter-email">Your email address</label>
                <input id="newsletter-email" type="email" name="email" value="{{ old('email') }}" maxlength="190" autocomplete="email" placeholder="you@example.com" required>
                <label class="site-newsletter-consent"><input type="checkbox" name="consent" value="1" required @checked(old('consent'))><span>I agree to receive occasional e-Linx updates. Unsubscribe using the personal link in any update.</span></label>
                @error('email')<span class="site-newsletter-error">{{ $message }}</span>@enderror
                @error('consent')<span class="site-newsletter-error">Please confirm your consent to subscribe.</span>@enderror
                @error('website')<span class="site-newsletter-error">{{ $message }}</span>@enderror
                <button class="btn btn-primary rounded-pill px-4" type="submit">Subscribe <i class="bi bi-arrow-up-right" aria-hidden="true"></i></button>
            </form>
        </section>
        <div class="site-footer-bottom">
            <span>© {{ date('Y') }} e-Linx. All rights reserved.</span>
            <div class="site-footer-utility">
                <form method="post" action="{{ route('admin.access') }}">@csrf<button class="site-footer-admin" type="submit"><i class="bi bi-shield-lock" aria-hidden="true"></i> Admin</button></form>
                <a href="{{ route('contact') }}">Get in touch <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
</footer>
<div class="elinx-cursor" aria-hidden="true"><span></span></div>
@php($whatsAppNumber = preg_replace('/\D+/', '', (string) config('services.whatsapp.number')))
@if($whatsAppNumber)
    <a class="whatsapp-float" href="https://wa.me/{{ $whatsAppNumber }}" target="_blank" rel="noopener noreferrer" aria-label="Chat with e-Linx on WhatsApp" title="Chat with e-Linx on WhatsApp">
        <svg class="whatsapp-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path d="M20.52 3.48A11.84 11.84 0 0 0 12.1 0C5.54 0 .2 5.34.2 11.9c0 2.1.55 4.16 1.6 5.98L.1 24l6.28-1.64a11.9 11.9 0 0 0 5.7 1.45h.01c6.56 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.47-8.43ZM12.1 21.8a9.9 9.9 0 0 1-5.05-1.38l-.36-.21-3.73.98.99-3.64-.24-.37a9.87 9.87 0 1 1 8.39 4.62Zm5.42-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.23-.64.08-.3-.15-1.26-.47-2.4-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.61.13-.13.3-.34.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.91-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.06 2.88 1.21 3.08c.15.2 2.09 3.2 5.07 4.49.71.3 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.42-.07-.12-.27-.2-.57-.34Z"/>
        </svg>
    </a>
@endif
<section class="chatbot-widget" data-chatbot data-chatbot-configured="{{ filled(config('services.chatbot.api_key')) ? 'true' : 'false' }}" aria-label="e-Linx live assistant">
    <button class="chatbot-toggle" type="button" data-chatbot-toggle aria-expanded="false" aria-controls="chatbot-panel" aria-label="Open e-Linx live assistant" title="Chat with e-Linx">
        <i class="bi bi-chat-dots-fill" aria-hidden="true"></i>
    </button>
    <div class="chatbot-panel" id="chatbot-panel" data-chatbot-panel hidden>
        <header class="chatbot-header">
            <span class="chatbot-header-icon"><i class="bi bi-stars" aria-hidden="true"></i></span>
            <div><strong>e-Linx Assistant</strong><small>AI-powered website help</small></div>
            <button class="chatbot-close" type="button" data-chatbot-close aria-label="Close chat"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
        </header>
        <div class="chatbot-messages" data-chatbot-messages role="log" aria-live="polite" aria-relevant="additions text">
            <div class="chatbot-message chatbot-message-assistant">Hi! Ask me about e-Linx services, products, or courses.</div>
        </div>
        <p class="chatbot-status" data-chatbot-status role="status">Replies are AI-generated. Please confirm important details with our team.</p>
        <form class="chatbot-form" data-chatbot-form action="{{ route('chatbot.reply') }}" method="post">
            <label class="visually-hidden" for="chatbot-message">Your message</label>
            <textarea id="chatbot-message" class="chatbot-input" data-chatbot-input rows="1" maxlength="2000" placeholder="Type your question…" required></textarea>
            <button class="chatbot-send" data-chatbot-send type="submit" aria-label="Send message"><i class="bi bi-send-fill" aria-hidden="true"></i></button>
        </form>
        <p class="chatbot-privacy">Messages are processed by our AI provider and not saved by e-Linx. Avoid sending sensitive information.</p>
    </div>
</section>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>if (window.AOS) { AOS.init({once:true,duration:650}); }</script>
<script src="{{ asset('assets/js/open-pages-in-new-tab.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('assets/js/chatbot.js') }}"></script>
@stack('scripts')
</body>
</html>
