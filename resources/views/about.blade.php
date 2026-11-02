@extends('layouts.site')
@section('title', 'About e-Linx')
@section('description', 'Discover how e-Linx combines product strategy, thoughtful design, and dependable engineering to create digital solutions for growing businesses.')
@section('content')
@include('partials.site-hero', [
    'crumb' => 'About',
    'eyebrow' => 'ABOUT E-LINX',
    'title' => 'Your technology',
    'highlight' => 'partner for what’s next.',
    'description' => 'We partner with businesses to plan, design, and build dependable digital products that address real needs and support long-term growth.',
    'image' => 'assets/img/stock/product-design.jpg',
    'imageAlt' => 'A product design team reviewing interface layouts on a large display',
    'imageCaption' => 'A considered approach to every digital product',
    'ctaText' => 'Explore our services',
    'ctaUrl' => route('services'),
])
<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="text-primary fw-bold small">WHO WE ARE</span>
                <h2 class="section-title mt-2">Technology shaped around people and business goals.</h2>
            </div>
            <div class="col-lg-6">
                <p class="section-lead">Our work brings product strategy, user experience, engineering, and delivery together. A focused team and clear ownership help create solutions that are practical today and ready to evolve over time.</p>
                <div class="row g-3 mt-3">
                    <div class="col-6"><div class="cardx"><strong>01</strong><h3 class="mt-2 h5">Clarity</h3><p class="small text-secondary mb-0">We align on the problem, priorities, and measures of success before selecting an approach.</p></div></div>
                    <div class="col-6"><div class="cardx"><strong>02</strong><h3 class="mt-2 h5">Craft</h3><p class="small text-secondary mb-0">We value considered design, reliable engineering, and quality throughout delivery.</p></div></div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section section-soft">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4"><div class="cardx"><div class="icon-box"><i class="bi bi-bullseye" aria-hidden="true"></i></div><h3 class="mt-4 h4">Our mission</h3><p class="text-secondary mb-0">Make high-quality digital solutions accessible to businesses ready to improve how they work and serve their customers.</p></div></div>
            <div class="col-md-4"><div class="cardx"><div class="icon-box"><i class="bi bi-eye" aria-hidden="true"></i></div><h3 class="mt-4 h4">Our vision</h3><p class="text-secondary mb-0">Be a trusted technology partner, helping ambitious teams turn meaningful ideas into lasting digital products.</p></div></div>
            <div class="col-md-4"><div class="cardx"><div class="icon-box"><i class="bi bi-shield-check" aria-hidden="true"></i></div><h3 class="mt-4 h4">Our values</h3><p class="text-secondary mb-0">We work with accountability, communicate with honesty, and make quality, security, and measurable progress priorities.</p></div></div>
        </div>
    </div>
</section>
@endsection
