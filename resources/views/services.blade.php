@extends('layouts.site')
@section('title', 'Services')
@section('description', 'Explore e-Linx services in web and software engineering, mobile apps, product design, YouTube automation, and digital growth.')
@section('content')
@include('partials.site-hero', [
    'crumb' => 'Services',
    'eyebrow' => 'WHAT WE DELIVER',
    'title' => 'Expertise to move',
    'highlight' => 'your business forward.',
    'description' => 'From software engineering and product design to search optimization, paid campaigns, social media, and content, explore digital services aligned with your business priorities.',
    'image' => 'assets/img/services/youtube-automation.jpg',
    'imageAlt' => 'Video editing timeline open on a desktop monitor',
    'imageCaption' => 'From a clear plan to reliable delivery',
    'ctaText' => 'Discuss your project',
    'ctaUrl' => route('quote'),
])
@forelse($serviceGroups as $category => $services)
    @php($categorySlug = \App\Models\Service::CATEGORIES[$category] ?? \Illuminate\Support\Str::slug($category))
    <section class="section section-soft service-category-section" id="{{ $categorySlug }}">
        <div class="container">
            <div class="row align-items-end g-3 mb-5">
                <div class="col-lg-8">
                    <span class="text-primary fw-bold small">OUR CAPABILITIES</span>
                    <h2 class="section-title mt-2">{{ $category }}</h2>
                    <p class="section-lead mb-0">Review the available services and request a proposal for a scope tailored to your requirements.</p>
                </div>
            </div>
            <div class="row g-4">
                @foreach($services as $i => $service)
                    <div class="col-md-6 col-xl-4" id="{{ $service->slug }}" data-aos="fade-up">
                        @include('partials.service-card', ['service' => $service, 'i' => $i])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@empty
    <section class="section section-soft"><div class="container"><div class="product-empty"><span><i class="bi bi-window-stack"></i></span><h2>Our service portfolio is being updated.</h2><p>Contact our team to discuss your requirements and identify a suitable next step.</p><a class="btn btn-primary rounded-pill px-4" href="{{ route('contact') }}">Contact our team <i class="bi bi-arrow-up-right"></i></a></div></div></section>
@endforelse
@endsection
