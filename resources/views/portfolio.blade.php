@extends('layouts.site')
@section('title', 'Portfolio')
@section('description', 'Explore selected e-Linx projects across digital products, business platforms, and customer experiences.')
@section('content')
@include('partials.site-hero', [
    'crumb' => 'Portfolio',
    'eyebrow' => 'SELECTED WORK',
    'title' => 'Digital solutions.',
    'highlight' => 'Built around real needs.',
    'description' => 'Explore selected projects across digital products, business platforms, and customer experiences—each shaped by its users and objectives.',
    'image' => 'assets/img/stock/insight.jpg',
    'imageAlt' => 'Laptop showing business analytics and website performance charts',
    'imageCaption' => 'Strong delivery starts with clear objectives',
    'ctaText' => 'Start a project',
    'ctaUrl' => route('quote'),
])
<section class="section portfolio-listing">
    <div class="container">
        @if($projects->isNotEmpty())
            <div class="portfolio-intro"><span class="blog-section-label">SELECTED PROJECTS</span><h2>Solutions shaped by clear objectives.</h2><p>We begin by understanding the people, requirements, and outcomes behind each engagement. Explore a selection of our digital work.</p></div>
            <div class="row g-4">
                @foreach($projects as $project)
                    @php($projectUrl = $project->externalUrl())
                    <div class="col-md-6 col-xl-4">
                        <article class="work-card portfolio-card">
                            <a class="work-card-image" href="{{ $projectUrl ?: route('quote') }}" @if($projectUrl) target="_blank" rel="noopener noreferrer" @endif aria-label="{{ $projectUrl ? 'Visit ' : 'Discuss a project like ' }}{{ $project->title }}">
                                <img src="{{ asset($project->artworkPath()) }}" alt="" loading="lazy">
                                <span class="work-card-image-icon"><i class="{{ $project->icon ?: 'bi bi-window-stack' }}" aria-hidden="true"></i></span>
                                <span class="work-card-open"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
                            </a>
                            <div class="work-card-content">
                                @if($project->category)<span class="work-card-category">{{ $project->category }}</span>@endif
                                <h3>{{ $project->title }}</h3>
                                <p>{{ $project->description }}</p>
                                <a class="work-card-link" href="{{ route('portfolio.show', $project) }}">Read project story <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                                @if($projectUrl)<a class="work-card-link" href="{{ $projectUrl }}" target="_blank" rel="noopener noreferrer">Visit project <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>@else<a class="work-card-link" href="{{ route('quote') }}">Discuss a similar project <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>@endif
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @else
            <div class="blog-empty"><span><i class="bi bi-briefcase" aria-hidden="true"></i></span><h2>Our project portfolio is being updated.</h2><p>Contact our team to discuss your objectives and how we can help deliver your next digital project.</p><a class="btn btn-primary rounded-pill px-4" href="{{ route('quote') }}">Discuss your project <i class="bi bi-arrow-up-right"></i></a></div>
        @endif
    </div>
</section>
@endsection
