@extends('layouts.site')
@section('title', 'Digital Skills Courses')
@section('description', 'Explore e-Linx digital skills training and course outlines. Contact us to ask about course availability.')
@section('content')
@include('partials.site-hero', [
    'crumb' => 'Courses',
    'eyebrow' => 'DIGITAL SKILLS & TRAINING',
    'title' => 'Build practical',
    'highlight' => 'digital skills.',
    'description' => 'Explore our published course outlines and contact e-Linx about training options and availability.',
    'image' => 'assets/img/stock/product-design.jpg',
    'imageAlt' => 'A team learning and reviewing digital product design',
    'imageCaption' => 'Practical learning, shaped around digital work',
    'ctaText' => 'Ask about training',
    'ctaUrl' => route('contact', ['subject' => 'Training enquiry']),
])
<section class="section section-soft"><div class="container">
    <div class="courses-heading"><div><span class="text-primary fw-bold small">COURSE CATALOG</span><h2 class="section-title mt-2">Explore the course outlines.</h2><p class="section-lead mb-0">Schedules, delivery options, and prices are shown only when confirmed.</p></div></div>
    @if($courses->isNotEmpty())
        <div class="row g-4">
            @foreach($courses as $course)
                <div class="col-md-6 col-xl-4"><a class="home-product-card course-card" href="{{ route('courses.show', $course) }}"><span class="home-product-icon"><i class="{{ $course->icon ?: 'bi bi-mortarboard' }}" aria-hidden="true"></i></span>@if($course->category)<span class="small text-primary fw-bold">{{ $course->category }}</span>@endif<h3>{{ $course->title }}</h3><p>{{ $course->summary }}</p><span class="course-meta">@if($course->duration)<span><i class="bi bi-clock" aria-hidden="true"></i>{{ $course->duration }}</span>@endif @if($course->delivery_mode)<span><i class="bi bi-laptop" aria-hidden="true"></i>{{ $course->delivery_mode }}</span>@endif @if($course->price)<span><i class="bi bi-tag" aria-hidden="true"></i>{{ $course->price }}</span>@endif</span><span class="home-product-link">View course outline <i class="bi bi-arrow-up-right"></i></span></a></div>
            @endforeach
        </div>
    @else
        <div class="product-empty"><span><i class="bi bi-mortarboard" aria-hidden="true"></i></span><h2>Training details are being prepared.</h2><p>Contact our team to ask about future training opportunities.</p><a class="btn btn-primary rounded-pill px-4" href="{{ route('contact', ['subject' => 'Training enquiry']) }}">Ask about training <i class="bi bi-arrow-up-right"></i></a></div>
    @endif
</div></section>
@endsection
