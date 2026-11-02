@extends('layouts.site')
@section('title', 'Digital Products, Web Development & Marketing')
@section('description', 'e-Linx builds websites, mobile applications, and custom software, and offers search, social, campaign, and digital skills services for growing businesses.')
@section('content')
@php($homeWhatsAppNumber = preg_replace('/\D+/', '', (string) config('services.whatsapp.number')))
@include('partials.site-hero', [
    'eyebrow' => 'SOFTWARE, DIGITAL GROWTH & TRAINING',
    'title' => 'Empowering digital',
    'highlight' => 'growth for your business.',
    'rotatePhrases' => [
        'growth for your business.',
        'digital experiences that convert.',
        'software built to scale.',
        'ideas into lasting products.',
    ],
    'description' => 'From websites, mobile apps, and business software to search, social media, creative campaigns, and practical digital training, e-Linx helps move your digital work forward.',
    'image' => 'assets/img/stock/web-platforms.jpg',
    'imageAlt' => 'A laptop showing web development work in a software studio',
    'imageCaption' => 'Digital platforms designed around real business needs',
    'ctaText' => 'Explore our services',
    'ctaUrl' => '#what-we-do',
    'secondaryText' => $homeWhatsAppNumber ? 'Chat with us on WhatsApp' : 'Start a project',
    'secondaryUrl' => $homeWhatsAppNumber ? 'https://wa.me/'.$homeWhatsAppNumber : route('quote'),
    'secondaryExternal' => (bool) $homeWhatsAppNumber,
    'points' => ['Websites & custom software', 'Mobile apps & product design', 'SEO, social & paid campaigns', 'YouTube automation & training'],
])
<section class="home-services section section-soft" id="what-we-do">
    <div class="container">
        <div class="row align-items-end g-3 mb-5">
            <div class="col-lg-7">
                <span class="text-primary fw-bold small">WHAT WE DELIVER</span>
                <h2 class="section-title mt-2">Digital solutions, delivered end to end.</h2>
                <p class="section-lead mb-0">From initial planning through launch, our team brings product design and engineering together to deliver against your business goals.</p>
            </div>
            <div class="col-lg-5 text-lg-end mt-2 mt-lg-0">
                <a class="btn btn-primary rounded-pill px-4" href="{{ route('quote') }}">Discuss your project <i class="bi bi-arrow-up-right"></i></a>
            </div>
        </div>
        @if($services->isNotEmpty())
            <div class="row g-4">
                @foreach($services as $i => $service)
                    <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 70 }}">
                        @include('partials.service-card', ['service' => $service, 'i' => $i])
                    </div>
                @endforeach
            </div>
        @else
            <div class="product-empty"><span><i class="bi bi-window-stack"></i></span><h2>Our service portfolio is being updated.</h2><p>Contact our team to discuss your requirements and identify a suitable next step.</p><a class="btn btn-primary rounded-pill px-4" href="{{ route('contact') }}">Contact our team <i class="bi bi-arrow-up-right"></i></a></div>
        @endif
    </div>
</section>
<section class="tech-stack-section" aria-labelledby="tech-stack-title">
    <div class="container">
        <div class="tech-stack-panel">
            <div class="tech-stack-intro">
                <span class="tech-stack-kicker">OUR TOOLKIT</span>
                <h2 id="tech-stack-title">The right tools for reliable digital products.</h2>
                <p>We select proven technologies to support dependable product development, effective data management, and automated video publishing workflows.</p>
            </div>
            <ul class="tech-stack-list" aria-label="Technologies used by e-Linx">
                <li><i class="devicon-laravel-original" aria-hidden="true"></i><span>Laravel</span></li>
                <li><i class="devicon-php-plain" aria-hidden="true"></i><span>PHP</span></li>
                <li><i class="devicon-javascript-plain" aria-hidden="true"></i><span>JavaScript</span></li>
                <li><i class="devicon-react-original" aria-hidden="true"></i><span>React</span></li>
                <li><i class="devicon-nodejs-plain" aria-hidden="true"></i><span>Node.js</span></li>
                <li><i class="devicon-mysql-plain" aria-hidden="true"></i><span>MySQL</span></li>
                <li><i class="devicon-docker-plain" aria-hidden="true"></i><span>Docker</span></li>
                <li><i class="devicon-amazonwebservices-plain-wordmark" aria-hidden="true"></i><span>AWS</span></li>
            </ul>
        </div>
    </div>
</section>
@if($courses->isNotEmpty())
<section class="section section-soft course-home-section">
    <div class="container">
        <div class="courses-heading"><div><span class="text-primary fw-bold small">LEARN &amp; GROW</span><h2 class="section-title mt-2">Practical digital skills training.</h2><p class="section-lead mb-0">Explore published course outlines and enquire about availability.</p></div><a class="btn btn-soft rounded-pill" href="{{ route('courses.index') }}">All courses <i class="bi bi-arrow-right"></i></a></div>
        <div class="row g-4">
            @foreach($courses as $course)
                <div class="col-md-6 col-xl-4"><a class="home-product-card course-card" href="{{ route('courses.show', $course) }}"><span class="home-product-icon"><i class="{{ $course->icon ?: 'bi bi-mortarboard' }}" aria-hidden="true"></i></span>@if($course->category)<span class="small text-primary fw-bold">{{ $course->category }}</span>@endif<h3>{{ $course->title }}</h3><p>{{ $course->summary }}</p><span class="home-product-link">Explore course <i class="bi bi-arrow-up-right"></i></span></a></div>
            @endforeach
        </div>
    </div>
</section>
@endif
<section class="section"><div class="container"><div class="row align-items-end mb-5"><div class="col-lg-8"><span class="text-primary fw-bold small">PRODUCT SUITE</span><h2 class="section-title mt-2">Purpose-built tools for growing businesses.</h2><p class="section-lead mb-0">Explore products that support customer relationships, commerce, team operations, and informed decision-making.</p></div><div class="col-lg-4 text-lg-end mt-3 mt-lg-0"><a class="btn btn-soft rounded-pill" href="{{ route('products') }}">Explore all products <i class="bi bi-arrow-right"></i></a></div></div><div class="row g-4">@forelse($products as $product)<div class="col-md-6 col-lg-4"><a class="home-product-card" href="{{ route('quote', ['product' => $product->name]) }}"><span class="home-product-icon"><i class="{{ $product->icon ?: 'bi bi-box-seam' }}"></i></span>@if($product->category)<span class="small text-primary fw-bold">{{ $product->category }}</span>@endif<h3>{{ $product->name }}</h3><p>{{ $product->description ?: 'A flexible software platform designed around your team’s requirements.' }}</p><span class="home-product-link">Enquire about this product <i class="bi bi-arrow-up-right"></i></span></a></div>@empty<div class="col-12"><div class="product-empty"><p class="mb-3">Our product catalogue is being updated. Contact us to discuss your requirements.</p><a href="{{ route('contact') }}">Contact our team <i class="bi bi-arrow-up-right"></i></a></div></div>@endforelse</div></div></section>
@if($projects->isNotEmpty())
<section class="section section-soft">
    <div class="container">
        <div class="row align-items-end mb-5"><div class="col"><span class="text-primary fw-bold small">SELECTED WORK</span><h2 class="section-title mt-2">Selected projects.</h2><p class="section-lead mb-0">Explore digital products and experiences designed to address practical business requirements.</p></div><div class="col-auto"><a class="btn btn-soft rounded-pill" href="{{ route('portfolio') }}">View all work <i class="bi bi-arrow-right"></i></a></div></div>
        <div class="row g-4">
            @foreach($projects as $project)
                @php($projectUrl = $project->externalUrl())
                <div class="col-md-6 col-xl-4">
                    <article class="work-card">
                        <a class="work-card-image" href="{{ $projectUrl ?: route('portfolio') }}" @if($projectUrl) target="_blank" rel="noopener noreferrer" @endif aria-label="View {{ $project->title }}">
                            <img src="{{ asset($project->artworkPath()) }}" alt="" loading="lazy">
                            <span class="work-card-image-icon"><i class="{{ $project->icon ?: 'bi bi-grid-3x3-gap' }}" aria-hidden="true"></i></span>
                            <span class="work-card-open"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
                        </a>
                        <div class="work-card-content">
                            @if($project->category)<span class="work-card-category">{{ $project->category }}</span>@endif
                            <h3>{{ $project->title }}</h3>
                            <p>{{ $project->description }}</p>
                            <a class="work-card-link" href="{{ route('portfolio.show', $project) }}">Read project story <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                            @if($projectUrl)<a class="work-card-link" href="{{ $projectUrl }}" target="_blank" rel="noopener noreferrer">Visit project <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>@endif
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@if($latestPosts->isNotEmpty())
<section class="section blog-home-section">
    <div class="container">
        <div class="blog-listing-head"><div><span class="blog-section-label">FROM THE JOURNAL</span><h2>Ideas worth sharing.</h2><p>Perspectives on technology, product design, and building digital solutions for business.</p></div><a class="blog-all-link" href="{{ route('blog.index') }}">Explore the journal <i class="bi bi-arrow-right"></i></a></div>
        <div class="blog-grid">@foreach($latestPosts as $post)@include('blog.partials.card', ['post' => $post])@endforeach</div>
    </div>
</section>
@endif
@if($testimonials->isNotEmpty())
<section class="section testimonial-section">
    <div class="container">
        <div class="testimonial-section-heading"><div><span class="text-primary fw-bold small">CLIENT FEEDBACK</span><h2 class="section-title mt-2">Experiences shared with permission.</h2><p class="section-lead mb-0">Feedback from clients about working with e-Linx.</p></div></div>
        <div class="row g-4">
            @foreach($testimonials as $testimonial)
                <div class="col-md-6 col-xl-4">
                    <article class="testimonial-card">
                        <i class="bi bi-quote testimonial-quote-icon" aria-hidden="true"></i>
                        <blockquote>{{ $testimonial->quote }}</blockquote>
                        <div class="testimonial-author">
                            <span class="testimonial-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($testimonial->client_name, 0, 1)) }}</span>
                            <div><strong>{{ $testimonial->client_name }}</strong>@if($testimonial->client_role || $testimonial->company_name)<span>{{ collect([$testimonial->client_role, $testimonial->company_name])->filter()->join(' · ') }}</span>@endif</div>
                        </div>
                        @if($testimonial->service)<span class="testimonial-service">{{ $testimonial->service }}</span>@endif
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@include('partials.faq-section', ['faqs' => $faqs])
<section class="section section-dark"><div class="container"><div class="row align-items-center g-5"><div class="col-lg-7"><span class="eyebrow">START A CONVERSATION</span><h2 class="section-title mt-3">Let’s discuss your next project.</h2><p class="text-white-50">Share your goals, requirements, or early-stage idea. Our team will help you identify a clear and practical way forward.</p></div><div class="col-lg-5 text-lg-end"><a class="btn btn-primary btn-lg rounded-pill px-4" href="{{ route('quote') }}">Request a proposal <i class="bi bi-arrow-up-right"></i></a></div></div></div></section>
@endsection
